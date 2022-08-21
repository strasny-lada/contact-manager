<?php declare(strict_types = 1);

namespace App\Exception\Api;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiException extends \App\Exception\PhpException implements HttpExceptionInterface // phpcs:ignore Consistence.Exceptions.ExceptionDeclaration.IncorrectExceptionDirectory
{

    public function __construct(
        string $message,
        int $code,
        ?\Throwable $previous = null
    )
    {
        parent::__construct(
            $message,
            $code,
            $previous,
        );
    }

    public function getStatusCode(): int
    {
        return $this->getCode();
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return [];
    }

}
