<?php declare(strict_types=1);

namespace OpenSerializer;

use LogicException;
use function is_string;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;

final class JsonObject implements SerializedObject
{
    private string $encoded;
    /** @var array<string, mixed> */
    private array $decoded;

    private function __construct()
    {
    }

    /** @param array<string, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        $json = new self();
        $json->decoded = $decoded;

        return $json;
    }

    public static function fromJsonString(string $encoded): self
    {
        $json = new self();
        $json->encoded = $encoded;

        return $json;
    }

    /** @return array<string, mixed> */
    public function decode(): array
    {
        return $this->decoded ?? $this->doDecode();
    }

    public function encode(): string
    {
        return $this->encoded ?? $this->doEncode();
    }

    public function toString(): string
    {
        return $this->encode();
    }

    /** @return array<string, mixed> */
    private function doDecode(): array
    {
        $decoded = json_decode($this->encoded, true);
        if ($decoded === null && json_last_error()) {
            throw new LogicException('Invalid JSON: ' . json_last_error_msg());
        }

        return $this->decoded = $decoded;
    }

    private function doEncode(): string
    {
        $encoded = json_encode($this->decoded);
        if (!is_string($encoded)) {
            throw new LogicException('JSON encoding failed');
        }

        return $this->encoded = $encoded;
    }
}
