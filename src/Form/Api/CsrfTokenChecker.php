<?php declare(strict_types = 1);

namespace App\Form\Api;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

// phpcs:disable PSR1.Files.SideEffects
final readonly class CsrfTokenChecker
{

    public function __construct(
        private CsrfTokenManagerInterface $csrfTokenManager,
    )
    {
    }

    /**
     * @param array<string, mixed> $requestData
     * @throws \App\Exception\Api\ApiRequestValidationException
     * @throws \App\Exception\Api\BadRequestException
     */
    public function checkCsrfToken(
        array $requestData,
        string $formName,
    ): void
    {
        $requestCsrfToken = $requestData['_token'] ?? null;
        if ($requestCsrfToken === null) {
            throw new \App\Exception\Api\BadRequestException(
                'CSRF token should be defined at this point',
            );
        }

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($formName, $requestCsrfToken))) {
            $constraintViolationList = new ConstraintViolationList();
            $constraintViolationList->add(new ConstraintViolation('Invalid CSRF token', null, [], null, null, null));
            throw new \App\Exception\Api\ApiRequestValidationException($constraintViolationList);
        }
    }

}
