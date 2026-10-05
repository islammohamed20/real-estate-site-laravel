<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

/**
 * Centralized mPDF renderer — single source of truth for PDF configuration,
 * font setup, image embedding, and error handling.
 *
 * Used by both the calculator (live PDF) and PlanPdfService (saved-plan PDF)
 * to guarantee identical rendering settings and avoid configuration drift.
 */
class PdfRendererService
{
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Render an HTML string to PDF bytes.
     *
     * @return string  Raw PDF content (binary).
     *
     * @throws MpdfException  If PDF generation fails (font missing, memory, etc.).
     */
    public function renderHtml(string $html): string
    {
        $this->verifyFonts();

        $defaultConfig = (new ConfigVariables)->getDefaults();
        $defaultFontConfig = (new FontVariables)->getDefaults();

        $isArabic = app()->getLocale() === 'ar';
        $tempDir = storage_path('mpdf-temp');

        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'tempDir' => $tempDir,
            'fontDir' => array_merge($defaultConfig['fontDir'], [public_path('fonts')]),
            'fontdata' => $defaultFontConfig['fontdata'] + [
                'tajawal' => [
                    'R' => 'Tajawal-Regular.ttf',
                    'B' => 'Tajawal-Bold.ttf',
                ],
            ],
            'default_font' => 'tajawal',
            'default_font_size' => 12,
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $mpdf->SetDirectionality($isArabic ? 'rtl' : 'ltr');
        $mpdf->SetFooter('{PAGENO} / {nb}');
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', 'S');
    }

    /**
     * Render an HTML string to PDF, returning a Response ready to send.
     *
     * @param  string  $filename  Download filename (ASCII-safe).
     * @param  bool    $inline    true → browser preview; false → forced download.
     */
    public function renderResponse(string $html, string $filename, bool $inline = false): \Illuminate\Http\Response
    {
        try {
            $content = $this->renderHtml($html);
        } catch (MpdfException $e) {
            Log::error('PDF generation failed: '.$e->getMessage(), [
                'filename' => $filename,
                'exception' => $e,
            ]);

            abort(500, __('Failed to generate the PDF. Please try again or contact support.'));
        }

        $disposition = $inline ? 'inline' : 'attachment';
        $encodedFilename = rawurlencode($filename);

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition."; filename=\"".$filename."\"; filename*=UTF-8''".$encodedFilename,
            'Content-Length' => strlen($content),
        ]);
    }

    /**
     * Convert a stored asset URL to a base64 data URI, cached for 1 hour.
     * Only image/* MIME types are accepted; everything else returns null.
     */
    public function imageDataUri(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        return Cache::remember('pdf-image:'.md5($url), self::CACHE_TTL, function () use ($url): ?string {
            $path = parse_url($url, PHP_URL_PATH);
            $file = $path ? public_path(ltrim($path, '/')) : null;

            if ($file === null || ! is_file($file) || ! is_readable($file)) {
                return null;
            }

            $mime = function_exists('mime_content_type') ? mime_content_type($file) : false;

            // Reject non-image files to prevent embedding arbitrary content.
            if (! $mime || ! str_starts_with($mime, 'image/')) {
                Log::warning('PDF image skipped — not an image', ['url' => $url, 'mime' => $mime ?: 'unknown']);

                return null;
            }

            return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($file));
        });
    }

    /**
     * Verify that the Tajawal font files exist before attempting to render.
     * Logs an error if missing — mPDF would silently fall back to a non-Arabic font.
     */
    private function verifyFonts(): void
    {
        $fonts = [
            'Tajawal-Regular.ttf' => public_path('fonts/Tajawal-Regular.ttf'),
            'Tajawal-Bold.ttf' => public_path('fonts/Tajawal-Bold.ttf'),
        ];

        foreach ($fonts as $name => $path) {
            if (! is_file($path)) {
                Log::error('PDF font missing', ['font' => $name, 'path' => $path]);
            }
        }
    }
}
