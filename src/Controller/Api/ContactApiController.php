<?php declare(strict_types = 1);

namespace App\Controller\Api;

use App\Dto\ContactDto;
use App\Entity\Contact;
use App\Form\Api\CsrfTokenChecker;
use App\Form\Request\ContactRequest;
use App\Model\ContactFacade;
use App\Provider\ContactListDataProvider;
use App\Provider\ContactListPaginationProvider;
use App\Ui\FlashMessage\FormFlashMessageStorage;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/contact/')]
final class ContactApiController extends AbstractController
{

    public function __construct(
        private readonly FormFlashMessageStorage $flashMessageStorage,
        private readonly LoggerInterface $auditLogger,
    )
    {
    }

    /**
     * @throws \App\Exception\Api\BadRequestException
     */
    #[Route('list/{pageNumber}', requirements: ['pageNumber' => '\d+'], methods: ['GET'])]
    public function list(
        ContactListPaginationProvider $contactListPaginationProvider,
        ContactListDataProvider $contactListDataProvider,
        Request $request,
        int $pageNumber,
    ): Response
    {
        $pagination = $contactListPaginationProvider->providePaginationForApiRequest($pageNumber);

        if (
            $pagination->count() === 0 &&
            $pageNumber > $pagination->getPageCount()
        ) {
            $pageNumber = $pagination->getPageCount();
            $pagination = $contactListPaginationProvider->providePaginationForApiRequest($pageNumber);
        }

        if ($pagination->count() === 0) {
            throw new \App\Exception\Api\BadRequestException(
                'result-out-of-range',
            );
        }

        $pagination->setUsedRoute('contact_list_page');

        $request->getSession()->set(ContactFacade::PAGINATION_PAGE_HOLDER, $pageNumber);

        return new Response(
            $contactListDataProvider->provideSerializedContactListPageData(
                $pagination->getCurrentPageNumber(),
                (array) $pagination->getItems(),
                $pagination->getPaginationData(),
            ),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/json',
            ]
        );
    }

    #[Route('add-form', methods: ['GET'])]
    public function addForm(
        CsrfTokenManagerInterface $csrfTokenManager,
    ): JsonResponse
    {
        $csrfToken = $csrfTokenManager->getToken('contact_form')->getValue();

        return new JsonResponse([
            'csrf_token' => $csrfToken,
        ]);
    }

    /**
     * @throws \App\Exception\Api\ApiException
     */
    #[Route('add', methods: ['POST'])]
    public function add(
        ContactFacade $contactFacade,
        CsrfTokenChecker $csrfTokenChecker,
        ValidatorInterface $validator,
        Request $request,
    ): JsonResponse
    {
        $csrfTokenChecker->checkCsrfToken($request, 'contact_form');

        $contactRequest = ContactRequest::fromRequest($request, 'contact_form');

        $violations = $validator->validate($contactRequest);
        if ($violations->count() > 0) {
            throw new \App\Exception\Api\ApiRequestValidationException($violations);
        }

        try {
            $contact = $contactFacade->create(
                $contactRequest->firstname,
                $contactRequest->lastname,
                $contactRequest->email,
                $contactRequest->phone,
                $contactRequest->notice,
            );
        } catch (\Throwable $e) { // @phpstan-ignore-line (is never thrown in the corresponding try block)
            $this->auditLogger->error('Contact add failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);

            $this->flashMessageStorage->addFailureWhenAdd(
                sprintf(
                    '%s %s',
                    $contactRequest->lastname,
                    $contactRequest->firstname
                )
            );

            throw new \App\Exception\Api\ApiException(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e,
            );
        }

        $this->flashMessageStorage->addAdded($contact->getName());

        return new JsonResponse([
            'contact' => ContactDto::fromContact($contact)->toArray(),
        ], Response::HTTP_CREATED);
    }

    #[Route('edit-form/{slug}', methods: ['GET'])]
    public function editForm(
        Contact $contact,
        CsrfTokenManagerInterface $csrfTokenManager,
    ): JsonResponse
    {
        $csrfToken = $csrfTokenManager->getToken('contact_form')->getValue();

        return new JsonResponse([
            'contact' => ContactDto::fromContact($contact)->toArray(),
            'csrf_token' => $csrfToken,
        ]);
    }

    /**
     * @throws \App\Exception\Api\ApiException
     */
    #[Route('edit/{slug}', methods: ['PUT'])]
    public function edit(
        Contact $contact,
        ContactFacade $contactFacade,
        CsrfTokenChecker $csrfTokenChecker,
        ValidatorInterface $validator,
        Request $request,
    ): Response
    {
        $csrfTokenChecker->checkCsrfToken($request, 'contact_form');

        $contactRequest = ContactRequest::fromRequest($request, 'contact_form');

        $violations = $validator->validate($contactRequest);
        if ($violations->count() > 0) {
            throw new \App\Exception\Api\ApiRequestValidationException($violations);
        }

        try {
            $contact = $contactFacade->update(
                $contact,
                $contactRequest->firstname,
                $contactRequest->lastname,
                $contactRequest->email,
                $contactRequest->phone,
                $contactRequest->notice,
            );
        } catch (\Throwable $e) { // @phpstan-ignore-line (is never thrown in the corresponding try block)
            $this->auditLogger->error('Contact edit failed', [
                'contactId' => $contact->getId()->toString(),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);

            $this->flashMessageStorage->addFailureWhenEdit($contact->getName());

            throw new \App\Exception\Api\ApiException(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e,
            );
        }

        $this->flashMessageStorage->addEdited($contact->getName());

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('delete-form', methods: ['GET'])]
    public function deleteForm(
        CsrfTokenManagerInterface $csrfTokenManager,
    ): JsonResponse
    {
        $csrfToken = $csrfTokenManager->getToken('contact_delete_form')->getValue();

        return new JsonResponse([
            'csrf_token' => $csrfToken,
        ]);
    }

    /**
     * @throws \App\Exception\Api\ApiException
     */
    #[Route('delete/{slug}', methods: ['DELETE'])]
    public function delete(
        Contact $contact,
        ContactFacade $contactFacade,
        CsrfTokenChecker $csrfTokenChecker,
        Request $request,
    ): Response
    {
        $csrfTokenChecker->checkCsrfToken($request, 'contact_delete_form');

        try {
            $contactFacade->delete($contact);
        } catch (\Throwable $e) { // @phpstan-ignore-line (is never thrown in the corresponding try block)
            $this->auditLogger->error('Contact delete failed', [
                'contactId' => $contact->getId()->toString(),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);

            $this->flashMessageStorage->addFailureWhenDelete($contact->getName());

            throw new \App\Exception\Api\ApiException(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e,
            );
        }

        $this->flashMessageStorage->addDeleted($contact->getName());

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

}
