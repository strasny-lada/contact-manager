<?php declare(strict_types = 1);

namespace App\Ui\FlashMessage;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

// phpcs:disable PSR1.Files.SideEffects
final readonly class FlashMessageStorage
{

    public function __construct(
        private RequestStack $requestStack,
    )
    {
    }

    public function addDangerFlashMessage(string $message): void
    {
        $this->addFlashMessage(
            FlashMessageType::DANGER,
            $message
        );
    }

    public function addAlertFlashMessage(string $message): void
    {
        $this->addFlashMessage(
            FlashMessageType::ALERT,
            $message
        );
    }

    public function addSuccessFlashMessage(string $message): void
    {
        $this->addFlashMessage(
            FlashMessageType::SUCCESS,
            $message
        );
    }

    private function addFlashMessage(
        FlashMessageType $flashMessageType,
        string $message,
    ): void
    {
        $session = $this->requestStack->getSession();
        if (!$session instanceof Session) {
            throw new \Exception(sprintf('Expected "%s" got "%s"', Session::class, $session::class));
        }

        $session->getFlashBag()->add($flashMessageType->getType(), $message);
    }

}
