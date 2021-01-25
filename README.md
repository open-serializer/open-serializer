# Open Serializer

[![codecov](https://codecov.io/gh/open-serializer/open-serializer/branch/main/graph/badge.svg?token=DBL7RGBGFW)](https://codecov.io/gh/open-serializer/open-serializer)

Open Serializer is an uncomplicated library that recursively converts objects to and from JSON structure.

It is based on the brilliant idea of Matthias Noback described in the blog post 
[Designing a JSON serializer](https://matthiasnoback.nl/2017/07/designing-a-json-serializer/) and implemented in 
[matthiasnoback/naive-serializer](https://github.com/matthiasnoback/naive-serializer).

The core concepts of Open Serializer are:
* Stay as simple and small as possible.
* Do not require any supporting code or configuration other than object property types or `@var` annotations.
* Accept decoded or encoded JSON structures.
* Be open for extension.

## Usage

### Supported types

```php
class Example {
    private string $typedProperty;
    
    /** @var string */
    private $docblockProperty;
    
    private ?self $selfTypedProperty;
    
    /** @var Example[] */
    private array $genericList;
    
    /** @var array<Example> */
    private array $genericArray;
    
    /** @var array<int, Example> */
    private array $genericArrayWithIntKey;
    
    /** @var array<string, Example> */
    private array $genericArrayWithStringKeys;
    
    /** @var array<string, array<Example>> */
    private array $genericArrayOfArrays;
}
```

### Serialize to JsonObject

```php
class Example {
    private ExampleText $text;
}
class ExampleText {
    private string $value;
}

$object = new Example(new ExampleText('example'));
$serializer = new JsonSerializer();
$toJson = $serializer->serialize($object)->encode(); // '{"text": {"value":"example"}}'
$toArray = $serializer->serialize($object)->decode(); // ['text' => ['value' => 'example']]
```

### Deserialize from JsonObject

```php
$jsonObject = JsonObject::fromJsonString('{"text": {"value":"example"}}');
// or
$jsonObject = JsonObject::fromArray(['text' => ['value' => 'example']]);

$serializer = new JsonSerializer();
$exampleObject = $serializer->deserialize(Example::class, $jsonObject);
```

### Unsupported types

#### Types that has no valid case for JSON (de)serialization

* `resource`
* `callable`
* `Closure`
* `Generator`
* ...

#### Internal PHP objects and interfaces (at least by default, see [Extending](#Extending))

* `DateTime`
* `DateTimeImmutable`
* ...

#### Union types (PHP 8)

Union type will be treated as `mixed` and will be deserialized as is in JSON structure.

#### Constructor property (PHP 8)

Currently, constructor property promotion is not supported, planned for the future. 

## Extending

Sometimes there is a requirement to support internal types, interfaces or implement custom serialization logic.
It can be done by implementing custom `TypeSerializer` and/or `TypeDeserializer`.

```php
class Foo {}

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
        // ...serialize Foo to array
    }

    public function deserializeObject(string $class, array $struct): Foo
    {
        // ...deserialize Foo from array
    }
}

$fooSerializer = new ExampleFooSerializer();
$jsonSerializer = new JsonSerializer(
    new StructSerializer([Foo::class => $fooSerializer]),
    new StructDeserializer([Foo::class => $fooSerializer])
);

```
