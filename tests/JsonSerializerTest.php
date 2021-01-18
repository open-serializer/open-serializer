<?php declare(strict_types=1);

namespace OpenSerializer\Tests;

use DateTimeImmutable;
use LogicException;
use OpenSerializer\JsonObject;
use OpenSerializer\JsonSerializer;
use OpenSerializer\Tests\Stub\DatesTyped;
use OpenSerializer\Tests\Stub\GenericArrays;
use OpenSerializer\Tests\Stub\IntegersDocs;
use OpenSerializer\Tests\Stub\IntegersTyped;
use OpenSerializer\Tests\Stub\Node;
use OpenSerializer\Tests\Stub\NodeName;
use PHPUnit\Framework\TestCase;
use function get_class;

final class JsonSerializerTest extends TestCase
{
    /** @test */
    public function it_serializes_int_property(): void
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

    /** @test */
    public function it_serializes_list_of_objects(): void
    {
        $object = new IntegersTyped(1, null);
        $list = new GenericArrays([$object]);

        self::assertEquals(
            [
                'listOfIntegers' => [
                    [
                        'intProp' => 1,
                        'nullableIntProp' => null,
                    ],
                ],
            ],
            (new JsonSerializer())->serialize($list)->decode()
        );
    }

    /** @test */
    public function it_serializes_nested_structures(): void
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

    /** @test */
    public function it_will_not_serialize_datetime(): void
    {
        $this->expectException(LogicException::class);

        $date = new DatesTyped(new DateTimeImmutable('today 12:00'));
        (new JsonSerializer())->serialize($date);
    }

    /**
     * @test
     * @dataProvider simpleDeserialization
     */
    public function it_deserialized_simple_object(object $expected, JsonObject $json): void
    {
        self::assertEquals(
            $expected,
            (new JsonSerializer())->deserialize(get_class($expected), $json)
        );
    }

    /** @test */
    public function it_deserializes_generic_array(): void
    {
        self::assertEquals(
            new GenericArrays([new IntegersTyped(1, 2), new IntegersTyped(3, null)]),
            (new JsonSerializer())->deserialize(
                GenericArrays::class,
                JsonObject::fromArray(
                    [
                        'listOfIntegers' => [
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

    /** @test */
    public function it_deserializes_nested_structures()
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
                            ]
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
