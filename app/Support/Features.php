<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\CompanyProfile;

class Features
{
    /**
     * The global list of finishing & additional services features.
     * Managed from Settings -> Company Info -> Available Features,
     * falls back to the built-in defaults.
     *
     * @return array<int, array{icon: string, title: string, desc: string}>
     */
    public static function list(): array
    {
        $defaults = [
            ['icon' => 'sparkles', 'title' => __('Super lux finishing'), 'desc' => __('Premium finishing materials and fixtures to the highest international standards.')],
            ['icon' => 'car', 'title' => __('Private covered garage'), 'desc' => __('Dedicated parking space in the basement with electric charging capability.')],
            ['icon' => 'view', 'title' => __('Main promenade view'), 'desc' => __('Direct view of the promenade, green fence, and landscaped gardens.')],
            ['icon' => 'ac', 'title' => __('Central AC ready'), 'desc' => __('Fully prepared for concealed and central air conditioning connections.')],
            ['icon' => 'security', 'title' => __('24/7 security'), 'desc' => __('Complete security system with surveillance cameras and electronic gates.')],
            ['icon' => 'elevator', 'title' => __('Luxury electronic elevators'), 'desc' => __('Italian Schindler high-speed elevators with wide capacity.')],
        ];

        $stored = CompanyProfile::query()->value('available_features');

        return is_array($stored) && $stored !== [] ? $stored : $defaults;
    }

    /**
     * Renders a stroke-based SVG icon (24x24) matching the app's Heroicons
     * style for a given icon key. Unknown keys fall back to a sparkles icon.
     */
    public static function iconSvg(string $icon): string
    {
        $paths = [
            'sparkles' => '<path d="M12 3l1.9 5.8a2 2 0 0 0 1.3 1.3L21 12l-5.8 1.9a2 2 0 0 0-1.3 1.3L12 21l-1.9-5.8a2 2 0 0 0-1.3-1.3L3 12l5.8-1.9a2 2 0 0 0 1.3-1.3L12 3z"/><path d="M18 3v4M16 5h4"/>',
            'car' => '<path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/>',
            'view' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 15l4.5-4.5 4 4 3.5-3.5L21 15"/><circle cx="16" cy="8" r="1.5"/>',
            'ac' => '<path d="M12 2v20M2 12h20M4.9 4.9l14.2 14.2M19.1 4.9 4.9 19.1"/><path d="M12 6.5 9.5 5M12 6.5 14.5 5M12 17.5 9.5 19M12 17.5 14.5 19M6.5 12 5 9.5M6.5 12 5 14.5M17.5 12 19 9.5M17.5 12 19 14.5"/>',
            'security' => '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/><path d="m9 12 2 2 4-4"/>',
            'elevator' => '<rect x="7" y="3" width="10" height="18" rx="2"/><path d="m10 8 2-2 2 2M10 16l2 2 2-2"/>',
        ];

        $body = $paths[$icon] ?? $paths['sparkles'];

        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">' . $body . '</svg>';
    }
}
