<?php declare(strict_types = 1);

namespace App\Fixtures;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

final class RequestStackFixture
{

    public static function createRequestStackWithSession(
        ?SessionInterface $session = null
    ): RequestStack
    {
        if ($session === null) {
            $session = new Session(new MockArraySessionStorage());
        }

        $requestStack = new RequestStack();

        $request = new Request();
        $request->setSession($session);

        $requestStack->push($request);

        return $requestStack;
    }

}
