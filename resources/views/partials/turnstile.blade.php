@if (config('services.turnstile.enabled') && config('services.turnstile.site_key'))
    <div class="space-y-2">
        <div
            class="cf-turnstile"
            data-sitekey="{{ config('services.turnstile.site_key') }}"
            data-theme="dark"
            data-response-field-name="turnstile"
        ></div>
        @error('turnstile')
            <span class="block text-sm text-rose-400">{{ $message }}</span>
        @enderror
    </div>
@endif
