<?php declare(strict_types = 1);

namespace App\Ui\FlashMessage;

use App\Fixtures\RequestStackFixture;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormFlashMessageStorageTest extends TestCase
{

    private FormFlashMessageStorage $formFlashMessageStorage;

    private Session $session;

    public function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());

        /** @var \Symfony\Contracts\Translation\TranslatorInterface&\PHPUnit\Framework\MockObject\MockObject $translator */
        $translator = $this->getMockBuilder(TranslatorInterface::class)
            ->onlyMethods(['trans'])
            ->getMock();
        $translator->method('trans')->willReturnCallback(
            fn (string $key, array $args): string => $key . '___' . implode('|', $args)
        );

        $this->formFlashMessageStorage = new FormFlashMessageStorage(
            new FlashMessageStorage(
                RequestStackFixture::createRequestStackWithSession($this->session)
            ),
            $translator
        );
    }

    public function testAddSuccessFlashMessageAdded(): void
    {
        $this->formFlashMessageStorage->addAdded('test-item-name');

        self::assertSame(
            $this->session->getFlashBag()->all(),
            [
                'success' => [
                    'app.form.flash_message.added.success___test-item-name',
                ],
            ]
        );
    }

    public function testAddFailureFlashMessageAdded(): void
    {
        $this->formFlashMessageStorage->addFailureWhenAdd('test-item-name');

        self::assertSame(
            $this->session->getFlashBag()->all(),
            [
                'danger' => [
                    'app.form.flash_message.added.failed___test-item-name',
                ],
            ]
        );
    }

    public function testAddFlashMessageEdited(): void
    {
        $this->formFlashMessageStorage->addEdited('test-item-name');

        self::assertSame(
            $this->session->getFlashBag()->all(),
            [
                'success' => [
                    'app.form.flash_message.edited.success___test-item-name',
                ],
            ]
        );
    }

    public function testAddFailureFlashMessageEdited(): void
    {
        $this->formFlashMessageStorage->addFailureWhenEdit('test-item-name');

        self::assertSame(
            $this->session->getFlashBag()->all(),
            [
                'danger' => [
                    'app.form.flash_message.edited.failed___test-item-name',
                ],
            ]
        );
    }

    public function testAddFlashMessageDeleted(): void
    {
        $this->formFlashMessageStorage->addDeleted('test-item-name');

        self::assertSame(
            $this->session->getFlashBag()->all(),
            [
                'success' => [
                    'app.form.flash_message.deleted.success___test-item-name',
                ],
            ]
        );
    }

    public function testAddFailureFlashMessageDeleted(): void
    {
        $this->formFlashMessageStorage->addFailureWhenDelete('test-item-name');

        self::assertSame(
            $this->session->getFlashBag()->all(),
            [
                'danger' => [
                    'app.form.flash_message.deleted.failed___test-item-name',
                ],
            ]
        );
    }

    public function testAddSuccessWithParameters(): void
    {
        $this->formFlashMessageStorage->addSuccessWithParameters(
            'item-was-edited-sucessfully',
            [
                '%itemName%' => 'Item 1',
            ]
        );

        self::assertSame(
            $this->session->getFlashBag()->all(),
            [
                'success' => [
                    'item-was-edited-sucessfully___Item 1',
                ],
            ]
        );
    }

    public function testAddFlashMessageAddedCalledTwice(): void
    {
        $this->formFlashMessageStorage->addAdded('test-item-name');
        $this->formFlashMessageStorage->addAdded('test-item-name-2');

        self::assertSame(
            $this->session->getFlashBag()->all(),
            [
                'success' => [
                    'app.form.flash_message.added.success___test-item-name',
                    'app.form.flash_message.added.success___test-item-name-2',
                ],
            ]
        );
    }

}
