<?php declare(strict_types=1);

namespace OpenSerializer;

use LogicException;
use ReflectionClass;
use function get_class;
use function gettype;
use function is_array;
use function is_object;
use function is_scalar;
use function sprintf;

final class StructSerializer
{
    /**
     * @return array<string, mixed>
     */
    public function serializeObject(object $object): array
    {
        $class = new ReflectionClass(get_class($object));
        self::assertUserDefined($class);

        $properties = $class->getProperties();
        $props = [];

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $props[$property->getName()] = $this->serializeValue($property->getValue($object));
        }

        return $props;
    }

    /**
     * @param mixed $value
     * @return array|bool|float|int|string|null
     */
    private function serializeValue($value)
    {
        if (is_object($value)) {
            return $this->serializeObject($value);
        }

        if (is_array($value)) {
            return $this->serializeArray($value);
        }

        if (is_scalar($value) || $value === null) {
            return $value;
        }

        throw new LogicException(sprintf('Unsupported type: %s', gettype($value)));
    }

    /** @return array<int|string, mixed> */
    private function serializeArray(array $value): array
    {
        $items = [];
        foreach ($value as $key => $item) {
            $items[$key] = $this->serializeValue($item);
        }

        return $items;
    }

    private static function assertUserDefined(ReflectionClass $class): void
    {
        if (!$class->isUserDefined()) {
            throw new LogicException("Cannot serialize class {$class->getName()} that is not user defined");
        }
    }
}
