<?php declare(strict_types = 1);

namespace App\Ui\FlashMessage;

use Consistence\Enum\Enum;

final class FlashMessageType extends Enum
{

    public const ALERT = 'alert';
    public const DANGER = 'danger';
    public const SUCCESS = 'success';

}
