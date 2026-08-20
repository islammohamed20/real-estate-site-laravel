<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    public function index(): View
    {
        return view('dashboard.email-templates.index', [
            'templates' => EmailTemplate::query()->orderBy('name')->get(),
            'variables' => $this->variables(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.email-templates.form', [
            'template' => null,
            'variables' => $this->variables(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $template = EmailTemplate::query()->create($this->validated($request));

        return redirect()->route('dashboard.email-templates.edit', $template)
            ->with('status', __('Email template created successfully.'));
    }

    public function edit(EmailTemplate $template): View
    {
        return view('dashboard.email-templates.form', [
            'template' => $template,
            'variables' => $this->variables(),
        ]);
    }

    public function update(Request $request, EmailTemplate $template): RedirectResponse
    {
        $template->update($this->validated($request, $template));

        return back()->with('status', __('Email template updated successfully.'));
    }

    public function toggle(EmailTemplate $template): RedirectResponse
    {
        $template->update(['is_active' => ! $template->is_active]);

        return back()->with('status', $template->is_active ? __('Template activated.') : __('Template deactivated.'));
    }

    public function destroy(EmailTemplate $template): RedirectResponse
    {
        $template->delete();

        return redirect()->route('dashboard.email-templates.index')->with('status', __('Email template deleted.'));
    }

    public function preview(Request $request, EmailTemplate $template): View
    {
        $locale = in_array($request->input('locale'), ['ar', 'en'], true) ? $request->input('locale') : app()->getLocale();
        $rendered = $template->render($this->sampleVariables(), $locale);

        return view('dashboard.email-templates.preview', [
            'template' => $template,
            'rendered' => $rendered,
            'locale' => $locale,
        ]);
    }

    public function sendTest(Request $request, EmailTemplate $template): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'locale' => ['nullable', 'in:ar,en'],
        ]);
        $locale = $validated['locale'] ?? app()->getLocale();
        $rendered = $template->render($this->sampleVariables(), $locale);

        Mail::html(nl2br(e($rendered['body'])), function ($message) use ($validated, $rendered, $locale): void {
            $message->to($validated['email'])->subject(($locale === 'ar' ? '[تجربة] ' : '[TEST] ').$rendered['subject']);
        });

        return back()->with('status', __('Test email sent successfully.'));
    }

    private function validated(Request $request, ?EmailTemplate $template = null): array
    {
        $keyRule = 'required|string|max:100|regex:/^[a-z0-9_]+$/|unique:email_templates,key';
        if ($template) {
            $keyRule .= ','.$template->id;
        }

        return $request->validate([
            'key' => [$keyRule],
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:20000'],
            'subject_ar' => ['required', 'string', 'max:255'],
            'body_ar' => ['required', 'string', 'max:20000'],
            'subject_en' => ['required', 'string', 'max:255'],
            'body_en' => ['required', 'string', 'max:20000'],
            'is_active' => ['boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function variables(): array
    {
        return [
            'customer_name' => __('Customer name'), 'lead_name' => __('Lead name'), 'agent_name' => __('Salesperson name'),
            'company_name' => __('Company name'), 'offer_number' => __('Offer number'), 'amount' => __('Amount'),
            'hours' => __('Overdue hours'), 'action_url' => __('Action URL'),
        ];
    }

    private function sampleVariables(): array
    {
        return [
            'customer_name' => 'Ahmed Mohamed', 'lead_name' => 'Ahmed Mohamed', 'agent_name' => 'Sales Representative',
            'company_name' => config('app.name'), 'offer_number' => 'OFF-2026-001', 'amount' => '1,500,000 EGP',
            'hours' => '3', 'action_url' => url('/'),
        ];
    }
}
