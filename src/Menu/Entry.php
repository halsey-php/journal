<?php
declare(strict_types = 1);

namespace Halsey\Journal\Menu;

use Innmind\Url\{
    Url,
    RelativePath,
};

final class Entry
{
    private Url $url;
    private bool $externalLink;
    /** @var list<self> */
    private array $entries;
    private bool $alwaysOpen = false;

    private function __construct(Url $url, bool $externalLink, self ...$entries)
    {
        $this->url = $url;
        $this->externalLink = $externalLink;
        $this->entries = $entries;
    }

    public static function externalLink(Url $url, self ...$entries): self
    {
        return new self($url, true, ...$entries);
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
}
