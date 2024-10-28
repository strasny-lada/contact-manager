<?php declare(strict_types = 1);

namespace App\Form\Api;

use Symfony\Component\HttpFoundation\Request;
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
     * @throws \App\Exception\Api\ApiRequestValidationException
     * @throws \App\Exception\Api\BadRequestException
     */
    public function checkCsrfToken(
        Request $request,
        string $formName,
    ): void
    {
        $formData = $request->request->all()[$formName] ?? null;
        if ($formData === null) {
            throw new \App\Exception\Api\BadRequestException(
                'Form data not found',
            );
        }

        $csrfToken = $formData['_token'] ?? null;

        if (
            $csrfToken === null ||
            !$this->csrfTokenManager->isTokenValid(new CsrfToken($formName, $csrfToken))
        ) {
            $constraintViolationList = new ConstraintViolationList();
            $constraintViolationList->add(new ConstraintViolation('Invalid CSRF token', null, [], null, null, null));
            throw new \App\Exception\Api\ApiRequestValidationException($constraintViolationList);
        }
    }

}
