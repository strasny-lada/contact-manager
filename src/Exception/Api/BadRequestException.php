<?php declare(strict_types = 1);

namespace App\Exception\Api;

use Symfony\Component\HttpFoundation\Response;

final class BadRequestException extends \App\Exception\Api\ApiException // phpcs:ignore Consistence.Exceptions.ExceptionDeclaration.IncorrectExceptionDirectory
{

    public function __construct(
        string $message,
        ?\Throwable $previous = null
    )
    {
        parent::__construct(
            $message,
            Response::HTTP_BAD_REQUEST,
            $previous,
        );
    }

}
