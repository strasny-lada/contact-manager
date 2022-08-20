<?php declare(strict_types = 1);

namespace App\Value\Doctrine;

use App\Value\EmailAddress;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;

class EmailAddressTypeTest extends TestCase
{

    private AbstractPlatform $platform;

    private Type $type;

    public static function setUpBeforeClass(): void
    {
        Type::addType('test_' . EmailAddress::class, EmailAddressType::class);
    }

    protected function setUp(): void
    {
        /** @var \Doctrine\DBAL\Platforms\AbstractPlatform $platform */
        $platform = $this
            ->getMockBuilder(AbstractPlatform::class)
            ->getMockForAbstractClass();
        $this->platform = $platform;

        $this->type = Type::getType('test_' . EmailAddress::class);
    }

    public function testConvertsToDatabaseValue(): void
    {
        $phoneNumber = EmailAddress::fromString('maxmilian@pumpicka.com');

        self::assertSame(
            'maxmilian@pumpicka.com',
            $this->type->convertToDatabaseValue($phoneNumber, $this->platform)
        );
    }

    public function testConvertsEmptyToDatabaseValue(): void
    {
        self::assertNull(
            $this->type->convertToDatabaseValue(null, $this->platform)
        );
    }

    public function testConvertsToPhpValue(): void
    {
        /** @var \App\Value\EmailAddress $emailAddress */
        $emailAddress = $this->type->convertToPHPValue('harry@sroubek.cz', $this->platform);

        self::assertSame(
            'harry@sroubek.cz',
            $emailAddress->toString()
        );
    }

    public function testConvertsEmptyToPhpValue(): void
    {
        $emailAddress = $this->type->convertToPHPValue(null, $this->platform);

        self::assertNull($emailAddress);
    }

}
