<?php declare(strict_types = 1);

namespace App\Value\Doctrine;

use App\Value\EmailAddress;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class EmailAddressType extends StringType
{

    public function getName(): string
    {
        return EmailAddress::class;
    }

    /**
     * @param \App\Value\EmailAddress|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string // phpcs:ignore
    {
        /** @var \App\Value\EmailAddress|null $value */ // phpcs:ignore SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration.MissingVariable
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
    public function convertToPHPValue($value, AbstractPlatform $platform): ?EmailAddress
    {
        if ($value === null) {
            return null;
        }

        return EmailAddress::fromString($value);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

}
