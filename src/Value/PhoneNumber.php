<?php declare(strict_types = 1);

namespace App\Value;

final class PhoneNumber
{

    private string $phone;

    private function __construct(string $phone)
    {
        $this->phone = $phone;
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
