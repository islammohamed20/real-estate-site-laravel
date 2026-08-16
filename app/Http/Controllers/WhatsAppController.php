<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Crm\CrmDeal;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\MessageTemplate;
use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\EvolutionApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WhatsAppController extends Controller
{
    public function __construct(
        protected EvolutionApiService $evolution
    ) {
        //
    }

    /**
     * Chat panel page (conversation list + chat window).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $canManage = $user->can('view all whatsapp conversations') || $user->can('assign whatsapp');

        $query = WhatsAppConversation::query()
            ->with(['assignedTo:id,name', 'linkedLead:id,name', 'linkedCustomer:id,name'])
            // Sales reps see ONLY conversations assigned to them; managers see everything.
            ->when(! $canManage, fn ($q) => $q->where('assigned_to', $user->id));

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $conversations = $query->orderByDesc('last_message_at')->limit(100)->get();

        // Smart linking: find existing leads/customers that share the same phone.
        $phones = $conversations->pluck('customer_phone')->unique()->filter();
        $existingLeads = Lead::query()->whereIn('phone', $phones)->get(['id', 'phone', 'name'])->groupBy('phone');
        $existingCustomers = Customer::query()->whereIn('phone', $phones)->get(['id', 'phone', 'name'])->groupBy('phone');

        $templates = MessageTemplate::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $salesUsers = $canManage
            ? User::query()
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['Sales Executive', 'Sales Manager', 'Administrator', 'Marketing Manager']))
                ->orWhereHas('permissions', fn ($q) => $q->where('name', 'reply whatsapp'))
                ->orderBy('name')
                ->get(['id', 'name'])
            : collect();

        $plans = \App\Models\InstallmentPlan::query()
            ->with(['customer:id,name,phone', 'unit:id,unit_number', 'project:id,name'])
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        return view('dashboard.whatsapp.index', [
            'conversations' => $conversations,
            'conversationsJson' => $conversations->map(fn (WhatsAppConversation $c) => $this->conversationPayload($c))->values(),
            'templatesJson' => $templates->map(fn (MessageTemplate $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'body' => $t->body,
            ])->values(),
            'salesUsersJson' => $salesUsers->map(fn (User $u) => ['id' => $u->id, 'name' => $u->name])->values(),
            'existingLeadsJson' => $existingLeads->map->map(fn (Lead $l) => ['id' => $l->id, 'name' => $l->name])->toArray(),
            'existingCustomersJson' => $existingCustomers->map->map(fn (Customer $c) => ['id' => $c->id, 'name' => $c->name])->toArray(),
            'plansJson' => $plans->map(fn (\App\Models\InstallmentPlan $plan) => [
                'id' => $plan->id,
                'name' => $plan->name ?: ('#'.$plan->id),
                'customer_name' => $plan->customer?->name,
                'unit' => $plan->unit?->unit_number,
                'project' => $plan->project?->name,
            ])->values(),
            'templates' => $templates,
            'salesUsers' => $salesUsers,
            'canManage' => $canManage,
            'evolutionConfigured' => $this->evolution->isConfigured(),
            'connectionOpen' => $this->evolution->checkConnection(),
            'baseUrl' => url('/'),
        ]);
    }

    /**
     * JSON: conversation list for the panel (polled for live updates).
     */
    public function conversations(Request $request): JsonResponse
    {
        $user = Auth::user();
        $canManage = $user->can('view all whatsapp conversations') || $user->can('assign whatsapp');

        $query = WhatsAppConversation::query()
            ->with(['assignedTo:id,name'])
            ->when(! $canManage, fn ($q) => $q->where(fn ($q2) => $q2->where('assigned_to', $user->id)->orWhereNull('assigned_to')));

        if ($request->filled('status') && $request->string('status')->toString() !== 'all') {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(fn ($q) => $q->where('customer_name', 'like', "%{$search}%")->orWhere('customer_phone', 'like', "%{$search}%"));
        }

        $list = $query->orderByDesc('last_message_at')->limit(100)->get();

        return response()->json([
            'conversations' => $list->map(fn (WhatsAppConversation $c) => $this->conversationPayload($c)),
        ]);
    }

    /**
     * JSON: messages of one conversation (marks it read).
     */
    public function messages(WhatsAppConversation $conversation, Request $request): JsonResponse
    {
        $this->authorizeConversation($conversation);
        $conversation->markAsRead();

        $messages = $conversation->messages()
            ->with('senderUser:id,name')
            ->orderBy('created_at')
            ->get()
            ->map(fn (WhatsAppMessage $m) => [
                'id' => $m->id,
                'direction' => $m->direction,
                'body' => $m->body,
                'message_type' => $m->message_type,
                'media_name' => $m->media_name,
                'media_path' => $m->media_path,
                'delivery_status' => $m->delivery_status,
                'sender_name' => $m->senderUser?->name,
                'created_at' => $m->created_at?->format('Y-m-d H:i'),
            ]);

        return response()->json([
            'conversation' => $this->conversationPayload($conversation),
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message to the customer via the WhatsApp gateway.
     */
    public function send(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);
        abort_unless(Auth::user()->can('reply whatsapp'), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);

        $body = trim($validated['body']);
        if ($body === '') {
            return response()->json(['success' => false, 'message' => __('Message cannot be empty.')], 422);
        }

        // Try to deliver via the gateway first — the message is only stored
        // locally if the gateway accepted it (or is not configured yet).
        $sent = $this->evolution->sendMessage($conversation->customer_phone, $body);

        $message = $conversation->messages()->create([
            'direction' => WhatsAppMessage::DIRECTION_OUTGOING,
            'body' => $body,
            'message_type' => 'text',
            'delivery_status' => $sent ? 'sent' : 'failed',
            'sender_user_id' => Auth::id(),
        ]);

        // Whoever answers first owns the conversation (auto-claim) — the
        // panel is the single reply interface through the company number.
        $conversation->update([
            'last_message_at' => now(),
            'status' => WhatsAppConversation::STATUS_ASSIGNED,
            'assigned_to' => $conversation->assigned_to ?? Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'sent' => $sent,
            'message' => $sent ? __('Message sent.') : __('Message saved but the WhatsApp gateway is not configured — it will not reach the customer yet.'),
            'msg' => [
                'id' => $message->id,
                'direction' => $message->direction,
                'body' => $message->body,
                'delivery_status' => $message->delivery_status,
                'created_at' => $message->created_at?->format('Y-m-d H:i'),
            ],
        ]);
    }

    /**
     * Assign the conversation to a sales user (manager action).
     */
    public function assign(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        abort_unless(Auth::user()->can('assign whatsapp'), 403);

        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $conversation->update([
            'assigned_to' => $validated['user_id'] ?? null,
            'status' => ! empty($validated['user_id'])
                ? WhatsAppConversation::STATUS_ASSIGNED
                : WhatsAppConversation::STATUS_NEW,
        ]);

        Log::info("WhatsApp conversation #{$conversation->id} assigned to user ".($validated['user_id'] ?? 'nobody')." by ".Auth::id());

        return response()->json([
            'success' => true,
            'conversation' => $this->conversationPayload($conversation->fresh(['assignedTo:id,name'])),
        ]);
    }

    /**
     * Available payment plans for a conversation (linked customer/lead first,
     * then recent plans as a fallback) — for the send-PDF picker.
     */
    public function planOptions(WhatsAppConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);

        $plans = \App\Models\InstallmentPlan::query()
            ->with(['customer:id,name,phone', 'unit:id,unit_number', 'project:id,name'])
            ->whereNull('deleted_at')
            ->where(function ($q) use ($conversation) {
                $q->where('customer_id', $conversation->linked_customer_id)
                    ->orWhere('lead_id', $conversation->linked_lead_id)
                    ->orWhereHas('customer', fn ($c) => $c->where('phone', $conversation->customer_phone));
            })
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        if ($plans->isEmpty()) {
            $plans = \App\Models\InstallmentPlan::query()
                ->with(['customer:id,name,phone', 'unit:id,unit_number', 'project:id,name'])
                ->whereNull('deleted_at')
                ->orderByDesc('id')
                ->limit(15)
                ->get();
        }

        return response()->json([
            'plans' => $plans->map(fn (\App\Models\InstallmentPlan $plan) => [
                'id' => $plan->id,
                'name' => $plan->name ?: ('#'.$plan->id),
                'customer_name' => $plan->customer?->name,
                'unit' => $plan->unit?->unit_number,
                'project' => $plan->project?->name,
                'final_price' => number_format((float) $plan->final_price, 0),
                'installment_count' => $plan->installment_count,
                'created_at' => $plan->created_at?->format('Y-m-d'),
            ])->values(),
            'linked' => $plans->isNotEmpty(),
        ]);
    }

    /**
     * Generate the payment-plan PDF and send it to the customer via WhatsApp.
     */
    public function sendPlan(Request $request, WhatsAppConversation $conversation, \App\Services\PlanPdfService $pdfService): JsonResponse
    {
        $this->authorizeConversation($conversation);
        abort_unless(Auth::user()->can('reply whatsapp'), 403);

        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:installment_plans,id'],
            'caption' => ['nullable', 'string', 'max:500'],
        ]);

        $plan = \App\Models\InstallmentPlan::query()->findOrFail((int) $validated['plan_id']);

        $pdf = $pdfService->renderPdf($plan);

        // Persist a local copy so the chat history keeps a downloadable link.
        $dir = storage_path('app/private/whatsapp-media');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $storedPath = $dir.'/'.$pdf['filename'];
        file_put_contents($storedPath, $pdf['content']);

        $caption = $validated['caption'] ?? __('Payment plan PDF — price and installments.');
        $sent = $this->evolution->sendMedia(
            $conversation->customer_phone,
            base64_encode($pdf['content']),
            $pdf['filename'],
            $caption
        );

        $message = $conversation->messages()->create([
            'direction' => WhatsAppMessage::DIRECTION_OUTGOING,
            'body' => $caption,
            'message_type' => 'document',
            'media_name' => $pdf['filename'],
            'media_path' => str_replace(storage_path(), '', $storedPath),
            'delivery_status' => $sent ? 'sent' : 'failed',
            'sender_user_id' => Auth::id(),
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'status' => WhatsAppConversation::STATUS_ASSIGNED,
            'assigned_to' => $conversation->assigned_to ?? Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'sent' => $sent,
            'message' => $sent
                ? __('Payment plan PDF sent to the customer.')
                : __('PDF generated but the WhatsApp gateway is not configured — it will not reach the customer yet.'),
            'msg' => [
                'id' => $message->id,
                'direction' => $message->direction,
                'body' => $message->body,
                'message_type' => $message->message_type,
                'media_name' => $message->media_name,
                'media_path' => $message->media_path,
                'delivery_status' => $message->delivery_status,
                'created_at' => $message->created_at?->format('Y-m-d H:i'),
            ],
        ]);
    }

    /**
     * Stream a stored WhatsApp media file (authorized users only).
     */
    public function media(WhatsAppMessage $message): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $conversation = $message->conversation;
        $this->authorizeConversation($conversation);

        if (empty($message->media_path) || ! is_file(storage_path($message->media_path))) {
            abort(404);
        }

        return response()->streamDownload(
            function () use ($message): void {
                readfile(storage_path($message->media_path));
            },
            $message->media_name ?? 'document.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Claim an unassigned conversation (managers only — reps see only their assigned ones).
     */
    public function claim(WhatsAppConversation $conversation): JsonResponse
    {
        abort_unless(Auth::user()->can('view all whatsapp conversations') || Auth::user()->can('assign whatsapp'), 403);
        abort_unless($conversation->assigned_to === null, 403);

        $conversation->update([
            'assigned_to' => Auth::id(),
            'status' => WhatsAppConversation::STATUS_ASSIGNED,
        ]);

        return response()->json([
            'success' => true,
            'conversation' => $this->conversationPayload($conversation->fresh(['assignedTo:id,name'])),
        ]);
    }

    /**
     * Change conversation status (close / reopen).
     */
    public function status(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);

        $validated = $request->validate([
            'status' => ['required', 'in:new,assigned,closed'],
        ]);

        $conversation->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'conversation' => $this->conversationPayload($conversation->fresh(['assignedTo:id,name'])),
        ]);
    }

    /**
     * Link the conversation to an existing CRM lead or customer.
     */
    public function link(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);

        $validated = $request->validate([
            'linked_lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'linked_customer_id' => ['nullable', 'integer', 'exists:customers,id'],
        ]);

        $conversation->update($validated);

        return response()->json([
            'success' => true,
            'conversation' => $this->conversationPayload($conversation->fresh(['linkedLead:id,name', 'linkedCustomer:id,name'])),
        ]);
    }

    /**
     * Create a lead from the conversation (quick CRM hand-off).
     */
    public function createLead(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'numeric', 'min:0'],
        ]);

        $lead = DB::transaction(function () use ($conversation, $validated): Lead {
            return Lead::create([
                'name' => ! empty($validated['name']) ? $validated['name'] : ($conversation->customer_name ?: __('WhatsApp Customer')),
                'phone' => $conversation->customer_phone,
                'source' => 'WhatsApp',
                'assigned_sales_id' => $conversation->assigned_to,
                'budget' => $validated['budget'] ?? null,
            ]);
        });

        $conversation->update(['linked_lead_id' => $lead->id]);

        return response()->json([
            'success' => true,
            'lead_id' => $lead->id,
            'message' => __('Lead created and linked to this conversation.'),
        ]);
    }

    /**
     * Start a new conversation manually (e.g. outbound).
     */
    public function start(Request $request): JsonResponse
    {
        abort_unless(Auth::user()->can('reply whatsapp'), 403);

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $phone = \App\Support\WhatsApp::number($validated['phone']);
        if ($phone === null) {
            return response()->json(['success' => false, 'message' => __('Invalid phone number.')], 422);
        }

        $conversation = WhatsAppConversation::query()->firstOrCreate(
            ['customer_phone' => $phone],
            [
                'customer_name' => $validated['name'] ?? null,
                'status' => WhatsAppConversation::STATUS_NEW,
                'assigned_to' => Auth::id(),
            ]
        );

        if ($conversation->wasRecentlyCreated) {
            $conversation->update(['status' => WhatsAppConversation::STATUS_ASSIGNED]);
        }

        return response()->json([
            'success' => true,
            'conversation' => $this->conversationPayload($conversation->fresh(['assignedTo:id,name'])),
        ]);
    }

    /**
     * Register the Evolution instance webhook so incoming messages are pushed here.
     */
    public function registerWebhook(Request $request): JsonResponse
    {
        abort_unless(Auth::user()->can('view all whatsapp conversations') || Auth::user()->can('assign whatsapp'), 403);

        $webhookUrl = rtrim(url('/'), '/').'/webhook/whatsapp/evolution';
        $ok = $this->evolution->setWebhook($webhookUrl);

        return response()->json([
            'success' => $ok,
            'message' => $ok
                ? __('Webhook registered successfully on the WhatsApp instance.')
                : __('Could not register the webhook — check the Evolution API settings.'),
            'webhook_url' => $webhookUrl,
        ]);
    }

    /**
     * Sales team performance report: conversations, response time,
     * WhatsApp → lead conversion and deals.
     */
    public function reports(Request $request): View
    {
        $user = Auth::user();
        $canManage = $user->can('view all whatsapp conversations') || $user->can('assign whatsapp');

        $days = max(1, min(365, (int) $request->integer('days', 30)));
        $since = CarbonImmutable::now()->subDays($days);

        // Reps = anyone who has conversations assigned, sent messages, or holds reply permission.
        $repIds = WhatsAppConversation::query()
            ->where('created_at', '>=', $since)
            ->whereNotNull('assigned_to')
            ->distinct()
            ->pluck('assigned_to')
            ->merge(
                WhatsAppMessage::query()
                    ->where('sender_user_id', '!=', null)
                    ->where('created_at', '>=', $since)
                    ->distinct()
                    ->pluck('sender_user_id')
            )
            ->unique()
            ->filter();

        $reps = User::query()
            ->whereIn('id', $repIds)
            ->orWhereHas('permissions', fn ($q) => $q->where('name', 'reply whatsapp'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $rows = [];
        foreach ($reps as $rep) {
            if (! $canManage && $rep->id !== $user->id) {
                continue;
            }
            $rows[] = $this->repStats($rep, $since);
        }

        // Aggregate totals (visible to managers; single-rep view shows their own).
        $totals = [
            'conversations' => array_sum(array_column($rows, 'conversations')),
            'messages_sent' => array_sum(array_column($rows, 'messages_sent')),
            'leads' => array_sum(array_column($rows, 'leads')),
            'deals' => array_sum(array_column($rows, 'deals')),
            'deals_won' => array_sum(array_column($rows, 'deals_won')),
            'deal_value' => round(array_sum(array_column($rows, 'deal_value')), 2),
        ];

        return view('dashboard.whatsapp.reports', [
            'rows' => $rows,
            'totals' => $totals,
            'days' => $days,
            'canManage' => $canManage,
        ]);
    }

    /**
     * Compute WhatsApp performance metrics for one rep.
     */
    protected function repStats(User $rep, CarbonImmutable $since): array
    {
        $conversations = WhatsAppConversation::query()
            ->where('assigned_to', $rep->id)
            ->where('created_at', '>=', $since)
            ->get(['id']);
        $conversationIds = $conversations->pluck('id');

        $messagesSent = WhatsAppMessage::query()
            ->where('sender_user_id', $rep->id)
            ->where('direction', WhatsAppMessage::DIRECTION_OUTGOING)
            ->where('created_at', '>=', $since)
            ->count();

        // Average response time: incoming → next outgoing reply, per conversation.
        $totalSeconds = 0;
        $responsePairs = 0;
        if ($conversationIds->isNotEmpty()) {
            $byConversation = WhatsAppMessage::query()
                ->whereIn('conversation_id', $conversationIds)
                ->orderBy('created_at')
                ->get(['conversation_id', 'direction', 'created_at'])
                ->groupBy('conversation_id');

            foreach ($byConversation as $messages) {
                $waitingIncoming = null;
                foreach ($messages as $message) {
                    if ($message->direction === WhatsAppMessage::DIRECTION_INCOMING) {
                        $waitingIncoming = $message->created_at;
                    } elseif ($message->direction === WhatsAppMessage::DIRECTION_OUTGOING && $waitingIncoming !== null) {
                        $totalSeconds += abs($message->created_at->diffInSeconds($waitingIncoming));
                        $responsePairs++;
                        $waitingIncoming = null;
                    }
                }
            }
        }
        $avgResponseMinutes = $responsePairs > 0
            ? round($totalSeconds / $responsePairs / 60, 1)
            : null;

        // Leads from WhatsApp: linked to the rep's conversations, or sourced from WhatsApp.
        $linkedLeadIds = WhatsAppConversation::query()
            ->where('assigned_to', $rep->id)
            ->where('created_at', '>=', $since)
            ->whereNotNull('linked_lead_id')
            ->pluck('linked_lead_id');

        $leads = Lead::query()
            ->where('created_at', '>=', $since)
            ->where(function ($q) use ($linkedLeadIds, $rep) {
                $q->whereIn('id', $linkedLeadIds)
                    ->orWhere(fn ($q2) => $q2->where('source', 'WhatsApp')->where('assigned_sales_id', $rep->id));
            })
            ->get(['id']);
        $leadIds = $leads->pluck('id');

        $deals = CrmDeal::query()
            ->whereIn('lead_id', $leadIds)
            ->where('created_at', '>=', $since)
            ->get(['status', 'value']);

        $leadCount = $leads->count();
        $dealCount = $deals->count();
        $dealsWon = $deals->where('status', 'won')->count();
        $dealValue = $deals->whereIn('status', ['open', 'won'])->sum('value');

        return [
            'rep_id' => $rep->id,
            'rep_name' => $rep->name,
            'conversations' => $conversations->count(),
            'messages_sent' => $messagesSent,
            'avg_response_minutes' => $avgResponseMinutes,
            'leads' => $leadCount,
            'deals' => $dealCount,
            'deals_won' => $dealsWon,
            'deal_value' => round((float) $dealValue, 2),
            'lead_rate' => $conversations->count() > 0 ? round($leadCount / $conversations->count() * 100, 1) : 0.0,
            'deal_rate' => $leadCount > 0 ? round($dealCount / $leadCount * 100, 1) : 0.0,
        ];
    }

    /**
     * Templates management page.
     */
    public function templates(): View
    {
        $templates = MessageTemplate::query()->with('createdBy:id,name')->orderByDesc('id')->get();

        return view('dashboard.whatsapp.templates', ['templates' => $templates]);
    }

    public function storeTemplate(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->can('reply whatsapp'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:4000'],
            'is_active' => ['boolean'],
        ]);

        MessageTemplate::create([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
            'created_by' => Auth::id(),
        ]);

        return back()->with('status', __('Template saved.'));
    }

    public function destroyTemplate(MessageTemplate $template): RedirectResponse
    {
        abort_unless(Auth::user()->can('reply whatsapp'), 403);

        $template->delete();

        return back()->with('status', __('Template deleted.'));
    }

    /**
     * Scope check — a conversation is visible when the user is a manager
     * or the assignee. Sales reps see ONLY their assigned conversations.
     */
    protected function authorizeConversation(WhatsAppConversation $conversation): void
    {
        $user = Auth::user();
        if ($user->can('view all whatsapp conversations') || $user->can('assign whatsapp')) {
            return;
        }

        abort_unless($conversation->assigned_to === $user->id, 403);
    }

    protected function conversationPayload(WhatsAppConversation $c): array
    {
        return [
            'id' => $c->id,
            'customer_name' => $c->customer_name,
            'customer_phone' => $c->customer_phone,
            'status' => $c->status,
            'assigned_to' => $c->assigned_to,
            'assigned_name' => $c->assignedTo?->name,
            'linked_lead_id' => $c->linked_lead_id,
            'linked_lead_name' => $c->linkedLead?->name,
            'linked_customer_id' => $c->linked_customer_id,
            'linked_customer_name' => $c->linkedCustomer?->name,
            'unread_count' => (int) $c->unread_count,
            'last_message_at' => $c->last_message_at?->diffForHumans(),
            'last_message_raw' => $c->last_message_at?->toDateTimeString(),
        ];
    }
}
