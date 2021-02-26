<?php
declare(strict_types = 1);

namespace Halsey\Journal\Menu;

use Innmind\Url\{
    Url,
    RelativePath,
};

final class Entry
{
    private string $name;
    private Url $url;
    private bool $externalLink;
    /** @var list<self> */
    private array $entries;
    private bool $alwaysOpen = false;

    private function __construct(
        string $name,
        Url $url,
        bool $externalLink,
        self ...$entries
    ) {
        $this->name = $name;
        $this->url = $url;
        $this->externalLink = $externalLink;
        $this->entries = $entries;
    }

    public static function externalLink(
        string $name,
        Url $url,
        self ...$entries
    ): self {
        return new self($name, $url, true, ...$entries);
    }

    public function alwaysOpen(): self
    {
        $self = clone $this;
        $self->alwaysOpen = true;

        return $self;
    }

    public function openFor(RelativePath $markdown): bool
    {
        if ($this->alwaysOpen) {
            return true;
        }

        return false;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function url(): Url
    {
        return $this->url;
    }

    public function pointsElsewhere(): bool
    {
        return $this->externalLink;
    }

    /**
     * @return list<self>
     */
    public function entries(): array
    {
        return $this->entries;
    }
}
