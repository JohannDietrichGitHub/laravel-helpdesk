<?php

namespace App\Enums;

enum Role: string
{
    case CLIENT = 'client';
    case AGENT = 'agent';
    case ADMIN = 'admin';
}
