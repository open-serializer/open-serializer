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
