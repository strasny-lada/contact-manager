<?php declare(strict_types = 1);

namespace App\Value;

// phpcs:disable PSR1.Files.SideEffects
final readonly class PhoneNumber implements \Stringable
{

    private function __construct(
        private string $phone,
    )
    {
    }

    public static function fromString(string $phone): self
    {
        $cleanedValue = trim($phone);

        if ($cleanedValue === '') {
            throw new \Exception(sprintf('Invalid phone number "%s"', $phone));
        }

        return new self($cleanedValue);
    }

    public function toString(): string
    {
        return $this->phone;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}
