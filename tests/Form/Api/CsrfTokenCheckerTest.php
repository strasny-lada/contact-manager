<?php declare(strict_types = 1);

namespace App\Form\Api;

use Monolog\Test\TestCase;
use Symfony\Component\HttpFoundation\Request;
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

        $request = new Request([], ['contact_form' => ['_token' => '--csrf_token--']]);

        $csrfTokenChecker->checkCsrfToken($request, 'contact_form');
    }

    public function testCsrfTokenCannotBeCheckedWithEmptyRequest(): void
    {
        $csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $csrfTokenChecker = new CsrfTokenChecker($csrfTokenManager);

        $request = new Request([], []);

        self::expectException(\App\Exception\Api\BadRequestException::class);
        self::expectExceptionMessage('Form data not found');

        $csrfTokenChecker->checkCsrfToken($request, 'contact_form');
    }

    public function testCsrfTokenIsInvalid(): void
    {
        $csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $csrfTokenManager
            ->expects(self::once())
            ->method('isTokenValid')
            ->willReturn(false);

        $csrfTokenChecker = new CsrfTokenChecker($csrfTokenManager);

        $request = new Request([], ['contact_form' => ['_token' => '--csrf_token--']]);

        self::expectException(\App\Exception\Api\ApiRequestValidationException::class);
        self::expectExceptionMessage('Invalid CSRF token');

        $csrfTokenChecker->checkCsrfToken($request, 'contact_form');
    }

}
