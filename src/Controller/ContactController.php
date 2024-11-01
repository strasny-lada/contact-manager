<?php declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ConfirmDeleteForm;
use App\Model\ContactFacade;
use App\Provider\ContactFormDataProvider;
use App\Provider\ContactListPaginationProvider;
use App\Ui\FlashMessage\FormFlashMessageStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/', name: 'contact_')]
final class ContactController extends AbstractController
{

    public function __construct(
        private readonly FormFlashMessageStorage $flashMessageStorage,
        private readonly LoggerInterface $auditLogger,
    )
    {
    }

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

    #[Route('{slug}/odstraneni', name: 'delete', methods: ['GET', 'POST'])]
    public function delete(
        Contact $contact,
        ContactFacade $contactFacade,
        Request $request,
    ): Response
    {
        $form = $this->createForm(ConfirmDeleteForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactName = $contact->getName();

            try {
                $contactFacade->delete($contact);
            } catch (\Throwable $e) { // @phpstan-ignore-line (is never thrown in the corresponding try block)
                $this->auditLogger->error('Contact delete failed', [
                    'contactId' => $contact->getId()->toString(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace(),
                ]);

                $this->flashMessageStorage->addFailureWhenDelete($contactName);

                return $this->redirectToRoute('contact_list');
            }

            $this->flashMessageStorage->addDeleted($contactName);

            // hold pagination state
            $page = $request->getSession()->get(ContactFacade::PAGINATION_PAGE_HOLDER);
            if ($page > 1) {
                return $this->redirectToRoute('contact_list_page', ['pageNumber' => $page]);
            }

            return $this->redirectToRoute('contact_list');
        }

        return $this->render('contact/delete.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact,
        ]);
    }

}
