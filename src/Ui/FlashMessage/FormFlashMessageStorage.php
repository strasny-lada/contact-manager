<?php declare(strict_types = 1);

namespace App\Ui\FlashMessage;

use Symfony\Contracts\Translation\TranslatorInterface;

// phpcs:disable PSR1.Files.SideEffects
final readonly class FormFlashMessageStorage
{

    public function __construct(
        private FlashMessageStorage $flashMessageStorage,
        private TranslatorInterface $translator,
    )
    {
    }

    /**
     * @param string $messageTranslatorKey #TranslationKey
     */
    public function addSuccessByTranslationKey(string $messageTranslatorKey): void
    {
        $this->flashMessageStorage->addSuccessFlashMessage(
            $this->translator->trans($messageTranslatorKey)
        );
    }

    public function addSuccess(string $translatedMessage): void
    {
        $this->flashMessageStorage->addSuccessFlashMessage($translatedMessage);
    }

    /**
     * @param string $messageTranslatorKey #TranslationKey
     * @param mixed[] $parameters
     */
    public function addSuccessWithParameters(
        string $messageTranslatorKey,
        array $parameters,
    ): void
    {
        $this->addSuccess(
            $this->translator->trans($messageTranslatorKey, $parameters)
        );
    }

    /**
     * @param string $messageTranslatorKey #TranslationKey
     */
    public function addDangerByTranslationKey(string $messageTranslatorKey): void
    {
        $this->flashMessageStorage->addDangerFlashMessage(
            $this->translator->trans($messageTranslatorKey)
        );
    }

    /**
     * @param string $messageTranslatorKey #TranslationKey
     * @param mixed[] $parameters
     */
    public function addDangerWithParameters(
        string $messageTranslatorKey,
        array $parameters,
    ): void
    {
        $this->flashMessageStorage->addDangerFlashMessage(
            $this->translator->trans($messageTranslatorKey, $parameters)
        );
    }

    public function addDanger(string $translatedMessage): void
    {
        $this->flashMessageStorage->addDangerFlashMessage($translatedMessage);
    }

    public function addAdded(string $itemName): void
    {
        $this->addSuccess(
            $this->translator->trans(
                'app.form.flash_message.added.success',
                [
                    '%added_item%' => $itemName,
                ]
            )
        );
    }

    public function addFailureWhenAdd(string $itemName): void
    {
        $this->addDanger(
            $this->translator->trans(
                'app.form.flash_message.added.failed',
                [
                    '%added_item%' => $itemName,
                ]
            )
        );
    }

    public function addEdited(string $itemName): void
    {
        $this->addSuccess(
            $this->translator->trans(
                'app.form.flash_message.edited.success',
                [
                    '%edited_item%' => $itemName,
                ]
            )
        );
    }

    public function addFailureWhenEdit(string $itemName): void
    {
        $this->addDanger(
            $this->translator->trans(
                'app.form.flash_message.edited.failed',
                [
                    '%edited_item%' => $itemName,
                ]
            )
        );
    }

    public function addDeleted(string $itemName): void
    {
        $this->addSuccess(
            $this->translator->trans(
                'app.form.flash_message.deleted.success',
                [
                    '%deleted_item%' => $itemName,
                ]
            )
        );
    }

    public function addFailureWhenDelete(string $itemName): void
    {
        $this->addDanger(
            $this->translator->trans(
                'app.form.flash_message.deleted.failed',
                [
                    '%deleted_item%' => $itemName,
                ]
            )
        );
    }

}
