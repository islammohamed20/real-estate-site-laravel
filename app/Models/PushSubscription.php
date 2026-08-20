<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use NotificationChannels\WebPush\PushSubscription as WebPushSubscription;

class PushSubscription extends WebPushSubscription
{
    /**
     * Override the subscribable relationship to use a direct
     * user_id foreign key (BelongsTo) instead of the package's
     * polymorphic MorphTo.
     */
    public function user(): HasOne|HasMany
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
