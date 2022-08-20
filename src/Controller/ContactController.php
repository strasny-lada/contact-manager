<?php declare(strict_types = 1);

namespace App\Controller;

use App\Form\ContactForm;
use App\Form\Request\ContactRequest;
use App\Model\ContactFacade;
use App\Repository\ContactRepository;
use App\Ui\FlashMessage\FormFlashMessageStorage;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="contact_")
 */
final class ContactController extends AbstractController
{

    private FormFlashMessageStorage $flashMessageStorage;

    private LoggerInterface $auditLogger;

    public function __construct(
        FormFlashMessageStorage $flashMessageStorage,
        LoggerInterface $auditLogger
    )
    {
        $this->flashMessageStorage = $flashMessageStorage;
        $this->auditLogger = $auditLogger;
    }

    /**
     * @Route("", methods={"GET"}, name="list", defaults={"page": 1})
     * @Route("strana/{page}", methods={"GET"}, name="list_page", requirements={"page"="\d+"})
     */
    public function list(
        ContactRepository $contactRepository,
        PaginatorInterface $paginator,
        int $contactListItemsOnPage,
        int $page
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
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException($e->getMessage(), $e);
        }

        $pagination->setUsedRoute('contact_list_page');

        return $this->render('contact/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("pridat-kontakt", methods={"GET","POST"}, name="add")
     */
    public function add(
        ContactFacade $contactFacade,
        Request $request
    ): Response
    {
        $contactRequest = new ContactRequest();

        $form = $this->createForm(ContactForm::class, $contactRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

                return $this->render('contact/add.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $this->flashMessageStorage->addAdded($contact->getName());

            return $this->redirectToRoute('contact_list');
        }

        return $this->render('contact/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
