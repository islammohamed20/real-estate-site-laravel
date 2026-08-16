<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The sessions table stores the authenticated user id for every guard
        // (admin `web` and customer `customer`). With the customer portal, a
        // customer-authenticated request writes the CUSTOMER id into
        // sessions.user_id, which the FK below rejects (it points to `users`).
        // Keep the index for lookups but drop the hard constraint.
        Schema::table('sessions', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table): void {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};
