<?php declare(strict_types = 1);

namespace App\Ui\FlashMessage;

enum FlashMessageType: string
{

    case ALERT = 'alert';
    case DANGER = 'danger';
    case SUCCESS = 'success';

    public function getType(): string
    {
        return $this->value;
    }

}
