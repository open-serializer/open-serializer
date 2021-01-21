<?php declare(strict_types=1);

namespace OpenSerializer\Tests;

use LogicException;
use OpenSerializer\JsonObject;
use PHPUnit\Framework\TestCase;
use function tmpfile;

final class JsonObjectTest extends TestCase
{
    /** @dataProvider jsonExamples */
    public function test_creating_from_string(string $encoded, array $decoded): void
    {
        $json = JsonObject::fromJsonString($encoded);

        self::assertJsonStringEqualsJsonString($encoded, $json->encode());
        self::assertEquals($decoded, $json->decode());
    }

    /** @dataProvider jsonExamples */
    public function test_creating_from_array(string $encoded, array $decoded): void
    {
        $json = JsonObject::fromArray($decoded);

        self::assertJsonStringEqualsJsonString($encoded, $json->encode());
        self::assertEquals($decoded, $json->decode());
    }

    public function test_throwing_LogicException_when_decoding_fails()
    {
        $json = JsonObject::fromJsonString('{"fail"');

        $this->expectException(LogicException::class);
        $json->decode();
    }

    public function test_throwing_LogicException_when_encoding_fails()
    {
        $json = JsonObject::fromArray(['file' => tmpfile()]);

        $this->expectException(LogicException::class);
        $json->encode();
    }

    public function jsonExamples(): array
    {
        return [
            [
                <<<'JSON'
                {
                    "title": "Once upon a time",
                    "pages": 999,
                    "progress": 0.44,
                    "tags": ["book", "paper", "print"],
                    "price": {
                        "value": "9.99",
                        "currency": "USD"
                    }
                }
                JSON,
                [
                    'title' => 'Once upon a time',
                    'pages' => 999,
                    'progress' => 0.44,
                    'tags' => ['book', 'paper', 'print'],
                    'price' => [
                        'value' => '9.99',
                        'currency' => 'USD',
                    ],
                ]
            ]
        ];
    }
}
