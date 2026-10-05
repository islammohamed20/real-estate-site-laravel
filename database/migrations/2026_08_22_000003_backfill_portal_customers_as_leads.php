<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $portalSourceId = DB::table('lead_sources')
            ->whereRaw('LOWER(name) = ?', ['portal'])
            ->value('id');

        if ($portalSourceId === null) {
            $portalSourceId = DB::table('lead_sources')->insertGetId([
                'name' => 'Portal',
                'color' => '#64748b',
                'sort_order' => 0,
                'is_active' => true,
                'is_default' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $customers = DB::table('customers')
            ->whereIn(DB::raw('LOWER(source)'), ['portal'])
            ->whereNotExists(function ($query): void {
                $query->select(DB::raw(1))
                    ->from('leads')
                    ->whereColumn('leads.customer_id', 'customers.id')
                    ->whereNull('leads.deleted_at');
            })
            ->get(['id', 'name', 'phone', 'whatsapp', 'email', 'occupation']);

        foreach ($customers as $customer) {
            DB::table('leads')->insert([
                'customer_id' => $customer->id,
                'lead_source_id' => $portalSourceId,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'whatsapp' => $customer->whatsapp ?: $customer->phone,
                'email' => $customer->email,
                'occupation' => $customer->occupation,
                'stage' => 'new',
                'source' => 'Portal',
                'priority' => 'normal',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('leads')
            ->where('source', 'Portal')
            ->whereNotNull('customer_id')
            ->whereIn('customer_id', function ($query): void {
                $query->select('id')
                    ->from('customers')
                    ->whereRaw('LOWER(source) = ?', ['portal']);
            })
            ->delete();
    }
};
