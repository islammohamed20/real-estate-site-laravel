<!DOCTYPE html>
<html lang="{{ $isArabic ? 'ar' : 'en' }}" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Tajawal', sans-serif; font-size: 11px; color: #1e293b; }
    .receipt { width: 100%; padding: 15px; }
    .header { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 10px; margin-bottom: 12px; }
    .header img { height: 40px; margin-bottom: 5px; }
    .header h2 { font-size: 14px; color: #059669; margin-bottom: 2px; }
    .header p { font-size: 9px; color: #64748b; }
    .receipt-title { text-align: center; font-size: 16px; font-weight: bold; color: #059669; margin: 10px 0; padding: 8px; background: #ecfdf5; border-radius: 8px; border: 1px solid #d1fae5; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 12px; }
    .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px; }
    .info-box .label { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
    .info-box .value { font-size: 11px; font-weight: bold; color: #1e293b; margin-top: 2px; }
    .amount-box { background: #ecfdf5; border: 2px solid #10b981; border-radius: 8px; padding: 12px; text-align: center; margin: 12px 0; }
    .amount-box .amount { font-size: 22px; font-weight: bold; color: #059669; }
    .amount-box .currency { font-size: 11px; color: #64748b; }
    .details { margin: 10px 0; }
    .details table { width: 100%; border-collapse: collapse; }
    .details td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
    .details td:first-child { color: #94a3b8; width: 40%; }
    .details td:last-child { font-weight: 600; color: #1e293b; }
    .footer { text-align: center; margin-top: 15px; padding-top: 10px; border-top: 1px dashed #e2e8f0; font-size: 8px; color: #94a3b8; }
    .stamp { text-align: center; margin-top: 10px; }
    .stamp img { height: 50px; opacity: 0.6; }
    .paid-badge { display: inline-block; background: #059669; color: white; padding: 3px 12px; border-radius: 20px; font-size: 10px; font-weight: bold; margin: 5px 0; }
    .barcode { text-align: center; margin: 8px 0; font-family: monospace; font-size: 8px; color: #64748b; letter-spacing: 2px; }
</style>
</head>
<body>
<div class="receipt">
    <div class="header">
        @if($logoDataUri)
            <img src="{{ $logoDataUri }}" alt="Logo">
        @endif
        <h2>{{ $company?->name ?? 'Venicía' }}</h2>
        <p>{{ $company?->address ?? '' }}</p>
        <p>{{ $company?->phone ?? '' }} {{ $company?->email ? '· '.$company->email : '' }}</p>
    </div>

    <div class="receipt-title">
        {{ __('Installment Payment Receipt') }}
    </div>

    <div style="text-align:center">
        <span class="paid-badge">✅ {{ __('PAID') }}</span>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <div class="label">{{ __('Customer') }}</div>
            <div class="value">{{ $plan->customer?->name ?? '—' }}</div>
        </div>
        <div class="info-box">
            <div class="label">{{ __('Phone') }}</div>
            <div class="value" dir="ltr">{{ $plan->customer?->phone ?? '—' }}</div>
        </div>
        <div class="info-box">
            <div class="label">{{ __('Project') }}</div>
            <div class="value">{{ $plan->unit?->project?->name ?? '—' }}</div>
        </div>
        <div class="info-box">
            <div class="label">{{ __('Unit') }}</div>
            <div class="value">{{ $plan->unit?->unit_number ?? '—' }}</div>
        </div>
        <div class="info-box">
            <div class="label">{{ __('Plan Number') }}</div>
            <div class="value">#{{ $plan->id }}</div>
        </div>
        <div class="info-box">
            <div class="label">{{ __('Installment Number') }}</div>
            <div class="value">#{{ $item->installment_number }}</div>
        </div>
    </div>

    <div class="amount-box">
        <div class="amount">{{ number_format((float) $item->paid_amount, 2) }}</div>
        <div class="currency">{{ __('EGP') }}</div>
    </div>

    <div class="details">
        <table>
            <tr>
                <td>{{ __('Total Installment Amount') }}</td>
                <td>{{ number_format((float) $item->amount, 2) }} {{ __('EGP') }}</td>
            </tr>
            <tr>
                <td>{{ __('Paid Amount') }}</td>
                <td style="color:#059669">{{ number_format((float) $item->paid_amount, 2) }} {{ __('EGP') }}</td>
            </tr>
            <tr>
                <td>{{ __('Remaining') }}</td>
                <td>{{ number_format(max(0, (float)$item->amount - (float)$item->paid_amount), 2) }} {{ __('EGP') }}</td>
            </tr>
            <tr>
                <td>{{ __('Payment Date') }}</td>
                <td>{{ $item->paid_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td>{{ __('Due Date') }}</td>
                <td>{{ $item->due_date?->format('Y-m-d') ?? '—' }}</td>
            </tr>
            @if($item->payment_method)
            <tr>
                <td>{{ __('Payment Method') }}</td>
                <td>{{ __($item->payment_method) }}</td>
            </tr>
            @endif
            <tr>
                <td>{{ __('Recorded By') }}</td>
                <td>{{ $item->paid_by ?? auth()->user()?->name ?? '—' }}</td>
            </tr>
            @if($item->payment_notes)
            <tr>
                <td>{{ __('Notes') }}</td>
                <td>{{ $item->payment_notes }}</td>
            </tr>
            @endif
            <tr>
                <td>{{ __('Total Plan Value') }}</td>
                <td>{{ number_format((float) $plan->final_price, 2) }} {{ __('EGP') }}</td>
            </tr>
        </table>
    </div>

    <div class="barcode">
        ||||| {{ sprintf('PLAN-%d-INST-%d-%s', $plan->id, $item->installment_number, now()->format('YmdHis')) }} |||||
    </div>

    @if($company?->stamp_path)
    <div class="stamp">
        <img src="{{ $this->imageDataUri($company->stamp_path) ?? '' }}" alt="Stamp">
    </div>
    @endif

    <div class="footer">
        <p>{{ $company?->name ?? 'Venicía' }} — {{ __('This is a computer-generated receipt') }}</p>
        <p>{{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</div>
</body>
</html>
