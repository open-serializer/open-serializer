<?php declare(strict_types=1);

namespace OpenSerializer\Examples;

use LogicException;
use OpenSerializer\TypeSerializer;
use OpenSerializer\TypeDeserializer;
use function get_class;
use function sprintf;

/**
 * @implements TypeSerializer<Foo>
 * @implements TypeDeserializer<Foo>
 */
final class ExampleFooSerializer implements TypeSerializer, TypeDeserializer
{
    /**
     * @param Foo $object
     */
    public function serializeObject(object $object): array
    {
        if (!$object instanceof Foo) {
            throw new LogicException(sprintf('Unable to serialize %s', get_class($object)));
        }

        return [
            'bar' => $object->bar(),
        ];
    }

    public function deserializeObject(string $class, array $struct): object
    {
        if ($class !== Foo::class) {
            throw new LogicException(sprintf('Unable to deserialize %s', $class));
        }

        return new Foo($struct['bar']);
    }
}
