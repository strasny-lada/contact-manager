<?php declare(strict_types = 1);

namespace App\Controller\Api;

use App\Repository\ContactRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/api/contact/")
 */
final class ContactApiController extends AbstractController
{

    /**
     * @Route("list/{page}", methods={"GET"}, requirements={"page"="\d+"})
     */
    public function list(
        ContactRepository $contactRepository,
        PaginatorInterface $paginator,
        TranslatorInterface $translator,
        RouterInterface $router,
        int $contactListItemsOnPage,
        int $page
    ): JsonResponse
    {
        try {
            /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination */
            $pagination = $paginator->paginate(
                $contactRepository->getFetchAllQuery(),
                $page,
                $contactListItemsOnPage
            );
        } catch (\OutOfRangeException $e) { // @phpstan-ignore-line (is never thrown in the corresponding try block)
            return new JsonResponse([
                'error' => [
                    'type' => 'pagination-out-of-range',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($pagination->count() === 0) {
            return new JsonResponse([
                'error' => [
                    'type' => 'result-out-of-range',
                    'message' => sprintf(
                        'Page number "%s" is out of range',
                        $page,
                    ),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $pagination->setUsedRoute('contact_list_page');

        $pageTitle = $translator->trans('app.title');
        if ($page > 1) {
            $pageTitle .= ' - ' . $translator->trans('app.contact.list.title', ['%page%' => $page]);
        }

        return new JsonResponse([
            'page' => [
                'url' => $router->generate('contact_list_page', [
                    'page' => $page,
                ]),
                'title' => $pageTitle,
                'content' => $this->renderView('contact/_list-table.html.twig', [
                    'pagination' => $pagination,
                ]),
            ],
        ]);
    }

}
