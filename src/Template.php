<?php
declare(strict_types = 1);

namespace Halsey\Journal;

use Innmind\Templating\Name;

final class Template
{
    private string $name;
    private Name $entrypoint;

    private function __construct(string $name, Name $entrypoint)
    {
        $this->name = $name;
        $this->entrypoint = $entrypoint;
    }

    public static function raw(): self
    {
        return new self('raw', new Name('raw/template/index.html.twig'));
    }

    public function entrypoint(): Name
    {
        return $this->entrypoint;
    }

    public function toString(): string
    {
        return $this->name;
    }
}
