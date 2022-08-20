<?php declare(strict_types = 1);

namespace App\Value\Doctrine;

use App\Value\PhoneNumber;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class PhoneNumberType extends StringType
{

    public function getName(): string
    {
        return PhoneNumber::class;
    }

    /**
     * @param \App\Value\PhoneNumber|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string // phpcs:ignore
    {
        /** @var \App\Value\PhoneNumber|null $value */ // phpcs:ignore SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration.MissingVariable
        if ($value === null) {
            return null;
        }

        return $value->toString();
    }

    /**
     * @param string|null $value
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?PhoneNumber
    {
        if ($value === null) {
            return null;
        }

        return PhoneNumber::fromString($value);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

}
