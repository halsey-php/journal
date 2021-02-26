<?php
declare(strict_types = 1);

namespace Halsey\Journal;

use Halsey\Journal\Menu\Entry;
use Innmind\Url\{
    Path,
    RelativePath,
};

final class Config
{
    private Path $project;
    private RelativePath $documentation;
    private string $title = 'Documentation';
    /** @var list<Entry> */
    private array $menu = [];
    private bool $alwaysOpen = false;

    public function __construct(Path $project)
    {
        $this->project = $project;
        /** @var RelativePath */
        $this->documentation = Path::of('documentation/');
    }

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

    public function locatedAt(RelativePath $folder): self
    {
        if (!$folder->directory()) {
            throw new \LogicException('Path to documentation must be a folder');
        }

        $self = clone $this;
        $self->documentation = $folder;

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

    public function documentation(): Path
    {
        return $this->project->resolve($this->documentation);
    }
}
