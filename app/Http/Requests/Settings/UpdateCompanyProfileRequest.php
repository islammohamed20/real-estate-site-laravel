<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'logo_light' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'logo_dark' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'stamp' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,ico', 'max:1024'],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'logo_light_path' => ['nullable', 'string', 'max:255'],
            'logo_dark_path' => ['nullable', 'string', 'max:255'],
            'logo_height_desktop' => ['nullable', 'integer', 'min:16', 'max:200'],
            'logo_height_mobile' => ['nullable', 'integer', 'min:16', 'max:200'],
            'stamp_path' => ['nullable', 'string', 'max:255'],
            'favicon_path' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'google_maps_api_key' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:120'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'currency_code' => ['nullable', 'string', 'max:10'],
            'default_language' => ['nullable', 'string', 'max:10'],
            'available_features' => ['nullable', 'array'],
            'available_features.*.icon' => ['nullable', 'string', 'max:20'],
            'available_features.*.title' => ['nullable', 'string', 'max:255'],
            'available_features.*.desc' => ['nullable', 'string', 'max:1000'],
            'maintenance_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'trash_retention_days' => ['required', 'integer', 'min:1', 'max:365'],
            'auto_purge_enabled' => ['nullable', 'boolean'],
            'smtp_from_name' => ['nullable', 'string', 'max:255'],
            'smtp_from_email' => ['nullable', 'email', 'string', 'max:255'],
            'evolution_dashboard_url' => ['nullable', 'url', 'max:255'],
            'evolution_outgoing_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'evolution_incoming_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'evolution_chat_background' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }
}
