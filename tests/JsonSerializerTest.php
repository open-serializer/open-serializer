<?php declare(strict_types=1);

namespace OpenSerializer\Tests;

use DateTimeImmutable;
use LogicException;
use OpenSerializer\JsonObject;
use OpenSerializer\JsonSerializer;
use OpenSerializer\Tests\Stub\ArrayOfArraysOfIntegersTyped;
use OpenSerializer\Tests\Stub\ArrayOfIntegers;
use OpenSerializer\Tests\Stub\ArrayOfIntegersTyped;
use OpenSerializer\Tests\Stub\DatesTyped;
use OpenSerializer\Tests\Stub\IntegersDocs;
use OpenSerializer\Tests\Stub\IntegersTyped;
use OpenSerializer\Tests\Stub\Node;
use OpenSerializer\Tests\Stub\NodeName;
use PHPUnit\Framework\TestCase;
use function get_class;

final class JsonSerializerTest extends TestCase
{
    public function test_serialization_of_int_property(): void
    {
        $typed = new IntegersTyped(11, 12);
        $documented = new IntegersDocs(13, 14);

        self::assertEquals(
            [
                'intProp' => 11,
                'nullableIntProp' => 12,
            ],
            (new JsonSerializer())->serialize($typed)->decode()
        );

        self::assertEquals(
            [
                'intProp' => 13,
                'nullableIntProp' => 14,
            ],
            (new JsonSerializer())->serialize($documented)->decode()
        );
    }

    public function test_serialization_of_list_of_objects(): void
    {
        $object = new IntegersTyped(1, null);
        $list = new ArrayOfIntegersTyped($object);

        self::assertEquals(
            [
                'items' => [
                    [
                        'intProp' => 1,
                        'nullableIntProp' => null,
                    ],
                ],
            ],
            (new JsonSerializer())->serialize($list)->decode()
        );
    }

    public function test_serialization_of_nested_structures(): void
    {
        $serializer = new JsonSerializer();
        $tree = new Node(
            new NodeName('root'),
            [
                new Node(new NodeName('child 1'), []),
                new Node(new NodeName('child 2'), [new Node(new NodeName('child 2 1'), [])]),
            ]
        );

        self::assertEquals(
            [
                'name' => [
                    'value' => 'root',
                ],
                'children' => [
                    [
                        'name' => [
                            'value' => 'child 1',
                        ],
                        'children' => [],
                    ],
                    [
                        'name' => [
                            'value' => 'child 2',
                        ],
                        'children' => [
                            [
                                'name' => [
                                    'value' => 'child 2 1',
                                ],
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
            (new JsonSerializer())->serialize($tree)->decode()
        );
    }

    public function test_will_not_serialize_datetime(): void
    {
        $this->expectException(LogicException::class);

        $date = new DatesTyped(new DateTimeImmutable('today 12:00'));
        (new JsonSerializer())->serialize($date);
    }

    /**
     * @dataProvider simpleDeserialization
     */
    public function test_deserialized_simple_object(object $expected, JsonObject $json): void
    {
        self::assertEquals(
            $expected,
            (new JsonSerializer())->deserialize(get_class($expected), $json)
        );
    }

    public function test_deserialization_of_generic_array(): void
    {
        self::assertEquals(
            new ArrayOfIntegersTyped(new IntegersTyped(1, 2), new IntegersTyped(3, null)),
            (new JsonSerializer())->deserialize(
                ArrayOfIntegersTyped::class,
                JsonObject::fromArray(
                    [
                        'items' => [
                            [
                                'intProp' => 1,
                                'nullableIntProp' => 2,
                            ],
                            [
                                'intProp' => 3,
                                'nullableIntProp' => null,
                            ],
                        ],
                    ]
                )
            )
        );
    }

    public function test_deserialization_of_array_of_integers(): void
    {
        self::assertEquals(
            new ArrayOfIntegers(123, 321, 213, 231),
            (new JsonSerializer())->deserialize(
                ArrayOfIntegers::class,
                JsonObject::fromArray(['items' => [123, 321, 213, 231]])
            )
        );
    }

    public function test_deserialization_of_array_of_arrays_of_objects(): void
    {
        self::assertEquals(
            new ArrayOfArraysOfIntegersTyped(
                [
                    'one' => [new IntegersTyped(12, null), new IntegersTyped(13, 14)],
                    'two' => [new IntegersTyped(22, null), new IntegersTyped(23, 24)],
                ]
            ),
            (new JsonSerializer())->deserialize(
                ArrayOfArraysOfIntegersTyped::class,
                JsonObject::fromArray(
                    [
                        'items' => [
                            'one' => [
                                [
                                    'intProp' => 12,
                                    'nullableIntProp' => null,
                                ],
                                [
                                    'intProp' => 13,
                                    'nullableIntProp' => 14,
                                ],
                            ],
                            'two' => [
                                [
                                    'intProp' => 22,
                                    'nullableIntProp' => null,
                                ],
                                [
                                    'intProp' => 23,
                                    'nullableIntProp' => 24,
                                ],
                            ],
                        ],
                    ]
                )
            )
        );
    }

    public function test_deserialization_of_nested_structures(): void
    {
        self::assertEquals(
            new Node(new NodeName('root node'), [new Node(new NodeName('child 1'), [])]),
            (new JsonSerializer())->deserialize(
                Node::class,
                JsonObject::fromArray(
                    [
                        'name' => [
                            'value' => 'root node',
                        ],
                        'children' => [
                            [
                                'name' => [
                                    'value' => 'child 1',
                                ],
                                'children' => [],
                            ],
                        ],
                    ]
                )
            )
        );
    }

    public function simpleDeserialization(): array
    {
        return [
            [
                new IntegersTyped(11, 12),
                JsonObject::fromArray(['intProp' => 11, 'nullableIntProp' => 12]),
            ],
            [
                new IntegersTyped(11, null),
                JsonObject::fromArray(['intProp' => 11, 'nullableIntProp' => null]),
            ],
        ];
    }
}
