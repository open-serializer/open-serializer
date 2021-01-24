<?php declare(strict_types=1);

namespace OpenSerializer\Tests\Stub;

use DateTimeImmutable;
use DateTimeZone;
use LogicException;
use OpenSerializer\TypeDeserializer;
use OpenSerializer\TypeSerializer;
use function get_class;
use function json_encode;
use function sprintf;

/**
 * @implements TypeSerializer<DateTimeImmutable>
 * @implements TypeDeserializer<DateTimeImmutable>
 */
final class ExampleDateTimeSerializer implements TypeSerializer, TypeDeserializer
{
    public function serializeObject(object $object): array
    {
        if (!$object instanceof DateTimeImmutable) {
            throw new LogicException(sprintf('Unable to serialize %s', get_class($object)));
        }

        return [
            'atom' => $object->format(DateTimeImmutable::ATOM),
            'timezone' => $object->getTimezone()->getName(),
        ];
    }

    public function deserializeObject(string $class, array $struct): object
    {
        if ($class !== DateTimeImmutable::class) {
            throw new LogicException(sprintf('Unable to deserialize %s', $class));
        }

        $dateTime = DateTimeImmutable::createFromFormat(
            DateTimeImmutable::ATOM,
            $struct['atom'],
            new DateTimeZone($struct['timezone'])
        );

        if ($dateTime === false) {
            throw new LogicException(sprintf('Failed to deserialize DateTime from %s', json_encode($struct)));
        }

        return $dateTime;
    }
}
