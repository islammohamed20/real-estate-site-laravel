<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Payment Schedule') }}</title>
    <style>
        @page { margin: 30px 26px 34px; }

        body {
            font-family: 'tajawal', sans-serif;
            color: #0f172a;
            font-size: 11.5px;
            line-height: 1.5;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .container { width: 100%; }

        .brand-bar {
            background: #0f172a;
            color: #ffffff;
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 16px;
        }

        .brand-bar table { width: 100%; border-collapse: collapse; }
        .brand-bar td { vertical-align: middle; }

        .brand-logo { max-height: 52px; max-width: 190px; }
        .brand-name { font-size: 20px; font-weight: 700; color: #ffffff; }
        .brand-legal { font-size: 11px; color: #cbd5e1; }
        .brand-contacts { font-size: 10px; color: #cbd5e1; line-height: 1.6; }
        .brand-title { font-size: 13px; font-weight: 700; color: #fbbf24; }

        .meta-pair {
            width: 100%;
            margin-bottom: 14px;
            border-spacing: 8px 0;
        }
        .meta-pair td {
            width: 50%;
            vertical-align: top;
            padding: 0 4px;
        }
        .meta-box {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }
        .meta-grid { width: 100%; border-collapse: collapse; }
        .meta-grid td {
            padding: 8px 12px;
            width: 50%;
            vertical-align: top;
        }
        .meta-grid td + td { border-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 1px solid #e2e8f0; }
        .meta-label { font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: {{ app()->getLocale() === 'ar' ? '0' : '0.06em' }}; }
        .meta-value { font-size: 11.5px; font-weight: 600; color: #0f172a; margin-top: 2px; }

        .section { margin-top: 16px; }
        .section--keep-together { page-break-inside: avoid; }
        .section-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #ffffff;
            background: #0f172a;
            border-radius: 8px;
            padding: 7px 12px;
            margin: 0;
            page-break-after: avoid;
        }
        .section + .detail,
        .section table.detail {
            margin-top: 0;
        }
        .section-title .accent { color: #fbbf24; }

        table.detail {
            width: 100%;
            border-collapse: collapse;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            page-break-inside: avoid;
        }

        table.schedule {
            width: 100%;
            border-collapse: collapse;
            direction: ltr;
        }

        table.detail th,
        table.detail td,
        table.schedule th,
        table.schedule td {
            border: 1px solid #e2e8f0;
            padding: 7px 10px;
        }

        table.detail th {
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
            width: 40%;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        table.detail td {
            font-weight: 600;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
            width: 60%;
        }

        table.detail tr.total th {
            background: #dcfce7;
            border-color: #bbf7d0;
            font-size: 13px;
            color: #166534;
        }

        table.detail tr.total td {
            background: #f0fdf4;
            border-color: #bbf7d0;
            font-size: 13px;
            color: #059669;
        }

        table.schedule th {
            background: #0f172a;
            color: #ffffff;
            font-size: 10.5px;
            font-weight: 700;
            text-align: center;
            white-space: nowrap;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }

        table.schedule td { white-space: nowrap; vertical-align: top; text-align: right; }
        table.schedule td.center { text-align: center; }
        table.schedule thead { display: table-header-group; }
        table.schedule tr { page-break-inside: avoid; }
        .schedule-range {
            font-size: 10px;
            color: #334155;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 10px;
            margin-bottom: 8px;
            text-align: center;
        }

        table.schedule tr.total-row td {
            background: #f0fdf4;
            border-color: #bbf7d0;
            font-weight: 700;
            color: #059669;
            font-size: 12px;
        }

        table.schedule tr.grand-total-row td {
            background: #0f172a;
            border-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            font-size: 12px;
        }

        .highlight-green { background: #f0fdf4; }
        .highlight-green th { color: #059669; font-weight: 700; background: #f0fdf4; }
        .highlight-green td { color: #059669; font-weight: 700; font-size: 13px; }
        .highlight-amber { background: #fffbeb; }
        .highlight-amber th { color: #92400e; font-weight: 700; background: #fffbeb; }
        .highlight-amber td { color: #d97706; font-weight: 700; }

        .pct-badge {
            padding: 2px 8px;
            border-radius: 999px;
            background: #d1fae5;
            color: #059669;
            font-size: 9.5px;
            font-weight: 700;
            vertical-align: middle;
            margin-left: 5px;
            margin-right: 5px;
        }

        .notes-box {
            border: 1px solid #fde68a;
            background: #fffbeb;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 10.5px;
            color: #92400e;
            margin-top: 12px;
        }

        .signatures {
            margin-top: 18px;
            page-break-inside: avoid;
        }
        .signatures table { width: 100%; border-collapse: collapse; }
        .signatures td { width: 50%; vertical-align: bottom; }
        .sign-line {
            border-top: 1.5px solid #334155;
            margin-top: 28px;
            padding-top: 6px;
            font-size: 11px;
            font-weight: 700;
            color: #334155;
            width: 78%;
        }
        .sign-role { font-size: 10px; color: #64748b; margin-top: 2px; }
        .stamp-img { max-height: 48px; max-width: 130px; margin-bottom: 4px; }

        .footer-note { margin-top: 16px; color: #334155; font-size: 9.5px; text-align: center; }
        .center { text-align: center; }
        .ltr { direction: ltr; }
        .nowrap { white-space: nowrap; }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 64px;
            font-weight: 700;
            color: rgba(148, 163, 184, 0.12);
            white-space: nowrap;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">{{ __('Non-binding offer') }}</div>
    <div class="container">
        {{-- Brand header --}}
        <div class="brand-bar">
            <table>
                <tr>
                    <td style="width: 30%;">
                        @if ($logoDataUri)
                            <img src="{{ $logoDataUri }}" class="brand-logo" alt="">
                        @else
                            <div class="brand-name">{{ $company?->name ?? config('app.name') }}</div>
                            @if ($company?->legal_name)
                                <div class="brand-legal">{{ $company->legal_name }}</div>
                            @endif
                        @endif
                    </td>
                    <td style="width: 40%; text-align: center;">
                        <div class="brand-title">{{ __('Proposed Payment Schedule') }}</div>
                        <div style="font-size: 10px; color: #e2e8f0; margin-top: 3px;">{{ __('Installment Plan') }}</div>
                        <div class="ltr" style="font-size: 9.5px; color: #cbd5e1; margin-top: 4px;">{{ __('Ref') }}: {{ $reference }}</div>
                        <div class="ltr" style="font-size: 9.5px; color: #cbd5e1; margin-top: 2px;">{{ now()->format('Y-m-d H:i') }} ({{ config('app.timezone') }})</div>
                    </td>
                    <td style="width: 30%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
                        <div class="brand-contacts ltr">
                            @if ($company?->name && ! $logoDataUri)
                                {{ $company->name }}<br>
                            @endif
                            @if ($company?->address)
                                {{ $company->address }}<br>
                            @endif
                            @if ($company?->phone)
                                <span class="ltr">{{ $company->phone }}</span>@if ($company?->email) · <span class="ltr">{{ $company->email }}</span>@endif
                            @elseif ($company?->email)
                                <span class="ltr">{{ $company->email }}</span>
                            @endif
                            @if ($company?->website)
                                <br><span class="ltr">{{ $company->website }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Client & unit metadata (two boxes side by side) --}}
        <table class="meta-pair" cellpadding="0" cellspacing="8">
            <tr>
                {{-- Customer details --}}
                <td>
                    <div class="meta-box">
                        <table class="meta-grid" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <div class="meta-label">{{ __('Customer') }}</div>
                                    <div class="meta-value">{{ $customer?->name ?? __('—') }}</div>
                                </td>
                                <td>
                                    <div class="meta-label">{{ __('Phone') }}</div>
                                    <div class="meta-value ltr">{{ $customer?->phone ?? '—' }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="meta-label">{{ __('Email') }}</div>
                                    <div class="meta-value ltr">{{ $customer?->email ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="meta-label">{{ __('Offer') }}</div>
                                    <div class="meta-value">{{ $offer?->offer_number ?? '—' }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="meta-label">{{ __('Project') }}</div>
                                    <div class="meta-value">{{ $unit?->project?->name ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="meta-label">{{ __('Delivery Date') }}</div>
                                    <div class="meta-value ltr">{{ $unit?->delivery_date ? $unit->delivery_date->format('Y-m-d') : '—' }}</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                {{-- Unit details --}}
                <td>
                    <div class="meta-box">
                        <table class="meta-grid" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <div class="meta-label">{{ __('Unit') }}</div>
                                    <div class="meta-value">{{ $unit?->unit_number ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="meta-label">{{ __('Unit Type') }}</div>
                                    <div class="meta-value">{{ $unit?->unit_type ? __($unit->unit_type) : '—' }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="meta-label">{{ __('Building') }}</div>
                                    <div class="meta-value">{{ $unit?->building?->name ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="meta-label">{{ __('Floor') }}</div>
                                    <div class="meta-value">{{ $unit?->floor_label ?? '—' }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="meta-label">{{ __('Area') }}</div>
                                    <div class="meta-value ltr">{{ $unit?->area ? number_format((float) $unit->area, 0) . ' ' . __('م²') : '—' }}</div>
                                </td>
                                <td>
                                    <div class="meta-label">{{ __('Bedrooms') }}</div>
                                    <div class="meta-value">{{ $unit?->bedrooms ?: '—' }}</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Financial summary --}}
        <div class="section section--keep-together">
            <div class="section-title">{{ __('Financial Summary') }}</div>
            <table class="detail">
                <tbody>
                    <tr>
                        <th>{{ __('Base Price') }}</th>
                        <td class="ltr">{{ number_format((float) $result['base_price'], 2) }} {{ __('ج.م') }}</td>
                    </tr>
                    @if ((float) $result['excellence_percent'] > 0)
                        <tr>
                            <th>{{ __('Excellence') }} ({{ number_format((float) $result['excellence_percent'], 2) }}%)</th>
                            <td class="ltr">+ {{ number_format((float) $result['excellence_amount'], 2) }} {{ __('ج.م') }}</td>
                        </tr>
                    @endif
                    @if ((float) $result['discount_amount'] > 0)
                        <tr>
                            <th>{{ __('Discount') }}@if ((float) $result['discount_percent'] > 0)<span class="pct-badge">{{ number_format((float) $result['discount_percent'], 1) }}%</span>@endif</th>
                            <td class="ltr">- {{ number_format((float) $result['discount_amount'], 2) }} {{ __('ج.م') }}</td>
                        </tr>
                    @endif
                    <tr class="total">
                        <th>{{ __('Final Price') }}</th>
                        <td class="ltr">{{ number_format((float) $result['final_price'], 2) }} {{ __('ج.م') }}</td>
                    </tr>
                    @if ((float) $result['maintenance_deposit'] > 0)
                        <tr class="highlight-amber">
                            <th>{{ __('Maintenance Deposit') }} ({{ number_format((float) $result['maintenance_percent'], 2) }}%)</th>
                            <td class="ltr">+ {{ number_format((float) $result['maintenance_deposit'], 2) }} {{ __('ج.م') }}</td>
                        </tr>
                        <tr class="total">
                            <th>{{ __('Total incl. Maintenance') }}</th>
                            <td class="ltr">{{ number_format((float) $result['final_price'] + (float) $result['maintenance_deposit'], 2) }} {{ __('ج.م') }}</td>
                        </tr>
                    @endif
                    <tr class="highlight-green">
                        <th>{{ $result['is_cash'] ? __('Cash Payment') : __('Down Payment') }}@if (! $result['is_cash'] && ! empty($input['down_payment_percent']))<span class="pct-badge">{{ number_format((float) $input['down_payment_percent'], 1) }}%</span>@endif</th>
                        <td class="ltr">{{ number_format((float) $result['down_payment'], 2) }} {{ __('ج.م') }}</td>
                    </tr>
                    @if (! $result['is_cash'])
                        <tr>
                            <th>{{ __('Remaining Amount') }}</th>
                            <td class="ltr">{{ number_format((float) $result['remaining'], 2) }} {{ __('ج.م') }}</td>
                        </tr>
                        <tr class="total">
                            <th>{{ __('Installment Amount') }} ({{ count($result['schedule']) }} {{ __('installments') }})</th>
                            <td class="ltr">{{ number_format((float) $result['installment_amount'], 2) }} {{ __('ج.م') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Payment schedule --}}
        <div class="section">
            <div class="section-title">{{ __('Payment Schedule') }} <span class="accent">· {{ $result['is_cash'] ? __('Cash') : count($result['schedule']).' '.__('installments') }}</span></div>
            @if ($result['is_cash'] || count($result['schedule']) === 0)
                <div class="notes-box" style="border-color: #bbf7d0; background: #f0fdf4; color: #059669;">
                    {{ __('Cash payment — the full amount is paid upfront.') }}
                </div>
            @else
                @php($firstDue = $result['schedule'][0]['due_date'] ?? null)
                @php($lastDue = $result['schedule'][count($result['schedule']) - 1]['due_date'] ?? null)
                @if ($firstDue && $lastDue)
                    <div class="schedule-range ltr">
                        {{ __('First installment') }}: {{ $firstDue }} &nbsp;·&nbsp; {{ __('Last installment') }}: {{ $lastDue }}
                    </div>
                @endif
                <table class="schedule">
                    <colgroup>
                        <col style="width: 8%;">
                        <col style="width: 20%;">
                        <col style="width: 24%;">
                        <col style="width: 24%;">
                        <col style="width: 24%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Due Date') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Paid to Date') }}</th>
                            <th>{{ __('Balance After') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($totalAmount = 0)
                        @php($cumulativePaid = 0)
                        @php($runningBalance = (float) $result['remaining'])
                        @foreach ($result['schedule'] as $row)
                            @php($totalAmount += (float) $row['amount'])
                            @php($cumulativePaid += (float) $row['amount'])
                            <tr>
                                <td class="center">{{ $row['installment_number'] }}</td>
                                <td class="center ltr">{{ $row['due_date'] }}</td>
                                <td class="ltr">{{ number_format((float) $row['amount'], 2) }} {{ __('ج.م') }}</td>
                                <td class="ltr">{{ number_format($cumulativePaid, 2) }} {{ __('ج.م') }}</td>
                                <td class="ltr">{{ number_format((float) $row['balance_after'], 2) }} {{ __('ج.م') }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td class="center" colspan="2">{{ __('Total') }}</td>
                            <td class="ltr">{{ number_format($totalAmount, 2) }} {{ __('ج.م') }}</td>
                            <td class="ltr">{{ number_format($totalAmount, 2) }} {{ __('ج.م') }}</td>
                            <td></td>
                        </tr>
                        <tr class="grand-total-row">
                            <td class="center" colspan="3">{{ __('Grand Total (incl. Down Payment)') }}</td>
                            <td class="ltr" colspan="2">{{ number_format((float) $result['final_price'], 2) }} {{ __('ج.م') }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif

            <div class="notes-box">
                {{ __('Maintenance amount is paid during the contract period before delivery.') }}
            </div>
        </div>

        {{-- Signatures --}}
        <div class="signatures">
            <div class="section-title">{{ __('Signatures') }}</div>
            <table>
                <tr>
                    <td style="text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
                        <div class="sign-line" style="margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: auto;">{{ __('Client Signature') }}</div>
                        <div class="sign-role">{{ $customer?->name ?? '' }}</div>
                        <div class="sign-role">{{ __('Date') }}: <span class="ltr">____ / ____ / ________</span></div>
                    </td>
                    <td style="text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
                        @if ($stampDataUri)
                            <img src="{{ $stampDataUri }}" class="stamp-img" alt="">
                        @endif
                        <div class="sign-line" style="margin-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: auto;">{{ __('Company Signature') }}</div>
                        <div class="sign-role">{{ $company?->name ?? config('app.name') }}</div>
                        <div class="sign-role">{{ __('Date') }}: <span class="ltr">____ / ____ / ________</span></div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer-note">
            {{ __('This document is a non-binding offer generated automatically from the calculator inputs.') }}<br>
            {{ __('Prices and terms are subject to change and to the final signed contract.') }}<br>
            {{ __('Ref') }}: <span class="ltr">{{ $reference }}</span>
        </div>
    </div>
</body>
</html>
