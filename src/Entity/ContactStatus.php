<?php declare(strict_types = 1);

namespace App\Entity;

enum ContactStatus : string
{

    case ACTIVE = 'active';
    case CLOSED = 'closed';

}
