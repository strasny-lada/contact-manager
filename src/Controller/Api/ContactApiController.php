<?php declare(strict_types = 1);

namespace App\Controller\Api;

use App\Repository\ContactRepository;
use App\Serializer\ContactSerializer;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @throws \App\Exception\Api\BadRequestException
     */
    public function list(
        ContactRepository $contactRepository,
        ContactSerializer $contactSerializer,
        PaginatorInterface $paginator,
        TranslatorInterface $translator,
        RouterInterface $router,
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

}
