<?php

namespace App\Enums;

enum UserProfile: string
{
    case Administrateur = 'administrateur';
    case Directeur = 'directeur';
    case AgentTransit = 'agent_transit';
    case AgentCommercial = 'agent_commercial';
    case Comptable = 'comptable';
    case ResponsableTransport = 'responsable_transport';
    case Consultation = 'consultation';
}
