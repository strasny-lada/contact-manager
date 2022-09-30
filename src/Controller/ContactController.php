<?php declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ConfirmDeleteForm;
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

    public function __construct(
        private readonly FormFlashMessageStorage $flashMessageStorage,
        private readonly LoggerInterface $auditLogger,
    )
    {
    }

    /**
     * @Route("", methods={"GET"}, name="list", defaults={"page": 1})
     * @Route("strana/{page}", methods={"GET"}, name="list_page", requirements={"page"="\d+"})
     */
    public function list(
        ContactRepository $contactRepository,
        PaginatorInterface $paginator,
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
        Request $request,
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

    /**
     * @Route("{id}-{slug}", methods={"GET","POST"}, name="edit", requirements={"id"="\d+"})
     */
    public function edit(
        Contact $contact,
        ContactFacade $contactFacade,
        Request $request,
    ): Response
    {
        $contactRequest = ContactRequest::from($contact);

        $form = $this->createForm(ContactForm::class, $contactRequest, [
            'is_update' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $contactFacade->update(
                    $contact,
                    $contactRequest->firstname,
                    $contactRequest->lastname,
                    $contactRequest->email,
                    $contactRequest->phone,
                    $contactRequest->notice,
                );
            } catch (\Throwable $e) { // @phpstan-ignore-line (is never thrown in the corresponding try block)
                $this->auditLogger->error('Contact edit failed', [
                    'contactId' => $contact->getId(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace(),
                ]);

                $this->flashMessageStorage->addFailureWhenEdit($contact->getName());

                return $this->render('contact/edit.html.twig', [
                    'form' => $form->createView(),
                    'contact' => $contact,
                ]);
            }

            $this->flashMessageStorage->addEdited($contact->getName());

            return $this->redirectToRoute('contact_list');
        }

        return $this->render('contact/edit.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact,
        ]);
    }

    /**
     * @Route("{id}-{slug}/odstraneni", methods={"GET","POST"}, name="delete", requirements={"id"="\d+"})
     */
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
                    'contactId' => $contact->getId(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace(),
                ]);

                $this->flashMessageStorage->addFailureWhenDelete($contactName);

                return $this->redirectToRoute('contact_list');
            }

            $this->flashMessageStorage->addDeleted($contactName);

            return $this->redirectToRoute('contact_list');
        }

        return $this->render('contact/delete.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact,
        ]);
    }

}
