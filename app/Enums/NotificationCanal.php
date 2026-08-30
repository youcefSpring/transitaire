<?php

namespace App\Enums;

enum NotificationCanal: string
{
    case Email = 'email';
    case Sms = 'sms';
    case Whatsapp = 'whatsapp';
}
