<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionApiService
{
    protected ?string $apiUrl;
    protected ?string $apiKey;
    protected ?string $instanceName;

    public function __construct()
    {
        $profile = CompanyProfile::query()->first();

        $this->apiUrl = $profile?->evolution_api_url;
        $this->apiKey = $profile?->evolution_api_key;
        $this->instanceName = $profile?->evolution_instance_name;
    }

    /**
     * Check if Evolution API is configured and available.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->apiUrl) && ! empty($this->apiKey) && ! empty($this->instanceName);
    }

    /**
     * Send a WhatsApp message using Evolution API.
     */
    public function sendMessage(string $number, string $message): bool
    {
        if (! $this->isConfigured()) {
            Log::warning('Evolution API is not configured. Cannot send WhatsApp message.');
            return false;
        }

        try {
            $normalizedNumber = \App\Support\WhatsApp::number($number);

            if ($normalizedNumber === null) {
                Log::warning("Invalid WhatsApp number: {$number}");
                return false;
            }

            $url = rtrim($this->apiUrl, '/').'/message/sendText/'.$this->instanceName;

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'number' => $normalizedNumber,
                'text' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp message sent successfully to {$normalizedNumber}");
                return true;
            }

            Log::error("Failed to send WhatsApp message to {$normalizedNumber}: ".$response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Error sending WhatsApp message: ".$e->getMessage());
            return false;
        }
    }

    /**
     * Send a WhatsApp message to the sales manager.
     */
    public function sendToSalesManager(string $message): bool
    {
        $profile = CompanyProfile::query()->first();

        if ($profile === null || empty($profile->sales_manager_whatsapp)) {
            Log::warning('Sales manager WhatsApp number is not configured.');
            return false;
        }

        return $this->sendMessage($profile->sales_manager_whatsapp, $message);
    }

    /**
     * Check if the Evolution API instance is connected.
     */
    public function checkConnection(): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        try {
            $url = rtrim($this->apiUrl, '/').'/instance/connectionState/'.$this->instanceName;

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->get($url);

            return $response->successful() && $response->json('state') === 'open';
        } catch (\Exception $e) {
            Log::error("Error checking Evolution API connection: ".$e->getMessage());
            return false;
        }
    }

    /**
     * Send a media file (document) via the WhatsApp instance.
     * $mediaBase64 is the raw base64 payload of the file.
     */
    public function sendMedia(string $number, string $mediaBase64, string $fileName, ?string $caption = null): bool
    {
        if (! $this->isConfigured()) {
            Log::warning('Evolution API is not configured. Cannot send media.');
            return false;
        }

        try {
            $normalizedNumber = \App\Support\WhatsApp::number($number);

            if ($normalizedNumber === null) {
                Log::warning("Invalid WhatsApp number: {$number}");
                return false;
            }

            $url = rtrim($this->apiUrl, '/').'/message/sendMedia/'.$this->instanceName;

            $payload = [
                'number' => $normalizedNumber,
                'mediatype' => 'document',
                'media' => $mediaBase64,
                'fileName' => $fileName,
            ];

            if (! empty($caption)) {
                $payload['caption'] = $caption;
            }

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                Log::info("WhatsApp media sent successfully to {$normalizedNumber}: {$fileName}");
                return true;
            }

            Log::error("Failed to send WhatsApp media to {$normalizedNumber}: ".$response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Error sending WhatsApp media: ".$e->getMessage());
            return false;
        }
    }

    /**
     * Set the webhook URL on the Evolution instance so incoming messages
     * are pushed to this application (messages.upsert events).
     */
    public function setWebhook(string $webhookUrl): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        try {
            $url = rtrim($this->apiUrl, '/').'/webhook/set/'.$this->instanceName;

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'webhook' => [
                    'url' => $webhookUrl,
                    'byEvents' => false,
                    'events' => ['MESSAGES_UPSERT'],
                    'enabled' => true,
                ],
            ]);

            if ($response->successful()) {
                Log::info('Evolution API webhook set successfully.');
                return true;
            }

            Log::error('Failed to set Evolution API webhook: '.$response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('Error setting Evolution API webhook: '.$e->getMessage());
            return false;
        }
    }

    /**
     * List recent chats from the instance (used by the polling fallback).
     */
    public function getChats(int $limit = 30): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $url = rtrim($this->apiUrl, '/').'/chat/findChats/'.$this->instanceName;

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->get($url, ['limit' => $limit]);

            if ($response->successful()) {
                return $response->json() ?: [];
            }

            Log::warning('Failed to fetch chats: '.$response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('Error fetching chats: '.$e->getMessage());
            return [];
        }
    }

    /**
     * Fetch recent messages of a specific chat (used by the polling fallback).
     */
    public function getChatMessages(string $chatId, int $limit = 20): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $url = rtrim($this->apiUrl, '/').'/chat/findMessages/'.$this->instanceName;

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->get($url, [
                'chatId' => $chatId,
                'limit' => $limit,
            ]);

            if ($response->successful()) {
                return $response->json() ?: [];
            }

            Log::warning("Failed to fetch messages for {$chatId}: ".$response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("Error fetching messages for {$chatId}: ".$e->getMessage());
            return [];
        }
    }

    /**
     * Normalize an Evolution remoteJid ("2010xxx@s.whatsapp.net") into a plain phone.
     */
    public static function jidToPhone(string $jid): string
    {
        return preg_replace('/@.*$/', '', $jid) ?? $jid;
    }

    /**
     * Build the remoteJid ("phone@s.whatsapp.net") from a plain phone.
     */
    public static function phoneToJid(string $phone): string
    {
        return trim($phone).'@s.whatsapp.net';
    }
}
