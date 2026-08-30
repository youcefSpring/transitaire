<?php

namespace App\Enums;

enum ModeTransport: string
{
    case Maritime = 'maritime';
    case Aerien = 'aerien';
    case Terrestre = 'terrestre';
}
