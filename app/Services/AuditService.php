<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Dossier;
use App\Models\User;

class AuditService
{
    public function journaliser(
        User $user,
        string $action,
        ?Dossier $dossier = null,
        ?string $entiteType = null,
        ?int $entiteId = null,
        ?string $details = null,
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'dossier_id' => $dossier?->id,
            'entite_type' => $entiteType,
            'entite_id' => $entiteId,
            'details' => $details,
        ]);
    }
}
