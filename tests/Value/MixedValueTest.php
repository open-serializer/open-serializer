<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Value;

use LogicException;
use OpenSerializer\Value\MixedValue;
use PHPUnit\Framework\TestCase;

final class MixedValueTest extends TestCase
{
    function test_object_checking()
    {
        $value = new MixedValue(1);
        self::assertFalse($value->isObject());

        $value = new MixedValue($this);
        self::assertTrue($value->isObject());
    }

    function test_getting_object()
    {
        $value = new MixedValue($this);
        self::assertEquals($this, $value->toObject());
    }

    function test_getting_object_failure()
    {
        $value = new MixedValue(1);
        $this->expectException(LogicException::class);
        $value->toObject();
    }

    function test_getting_array()
    {
        $value = new MixedValue(['a']);
        self::assertEquals(['a'], $value->toArray());
    }

    function test_getting_array_failure()
    {
        $value = new MixedValue(1);
        $this->expectException(LogicException::class);
        $value->toArray();
    }

    function test_getting_scalar_or_null()
    {
        $value = new MixedValue(2);
        self::assertEquals(2, $value->toScalarOrNull());
        $value = new MixedValue(null);
        self::assertEquals(null, $value->toScalarOrNull());
    }

    function test_getting_scalar_or_null_failure()
    {
        $value = new MixedValue($this);
        $this->expectException(LogicException::class);
        $value->toScalarOrNull();
    }
}
