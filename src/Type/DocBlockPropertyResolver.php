<?php declare(strict_types=1);

namespace OpenSerializer\Type;

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use ReflectionClass;
use ReflectionProperty;
use function count;
use function iterator_to_array;

final class DocBlockPropertyResolver implements PropertyTypeResolver
{
    private DocBlockFactory $docblockFactory;
    private ContextFactory $contextFactory;
    private TypeResolver $typeResolver;

    public function __construct()
    {
        $this->docblockFactory = DocBlockFactory::createInstance();
        $this->contextFactory = new ContextFactory();
        $this->typeResolver = new TypeResolver();
    }

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

        return $this->typeFromDoc($type);
    }

    private function typeFromDoc(?Type $type): TypeInfo
    {
        $isNullable = false;

        if ($type instanceof Compound) {
            $types = iterator_to_array($type);

            if (count($types) !== 2) {
                return TypeInfo::ofMixed();
            }

            if ($types[0] instanceof Null_) {
                $type = $types[1];
                $isNullable = true;
            } else {
                if ($types[1] instanceof Null_) {
                    $type = $types[0];
                    $isNullable = true;
                } else {
                    return TypeInfo::ofMixed();
                }
            }
        }

        switch (true) {
            case $type instanceof String_:
            case $type instanceof Integer:
            case $type instanceof Boolean:
            case $type instanceof Float_:
                return TypeInfo::ofBuiltIn((string)$type, $isNullable);

            case $type instanceof Object_:
                return TypeInfo::ofObject((string)$type, $isNullable);

            case $type instanceof Array_:
                return TypeInfo::ofArray($isNullable, $this->typeFromDoc($type->getValueType()));
        }

        return TypeInfo::ofMixed();
    }
}
