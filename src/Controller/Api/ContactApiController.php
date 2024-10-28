<?php declare(strict_types = 1);

namespace App\Controller\Api;

use App\Dto\ContactDto;
use App\Form\Api\CsrfTokenChecker;
use App\Form\Request\ContactRequest;
use App\Model\ContactFacade;
use App\Repository\ContactRepository;
use App\Serializer\ContactSerializer;
use App\Ui\FlashMessage\FormFlashMessageStorage;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    #[Route('list/{page}', requirements: ['page' => '\d+'], methods: ['GET'])]
    public function list(
        ContactRepository $contactRepository,
        ContactSerializer $contactSerializer,
        PaginatorInterface $paginator,
        TranslatorInterface $translator,
        RouterInterface $router,
        Request $request,
        int $contactListItemsOnPage,
        int $page,
    ): Response
    {
        try {
            /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination */
            $pagination = $paginator->paginate(
                $contactRepository->getFetchAllQuery(),
                $page,
                $contactListItemsOnPage
            );
        } catch (\OutOfRangeException $e) { // @phpstan-ignore-line (is never thrown in the corresponding try block)
            throw new \App\Exception\Api\BadRequestException(
                'pagination-out-of-range',
                $e,
            );
        }

        if ($pagination->count() === 0) {
            throw new \App\Exception\Api\BadRequestException(
                'result-out-of-range',
            );
        }

        $pagination->setUsedRoute('contact_list_page');

        $pageTitle = $translator->trans('app.title');
        if ($page > 1) {
            $pageTitle .= ' - ' . $translator->trans('app.contact.list.title', ['%page%' => $page]);
        }

        if ($page === 1) {
            $pageUrl = $router->generate('contact_list');
        } else {
            $pageUrl = $router->generate('contact_list_page', [
                'page' => $page,
            ]);
        }

        $request->getSession()->set(ContactFacade::PAGINATION_PAGE_HOLDER, $page);

        return new Response(
            $contactSerializer->serializeContactListPageToJson(
                $pageUrl,
                $pageTitle,
                $this->renderView('contact/_list-table.html.twig', [
                    'pagination' => $pagination,
                ]),
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

}
