<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutomationSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutomationController extends Controller
{
    public function index(): View
    {
        AutomationSetting::ensureDefaults();

        $settings = AutomationSetting::query()->pluck('value', 'key');

        return view('dashboard.automation.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lead_auto_assign' => ['boolean'],
            'sla_enabled' => ['boolean'],
            'sla_alert_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'sla_escalate_minutes' => ['required', 'integer', 'min:2', 'max:10080'],
            'followup_alerts_enabled' => ['boolean'],
            'weekly_report_enabled' => ['boolean'],
            'weekly_report_day' => ['required', 'integer', 'between:0,6'],
            'weekly_report_time' => ['required', 'date_format:H:i'],
            'scorecard_enabled' => ['boolean'],
            'scorecard_day' => ['required', 'integer', 'between:1,28'],
            'scorecard_time' => ['required', 'date_format:H:i'],
        ]);

        // Escalation must stay above the alert threshold.
        if ((int) $validated['sla_escalate_minutes'] <= (int) $validated['sla_alert_minutes']) {
            return back()->withErrors([
                'sla_escalate_minutes' => __('Escalation must be greater than the alert threshold.'),
            ])->withInput();
        }

        AutomationSetting::setMany($validated);

        return back()->with('status', __('Automation settings saved.'));
    }
}
