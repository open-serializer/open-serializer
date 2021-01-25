<?php declare(strict_types=1);

namespace OpenSerializer\Type;

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\Static_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\This;
use ReflectionClass;
use ReflectionProperty;
use function array_filter;
use function array_values;
use function count;
use function iterator_to_array;

final class DocBlockPropertyResolver implements PropertyTypeResolver
{
    private DocBlockFactory $docblockFactory;
    private ContextFactory $contextFactory;

    public function __construct()
    {
        $this->docblockFactory = DocBlockFactory::createInstance();
        $this->contextFactory = new ContextFactory();
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public function resolveType(ReflectionClass $class, ReflectionProperty $property): TypeInfo
    {
        $doc = $property->getDocComment();

        if ($doc === false || $doc === '') {
            return TypeInfo::ofMixed();
        }

        $context = $this->contextFactory->createFromReflector($class);
        $docBlock = $this->docblockFactory->create($doc, $context);
        $varTags = $docBlock->getTagsByName('var');

        if (count($varTags) === 0) {
            return TypeInfo::ofMixed();
        }

        /** @var Var_ $varTag */
        $varTag = $varTags[0];
        $type = $varTag->getType();

        return $this->typeFromDoc($class, $type);
    }

    /** @param ReflectionClass<object> $class */
    private function typeFromDoc(ReflectionClass $class, ?Type $type): TypeInfo
    {
        $isNullable = false;

        if ($type instanceof Compound) {
            $types = iterator_to_array($type);
            $typesExceptNull = array_values(array_filter($types, static fn(Type $t): bool => $t instanceof Null_));

            if (count($typesExceptNull) !== 1) {
                return TypeInfo::ofMixed();
            }

            $type = $types[0];
            $isNullable = count($typesExceptNull) !== count($types);
        }

        switch (true) {
            case $type instanceof String_:
            case $type instanceof Integer:
            case $type instanceof Float_:
                return TypeInfo::ofBuiltIn((string)$type, $isNullable);

            case $type instanceof Boolean:
                return TypeInfo::ofBuiltIn('bool', $isNullable);

            case $type instanceof Object_:
                return TypeInfo::ofObject((string)$type, $isNullable);

            case $type instanceof Self_:
            case $type instanceof This:
            case $type instanceof Static_:
                return TypeInfo::ofObject($class->getName(), $isNullable);

            case $type instanceof Array_:
                return TypeInfo::ofArray($isNullable, $this->typeFromDoc($class, $type->getValueType()));
        }

        // TODO fail here when type is not mixed, but unsupported

        return TypeInfo::ofMixed();
    }
}
