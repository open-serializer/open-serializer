<?php declare(strict_types=1);

namespace OpenSerializer\Tests;

use DateTimeImmutable;
use DateTimeZone;
use LogicException;
use OpenSerializer\JsonObject;
use OpenSerializer\JsonSerializer;
use OpenSerializer\StructDeserializer;
use OpenSerializer\StructSerializer;
use OpenSerializer\Tests\Stub\ArrayOfArraysOfIntegersTyped;
use OpenSerializer\Tests\Stub\ArrayOfIntegers;
use OpenSerializer\Tests\Stub\ArrayOfIntegersTyped;
use OpenSerializer\Tests\Stub\ArrayOfMixed;
use OpenSerializer\Tests\Stub\ArrayOfUnknown;
use OpenSerializer\Tests\Stub\BooleansDocs;
use OpenSerializer\Tests\Stub\BooleansFreak;
use OpenSerializer\Tests\Stub\BooleansTyped;
use OpenSerializer\Tests\Stub\ClosureTyped;
use OpenSerializer\Tests\Stub\DatesTyped;
use OpenSerializer\Tests\Stub\DefaultsTyped;
use OpenSerializer\Tests\Stub\ExampleDateTimeSerializer;
use OpenSerializer\Tests\Stub\FloatsDocs;
use OpenSerializer\Tests\Stub\FloatsTyped;
use OpenSerializer\Tests\Stub\IntegersDocs;
use OpenSerializer\Tests\Stub\IntegersTyped;
use OpenSerializer\Tests\Stub\ListOfIntegers;
use OpenSerializer\Tests\Stub\Node;
use OpenSerializer\Tests\Stub\NodeName;
use OpenSerializer\Tests\Stub\NodeSelf;
use OpenSerializer\Tests\Stub\NodeStatic;
use OpenSerializer\Tests\Stub\ResourceDocs;
use OpenSerializer\Tests\Stub\StringsDocs;
use OpenSerializer\Tests\Stub\StringsTyped;
use OpenSerializer\Tests\Stub\UnknownType;
use PHPUnit\Framework\TestCase;
use function get_class;
use function tmpfile;

