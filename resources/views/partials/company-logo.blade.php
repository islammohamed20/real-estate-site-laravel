@php
    $hasLogos = $companyProfile?->logo_light_path || $companyProfile?->logo_dark_path || $companyProfile?->logo_path;
    $size = $size ?? 'desktop';
    $height = $size === 'mobile'
        ? (int) ($companyProfile?->logo_height_mobile ?? 36)
        : (int) ($companyProfile?->logo_height_desktop ?? 40);
    $logoStyle = "height: {$height}px";
@endphp

@if ($hasLogos)
    <span class="flex items-center gap-2">
        @if ($companyProfile?->logo_light_path || $companyProfile?->logo_path)
            <img
                src="{{ $companyProfile->logo_light_path ?? $companyProfile->logo_path }}"
                alt="{{ $companyProfile->name ?? config('app.name') }}"
                class="block h-auto w-auto object-contain dark:hidden"
                style="{{ $logoStyle }}"
            >
        @endif
        @if ($companyProfile?->logo_dark_path || $companyProfile?->logo_path)
            <img
                src="{{ $companyProfile->logo_dark_path ?? $companyProfile->logo_path }}"
                alt="{{ $companyProfile->name ?? config('app.name') }}"
                class="hidden h-auto w-auto object-contain dark:block"
                style="{{ $logoStyle }}"
            >
        @endif
    </span>
@else
    <span class="leading-tight">
        <strong class="block text-base font-bold text-white">{{ config('app.name') }}</strong>
        <span class="block text-[11px] font-medium text-brand-300">{{ __('Real estate management system') }}</span>
    </span>
@endif
