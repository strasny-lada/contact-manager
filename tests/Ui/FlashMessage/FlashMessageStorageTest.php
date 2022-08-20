<?php declare(strict_types = 1);

namespace App\Ui\FlashMessage;

use App\Fixtures\RequestStackFixture;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class FlashMessageStorageTest extends TestCase
{

    private FlashMessageStorage $flashMessageStorage;

    private Session $session;

    public function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->flashMessageStorage = new FlashMessageStorage(
            RequestStackFixture::createRequestStackWithSession($this->session)
        );
    }

    public function testAddAlertFlashMessage(): void
    {
        $this->flashMessageStorage->addAlertFlashMessage('test-message');

        self::assertSame(
            $this->session->getFlashBag()->all(),
            [
                'alert' => [
                    'test-message',
                ],
            ]
        );
    }

    public function testAddAlertFlashMessageCalledTwice(): void
    {
        $this->flashMessageStorage->addAlertFlashMessage('test-message');
        $this->flashMessageStorage->addAlertFlashMessage('test-message-2');

        self::assertSame(
            $this->session->getFlashBag()->all(),
            [
                'alert' => [
                    'test-message',
                    'test-message-2',
                ],
            ]
        );
    }

    public function testAddSuccessFlashMessageCalledTwice(): void
    {
        $this->flashMessageStorage->addSuccessFlashMessage('test-success-message');
        $this->flashMessageStorage->addSuccessFlashMessage('test-success-message-2');

        self::assertSame(
            $this->session->getFlashBag()->all(),
            [
                'success' => [
                    'test-success-message',
                    'test-success-message-2',
                ],
            ]
        );
    }

    public function testAddAllMessagesTypes(): void
    {
        $this->flashMessageStorage->addAlertFlashMessage('test-alert-message');
        $this->flashMessageStorage->addSuccessFlashMessage('test-success-message');
        $this->flashMessageStorage->addSuccessFlashMessage('test-success-message-2');
        $this->flashMessageStorage->addDangerFlashMessage('test-danger-message');

        self::assertSame(
            $this->session->getFlashBag()->all(),
            [
                'alert' => [
                    'test-alert-message',
                ],
                'success' => [
                    'test-success-message',
                    'test-success-message-2',
                ],
                'danger' => [
                    'test-danger-message',
                ],
            ]
        );
    }

}
