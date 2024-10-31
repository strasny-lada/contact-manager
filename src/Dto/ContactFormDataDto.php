<?php declare(strict_types = 1);

namespace App\Dto;

// phpcs:disable PSR1.Files.SideEffects
final readonly class ContactFormDataDto
{

    /**
     * @param array<string, string> $texts
     */
    public function __construct(
        private ?ContactDto $contactDto,
        private array $texts,
    )
    {
    }

    public function getContactDto(): ?ContactDto
    {
        return $this->contactDto;
    }

    /**
     * @return array<string, string>
     */
    public function getTexts(): array
    {
        return $this->texts;
    }

}
