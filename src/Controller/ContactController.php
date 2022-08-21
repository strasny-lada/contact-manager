<?php declare(strict_types = 1);

namespace App\Controller;

use App\Repository\ContactRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="contact_")
 */
final class ContactController extends AbstractController
{

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

}
