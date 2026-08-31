<?php

namespace App\Console\Commands;

use App\Services\AlerteService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('alertes:generer')]
#[Description('Génère les alertes opérationnelles et met les notifications clients en file')]
class GenererAlertesCommand extends Command
{
    public function handle(): int
    {
        $creees = app(AlerteService::class)->genererToutes();

        $this->info("{$creees} nouvelle(s) alerte(s) générée(s).");

        return self::SUCCESS;
    }
}
