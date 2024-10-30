<?php declare(strict_types = 1);

namespace App\Dto;

// phpcs:disable PSR1.Files.SideEffects
final readonly class ContactListPageDto
{

    /**
     * @param \App\Dto\ContactDto[] $items
     * @param array<string, mixed> $paginationData
     */
    public function __construct(
        private int $number,
        private string $title,
        private string $url,
        private array $items,
        private array $paginationData,
    )
    {
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return \App\Dto\ContactDto[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPaginationData(): array
    {
        return $this->paginationData;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'title' => $this->title,
            'url' => $this->url,
            'items' => array_map(function (ContactDto $contactDto) {
                return $contactDto->toArray();
            }, $this->items),
            'paginationData' => $this->paginationData,
        ];
    }

}
