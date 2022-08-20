<?php declare(strict_types = 1);

namespace App\Value\Doctrine;

use App\Value\PhoneNumber;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;

class PhoneNumberTypeTest extends TestCase
{

    private AbstractPlatform $platform;

    private Type $type;

    public static function setUpBeforeClass(): void
    {
        Type::addType('test_' . PhoneNumber::class, PhoneNumberType::class);
    }

    protected function setUp(): void
    {
        /** @var \Doctrine\DBAL\Platforms\AbstractPlatform $platform */
        $platform = $this
            ->getMockBuilder(AbstractPlatform::class)
            ->getMockForAbstractClass();
        $this->platform = $platform;

        $this->type = Type::getType('test_' . PhoneNumber::class);
    }

    public function testConvertsToDatabaseValue(): void
    {
        $phoneNumber = PhoneNumber::fromString('+420777888999');

        self::assertSame(
            '+420777888999',
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
        /** @var \App\Value\PhoneNumber $phoneNumber */
        $phoneNumber = $this->type->convertToPHPValue('+420777888999', $this->platform);

        self::assertSame(
            '+420777888999',
            $phoneNumber->toString()
        );
    }

    public function testConvertsEmptyToPhpValue(): void
    {
        $phoneNumber = $this->type->convertToPHPValue(null, $this->platform);

        self::assertNull($phoneNumber);
    }

}
