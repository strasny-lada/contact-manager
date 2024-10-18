<?php declare(strict_types = 1);

namespace App\Value;

// phpcs:disable PSR1.Files.SideEffects
final readonly class EmailAddress
{

    private string $emailAddress;

    private function __construct(
        string $emailAddress,
    )
    {
        $this->emailAddress = mb_strtolower($emailAddress);
    }

    public static function fromString(string $emailAddress): self
    {
        if (!self::isValidEmailAddress($emailAddress)) {
            throw new \Exception(sprintf('Invalid email address "%s"', $emailAddress));
        }

        return new self($emailAddress);
    }

    public static function isValidEmailAddress(string $emailAddress): bool
    {
        return filter_var($emailAddress, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function getDomain(): string
    {
        if (!self::isValidEmailAddress($this->emailAddress)) {
            throw new \Exception(sprintf('Invalid email address "%s"', $this->emailAddress));
        }

        $parts = explode('@', $this->emailAddress);

        return array_pop($parts);
    }

    public function toString(): string
    {
        return $this->emailAddress;
    }

}