final class JsonSerializerTest extends TestCase
{
    public function test_serialization_of_int_property(): void
    {
        $typed = new IntegersTyped(11, 12);
        $documented = new IntegersDocs(13, 14);

        self::assertEquals(
            [
                'integer' => 11,
                'nullableInteger' => 12,
            ],
            (new JsonSerializer())->serialize($typed)->decode()
        );

        self::assertEquals(
            [
                'integer' => 13,
                'nullableInteger' => 14,
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
                        'integer' => 1,
                        'nullableInteger' => null,
                    ],
                ],
            ],
            (new JsonSerializer())->serialize($list)->decode()
        );
    }

    public function test_serialization_of_nested_structures(): void
    {
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

    public function test_will_not_deserialize_datetime(): void
    {
        $this->expectException(LogicException::class);

        (new JsonSerializer())->deserialize(DatesTyped::class, JsonObject::fromJsonString('{"date": {"datetime": "2021-01-21"}}'));
    }

    public function test_will_fail_with_invalid_class_name(): void
    {
        $this->expectException(LogicException::class);

        /** @phpstan-ignore-next-line */
        (new JsonSerializer())->deserialize('string', JsonObject::fromJsonString('{"prop":"string"}'));
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
                                'integer' => 1,
                                'nullableInteger' => 2,
                            ],
                            [
                                'integer' => 3,
                                'nullableInteger' => null,
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

    public function test_deserialization_of_list_of_integers(): void
    {
        self::assertEquals(
            new ListOfIntegers(123, 321, 213, 231),
            (new JsonSerializer())->deserialize(
                ListOfIntegers::class,
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
                                    'integer' => 12,
                                    'nullableInteger' => null,
                                ],
                                [
                                    'integer' => 13,
                                    'nullableInteger' => 14,
                                ],
                            ],
                            'two' => [
                                [
                                    'integer' => 22,
                                    'nullableInteger' => null,
                                ],
                                [
                                    'integer' => 23,
                                    'nullableInteger' => 24,
                                ],
                            ],
                        ],
                    ]
                )
            )
        );
    }

    public function test_deserialization_of_array_of_mixed_values(): void
    {
        self::assertEquals(
            new ArrayOfMixed(123, 'string', 1.23, ['array']),
            (new JsonSerializer())->deserialize(
                ArrayOfMixed::class,
                JsonObject::fromArray(['items' => [123, 'string', 1.23, ['array']]])
            )
        );
    }

    public function test_deserialization_of_array_of_unspecified_values(): void
    {
        self::assertEquals(
            new ArrayOfUnknown(123, 'string', 1.23, ['array']),
            (new JsonSerializer())->deserialize(
                ArrayOfUnknown::class,
                JsonObject::fromArray(['items' => [123, 'string', 1.23, ['array']]])
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

    public function test_fails_to_deserialize_unknown_type(): void
    {
        $this->expectException(LogicException::class);

        (new JsonSerializer())->deserialize(
            UnknownType::class,
            JsonObject::fromJsonString('{"unknown": {"whatever": true}}')
        );
    }

    public function test_fails_to_serialize_closure(): void
    {
        $this->expectException(LogicException::class);

        (new JsonSerializer())->serialize(new ClosureTyped(fn() => 'hello world'));
    }

    public function test_fails_to_serialize_resource(): void
    {
        $res = tmpfile();
        self::assertNotFalse($res);

        $this->expectException(LogicException::class);

        /** @phpstan-ignore-next-line */
        (new JsonSerializer())->serialize(new ResourceDocs($res));
    }

    public function test_serializing_DateTime_with_custom_serializer(): void
    {
        $today = new DateTimeImmutable('today 12:00', new DateTimeZone('Europe/Warsaw'));
        $serializer = new JsonSerializer(
            new StructSerializer([DateTimeImmutable::class => new ExampleDateTimeSerializer()])
        );

        self::assertEquals(
            [
                'date' => [
                    'atom' => $today->format(DateTimeImmutable::ATOM),
                    'timezone' => 'Europe/Warsaw',
                ],
            ],
            $serializer->serialize(new DatesTyped($today))->decode()
        );
    }

    public function test_deserializing_DateTime_with_custom_serializer(): void
    {
        $today = new DateTimeImmutable('today 12:00', new DateTimeZone('Europe/Warsaw'));
        $serializer = new JsonSerializer(
            null,
            new StructDeserializer([DateTimeImmutable::class => new ExampleDateTimeSerializer()])
        );

        self::assertEquals(
            new DatesTyped($today),
            $serializer->deserialize(
                DatesTyped::class,
                JsonObject::fromArray(
                    [
                        'date' => [
                            'atom' => $today->format(DateTimeImmutable::ATOM),
                            'timezone' => 'Europe/Warsaw',
                        ],
                    ]
                )
            )
        );
    }

    public function test_serializing_class_with_self_properties(): void
    {
        self::assertEquals(
            [
                'name' => 'parent',
                'child' => [
                    'name' => 'child',
                    'child' => null,
                    'otherChildren' => [],
                ],
                'otherChildren' => [
                    [
                        'name' => 'other child 1',
                        'child' => null,
                        'otherChildren' => [],
                    ],
                    [
                        'name' => 'other child 2',
                        'child' => null,
                        'otherChildren' => [],
                    ],
                ]
            ],
            (new JsonSerializer())
                ->serialize(
                    new NodeSelf(
                        'parent',
                        new NodeSelf('child', null),
                        new NodeSelf('other child 1', null),
                        new NodeSelf('other child 2', null)
                    )
                )
                ->decode()
        );
    }

    public function test_deserializing_class_with_self_properties(): void
    {
        self::assertEquals(
            new NodeSelf(
                'parent',
                new NodeSelf('child', null),
                new NodeSelf('other child 1', null),
                new NodeSelf('other child 2', null)
            ),
            (new JsonSerializer())->deserialize(
                NodeSelf::class,
                JsonObject::fromArray(
                    [
                        'name' => 'parent',
                        'child' => [
                            'name' => 'child',
                            'child' => null,
                            'otherChildren' => [],
                        ],
                        'otherChildren' => [
                            [
                                'name' => 'other child 1',
                                'child' => null,
                                'otherChildren' => [],
                            ],
                            [
                                'name' => 'other child 2',
                                'child' => null,
                                'otherChildren' => [],
                            ],
                        ],
                    ]
                )
            )
        );
    }

    public function test_serializing_class_with_static_properties(): void
    {
        self::assertEquals(
            [
                'name' => 'parent',
                'children' => [
                    [
                        'name' => 'child 1',
                        'children' => [],
                    ],
                    [
                        'name' => 'child 2',
                        'children' => [],
                    ],
                ],
            ],
            (new JsonSerializer())
                ->serialize(
                    new NodeStatic(
                        'parent',
                        new NodeStatic('child 1'),
                        new NodeStatic('child 2'),
                    )
                )
                ->decode()
        );
    }

    public function test_deserializing_class_with_static_properties(): void
    {
        self::assertEquals(
            new NodeStatic(
                'parent',
                new NodeStatic('child 1'),
                new NodeStatic('child 2'),
            ),
            (new JsonSerializer())->deserialize(
                NodeStatic::class,
                JsonObject::fromArray(
                    [
                        'name' => 'parent',
                        'children' => [
                            [
                                'name' => 'child 1',
                                'children' => [],
                            ],
                            [
                                'name' => 'child 2',
                                'children' => [],
                            ],
                        ],
                    ]
                )
            )
        );
    }

    public function test_recognizing_true_and_false_as_boolean(): void
    {
        self::assertEquals(
            new BooleansFreak(true, false),
            (new JsonSerializer())->deserialize(
                BooleansFreak::class,
                JsonObject::fromJsonString('{"true":true,"false":false}')
            )
        );
    }

    public function simpleDeserialization(): array
    {
        return [
            [
                new IntegersTyped(11, 12),
                JsonObject::fromJsonString('{"integer": 11, "nullableInteger": 12}'),
            ],
            [
                new IntegersTyped(11, null),
                JsonObject::fromJsonString('{"integer": 11, "nullableInteger": null}'),
            ],
            [
                new IntegersDocs(11, 12),
                JsonObject::fromJsonString('{"integer": 11, "nullableInteger": 12}'),
            ],
            [
                new IntegersDocs(11, null),
                JsonObject::fromJsonString('{"integer": 11, "nullableInteger": null}'),
            ],
            [
                new FloatsTyped(1.23, 4.56),
                JsonObject::fromJsonString('{"float": 1.23,"nullableFloat": 4.56}'),
            ],
            [
                new FloatsTyped(1.23, null),
                JsonObject::fromJsonString('{"float": 1.23,"nullableFloat": null}'),
            ],
            [
                new FloatsDocs(1.23, 4.56),
                JsonObject::fromJsonString('{"float": 1.23,"nullableFloat": 4.56}'),
            ],
            [
                new FloatsDocs(1.23, null),
                JsonObject::fromJsonString('{"float": 1.23,"nullableFloat": null}'),
            ],
            [
                new BooleansTyped(true, false),
                JsonObject::fromJsonString('{"boolean": true,"nullableBoolean": false}'),
            ],
            [
                new BooleansTyped(true, null),
                JsonObject::fromJsonString('{"boolean": true,"nullableBoolean": null}'),
            ],
            [
                new BooleansDocs(true, false),
                JsonObject::fromJsonString('{"boolean": true,"nullableBoolean": false}'),
            ],
            [
                new BooleansDocs(true, null),
                JsonObject::fromJsonString('{"boolean": true,"nullableBoolean": null}'),
            ],
            [
                new StringsTyped("a string", "optional string"),
                JsonObject::fromJsonString('{"string": "a string", "nullableString": "optional string"}'),
            ],
            [
                new StringsTyped("a string", null),
                JsonObject::fromJsonString('{"string": "a string", "nullableString": null}'),
            ],
            [
                new StringsDocs("a string", "optional string"),
                JsonObject::fromJsonString('{"string": "a string", "nullableString": "optional string"}'),
            ],
            [
                new StringsDocs("a string", null),
                JsonObject::fromJsonString('{"string": "a string", "nullableString": null}'),
            ],
            [
                new DefaultsTyped(),
                JsonObject::fromJsonString('{}'),
            ],
            [
                new DefaultsTyped(null, 'custom'),
                JsonObject::fromJsonString('{"nullableString": "custom"}'),
            ],
        ];
    }
}
