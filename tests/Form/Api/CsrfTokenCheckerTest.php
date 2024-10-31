<?php declare(strict_types = 1);

namespace App\Form\Api;

use Monolog\Test\TestCase;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class CsrfTokenCheckerTest extends TestCase
{

    public function testCsrfTokenIsValid(): void
    {
        $csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $csrfTokenManager
            ->expects(self::once())
            ->method('isTokenValid')
            ->willReturn(true);

        $csrfTokenChecker = new CsrfTokenChecker($csrfTokenManager);

        $requestData = ['_token' => '--csrf_token--'];

        $csrfTokenChecker->checkCsrfToken($requestData, 'contact_form');
    }

    public function testCsrfTokenCannotBeCheckedWithEmptyRequest(): void
    {
        $csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $csrfTokenChecker = new CsrfTokenChecker($csrfTokenManager);

        $requestData = [];

        self::expectException(\App\Exception\Api\BadRequestException::class);
        self::expectExceptionMessage('CSRF token should be defined at this point');

        $csrfTokenChecker->checkCsrfToken($requestData, 'contact_form');
    }

    public function testCsrfTokenIsInvalid(): void
    {
        $csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $csrfTokenManager
            ->expects(self::once())
            ->method('isTokenValid')
            ->willReturn(false);

        $csrfTokenChecker = new CsrfTokenChecker($csrfTokenManager);

        $requestData = ['_token' => '--csrf_token--'];

        self::expectException(\App\Exception\Api\ApiRequestValidationException::class);
        self::expectExceptionMessage('Invalid CSRF token');

        $csrfTokenChecker->checkCsrfToken($requestData, 'contact_form');
    }

}
