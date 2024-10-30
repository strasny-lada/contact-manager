<?php declare(strict_types = 1);

namespace App\Provider;

use App\Repository\ContactRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;

// phpcs:disable PSR1.Files.SideEffects
final readonly class ContactListPaginationProvider
{

    public function __construct(
        private PaginatorInterface $paginator,
        private ContactRepository $contactRepository,
        private int $contactListItemsOnPage,
    )
    {
    }

    public function providePagination(
        int $pageNumber,
    ): SlidingPagination
    {
        try {
            /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination */
            $pagination = $this->paginate($pageNumber);
        } catch (\OutOfRangeException $e) { // @phpstan-ignore-line (is never thrown in the corresponding try block)
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException($e->getMessage(), $e);
        }

        return $pagination;
    }

    /**
     * @throws \App\Exception\Api\BadRequestException
     */
    public function providePaginationForApiRequest(
        int $pageNumber,
    ): SlidingPagination
    {
        try {
            /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination */
            $pagination = $this->paginate($pageNumber);
        } catch (\OutOfRangeException $e) { // @phpstan-ignore-line (is never thrown in the corresponding try block)
            throw new \App\Exception\Api\BadRequestException(
                'pagination-out-of-range',
                $e,
            );
        }

        return $pagination;
    }

    private function paginate(
        int $pageNumber,
    ): SlidingPagination
    {
        /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination */
        $pagination = $this->paginator->paginate(
            $this->contactRepository->getFetchAllQuery(),
            $pageNumber,
            $this->contactListItemsOnPage
        );

        return $pagination;
    }

}
