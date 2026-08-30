<?php

namespace App\Enums;

enum NotificationStatut: string
{
    case EnFile = 'en_file';
    case Envoyee = 'envoyee';
    case Echec = 'echec';
}
