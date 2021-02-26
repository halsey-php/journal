<?php
declare(strict_types = 1);

namespace Halsey\Journal;

use Halsey\Journal\Menu\Entry;
use Innmind\Url\RelativePath;

final class Config
{
    private string $title = 'Documentation';
    /** @var list<Entry> */
    private array $menu = [];
    private bool $alwaysOpen = false;

    public function title(string $title): self
    {
        $self = clone $this;
        $self->title = $title;

        return $self;
    }

    public function menu(Entry ...$menu): self
    {
        $self = clone $this;
        $self->menu = $menu;

        return $self;
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
