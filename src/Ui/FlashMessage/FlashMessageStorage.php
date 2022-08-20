<?php declare(strict_types = 1);

namespace App\Ui\FlashMessage;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

final class FlashMessageStorage
{

    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function addDangerFlashMessage(string $message): void
    {
        $this->addFlashMessage(
            FlashMessageType::get(FlashMessageType::DANGER),
            $message
        );
    }

    public function addAlertFlashMessage(string $message): void
    {
        $this->addFlashMessage(
            FlashMessageType::get(FlashMessageType::ALERT),
            $message
        );
    }

    public function addSuccessFlashMessage(string $message): void
    {
        $this->addFlashMessage(
            FlashMessageType::get(FlashMessageType::SUCCESS),
            $message
        );
    }

    private function addFlashMessage(FlashMessageType $flashMessageType, string $message): void
    {
        $session = $this->requestStack->getSession();
        if (!$session instanceof Session) {
            throw new \Exception(sprintf('Expected "%s" got "%s"', Session::class, get_class($session)));
        }

        $session->getFlashBag()->add($flashMessageType->getValue(), $message);
    }

}
