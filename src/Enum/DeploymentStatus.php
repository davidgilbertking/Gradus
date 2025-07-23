<?php

namespace App\Enum;

enum DeploymentStatus: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
    case LOCKED = 'locked';
}
