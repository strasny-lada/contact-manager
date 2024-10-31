<?php declare(strict_types = 1);

namespace App\Provider;

use App\Dto\ContactDto;
use App\Dto\ContactListPageDto;
use App\Entity\Contact;
use App\Serializer\ContactSerializer;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

// phpcs:disable PSR1.Files.SideEffects
final readonly class ContactListDataProvider
{

    public function __construct(
        private ContactSerializer $contactSerializer,
        private TranslatorInterface $translator,
        private RouterInterface $router,
    )
    {
    }

    /**
     * @param \App\Entity\Contact[] $pageItems
     * @param array<string, mixed> $paginationData
     */
    public function provideSerializedContactListPageData(
        int $pageNumber,
        array $pageItems,
        array $paginationData,
    ): string
    {
        $pageTitle = $this->translator->trans('app.title');
        if ($pageNumber > 1) {
            $pageTitle .= ' - ' . $this->translator->trans('app.contact.list.title', ['%page%' => $pageNumber]);
        }

        if ($pageNumber === 1) {
            $pageUrl = $this->router->generate('contact_list');
        } else {
            $pageUrl = $this->router->generate('contact_list_page', [
                'pageNumber' => $pageNumber,
            ]);
        }

        $contactListPageDto = new ContactListPageDto(
            $pageNumber,
            $pageTitle,
            $pageUrl,
            array_map(function (Contact $contact) {
                return ContactDto::fromContact($contact);
            }, $pageItems),
            $paginationData,
        );

        return $this->contactSerializer->serializeContactListPageToJson(
            $contactListPageDto,
            [
                'app.contact.list.empty' => $this->translator->trans('app.contact.list.empty'),
                'app.contact.name' => $this->translator->trans('app.contact.name'),
                'app.contact.email' => $this->translator->trans('app.contact.email'),
                'app.contact.phone' => $this->translator->trans('app.contact.phone'),
                'app.contact.notice' => $this->translator->trans('app.contact.notice'),
                'app.pagination.previous' => $this->translator->trans('label_previous', [], 'KnpPaginatorBundle'),
                'app.pagination.next' => $this->translator->trans('label_next', [], 'KnpPaginatorBundle'),
                'app.form.add' => $this->translator->trans('app.form.add'),
            ],
            [
                'contact_add' => $this->router->generate('contact_add'),
            ],
        );
    }

}
