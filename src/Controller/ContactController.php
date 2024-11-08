<?php declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Contact;
use App\Model\ContactFacade;
use App\Provider\ContactFormDataProvider;
use App\Provider\ContactListPaginationProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/', name: 'contact_')]
final class ContactController extends AbstractController
{

    #[Route('', name: 'list', defaults: ['pageNumber' => 1], methods: ['GET'])]
    #[Route('strana/{pageNumber}', name: 'list_page', requirements: ['pageNumber' => '\d+'], methods: ['GET'])]
    public function list(
        ContactListPaginationProvider $contactListPaginationProvider,
        Request $request,
        int $pageNumber,
    ): Response
    {
        $pagination = $contactListPaginationProvider->providePagination($pageNumber);

        // resolve the situation where the current page disappears after deleting a contact
        if ($pageNumber > $pagination->getPageCount()) {
            $pageNumber = $pagination->getPageCount();
        }

        $request->getSession()->set(ContactFacade::PAGINATION_PAGE_HOLDER, $pageNumber);

        return $this->render('contact/list.html.twig', [
            'pageNumber' => $pageNumber,
        ]);
    }

    #[Route('pridat-kontakt', name: 'add', methods: ['GET'])]
    public function add(
        ContactFormDataProvider $contactFormDataProvider,
        CsrfTokenManagerInterface $csrfTokenManager,
    ): Response
    {
        $csrfToken = $csrfTokenManager->getToken('contact_form')->getValue();

        $contactFormDataDto = $contactFormDataProvider->provideContactFormData(null);

        return $this->render('contact/add.html.twig', [
            'texts' => $contactFormDataDto->getTexts(),
            'csrf_token' => $csrfToken,
        ]);
    }

    #[Route('{slug}', name: 'edit', methods: ['GET'])]
    public function edit(
        Contact $contact,
        ContactFormDataProvider $contactFormDataProvider,
        CsrfTokenManagerInterface $csrfTokenManager,
    ): Response
    {
        $csrfToken = $csrfTokenManager->getToken('contact_form')->getValue();

        $contactFormDataDto = $contactFormDataProvider->provideContactFormData($contact);

        if ($contactFormDataDto->getContactDto() === null) {
            throw new \Exception('Contact DTO should not be null at this point');
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contactFormDataDto->getContactDto()->toArray(),
            'texts' => $contactFormDataDto->getTexts(),
            'csrf_token' => $csrfToken,
        ]);
    }

}
