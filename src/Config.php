<?php
declare(strict_types = 1);

namespace Halsey\Journal;

use Halsey\Journal\Menu\Entry;
use Innmind\Url\{
    Path,
    RelativePath,
};
use Innmind\Immutable\Map;

final class Config
{
    private Path $project;
    private RelativePath $documentation;
    private Template $template;
    private string $title = 'Documentation';
    /** @var list<Entry> */
    private array $menu = [];

    public function __construct(Path $project)
    {
        $this->project = $project;
        /** @var RelativePath */
        $this->documentation = Path::of('documentation/');
        $this->template = Template::raw();
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

    /**
     * @internal
     */
    public function documentation(): Path
    {
        return $this->project->resolve($this->documentation);
    }

    /**
     * @internal
     */
    public function template(): Template
    {
        return $this->template;
    }

    /**
     * @internal
     *
     * @return Map<string, mixed>
     */
    public function forTemplating(): Map
    {
        /** @var Map<string, mixed> */
        $parameters = Map::of('string', 'mixed');

        return ($parameters)
            ('title', $this->title)
            ('menu', $this->menu);
    }
}
