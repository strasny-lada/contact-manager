<?php declare(strict_types = 1);

namespace App\Exception\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ApiRequestValidationException extends \App\Exception\Api\ApiException // phpcs:ignore Consistence.Exceptions.ExceptionDeclaration.IncorrectExceptionDirectory
{

    public function __construct(
        private ConstraintViolationListInterface $constraintViolationList,
        ?\Throwable $previous = null
    )
    {
        $message = 'Validation failed';

        if ($constraintViolationList instanceof ConstraintViolationList) {
            $violations = [];

            foreach ($constraintViolationList->getIterator() as $violation) {
                $violations[$violation->getPropertyPath()] = $violation->getMessage();
            }

            $message = json_encode($violations, JSON_THROW_ON_ERROR);
        }

        parent::__construct(
            $message,
            Response::HTTP_BAD_REQUEST,
            $previous,
        );
    }

    public function getConstraintViolationList(): ConstraintViolationListInterface
    {
        return $this->constraintViolationList;
    }

}
