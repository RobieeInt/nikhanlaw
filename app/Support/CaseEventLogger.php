<?php

namespace App\Support;

use App\Models\LegalCase;
use App\Models\CaseEvent;

class CaseEventLogger
{
    public static function log(
        LegalCase $case,
        string $event,
        ?string $note = null,
        ?string $statusFrom = null,
        ?string $statusTo = null,
        array $meta = []
    ): CaseEvent {
        $user = auth()->user();

        return $case->events()->create([
            'actor_id' => $user?->id,
            'actor_role' => $user?->getRoleNames()?->first() ?? null, // spatie
            'event' => $event,
            'status_from' => $statusFrom,
            'status_to' => $statusTo,
            'note' => $note,
            'meta' => $meta ?: null,
        ]);
    }
}
