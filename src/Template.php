<?php
declare(strict_types = 1);

namespace Halsey\Journal;

use Innmind\Templating\Name;

/**
 * @psalm-immutable
 */
enum Template
{
    case raw;

    public function entrypoint(): Name
    {
        return match ($this) {
            self::raw => new Name('raw/template/index.html.twig'),
        };
    }

    public function toString(): string
    {
        return match ($this) {
            self::raw => 'raw',
        };
    }
}
