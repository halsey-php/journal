<?php
declare(strict_types = 1);

namespace Halsey\Journal;

use Halsey\Journal\Menu\Entry;
use Innmind\Url\{
    Url,
    Path,
    RelativePath,
};
use Innmind\Immutable\Map;

final class Config
{
    private Path $project;
    private RelativePath $documentation;
    private Template $template;
    private string $organization = '';
    private string $repository = '';
    private string $vendor = '';
    private string $package = '';
    /** @var list<Entry> */
    private array $menu = [];

    /**
     * @internal
     */
    public function __construct(Path $project)
    {
        $this->project = $project;
        /** @var RelativePath */
        $this->documentation = Path::of('documentation/');
        $this->template = Template::raw();
    }

    public function package(
        string $vendor,
        string $package,
        string $organization = null,
        string $repository = null
    ): self {
        $self = clone $this;
        $self->organization = $organization ?? $vendor;
        $self->repository = $repository ?? $package;
        $self->vendor = $vendor;
        $self->package = $package;

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
    public function forTemplating(bool $preview): Map
    {
        $baseUrl = "https://{$this->organization}.github.io/{$this->repository}/";

        if ($preview) {
            $baseUrl = 'http://localhost:2492/';
        }

        /** @var Map<string, mixed> */
        $parameters = Map::of('string', 'mixed');

        return ($parameters)
            ('organization', $this->organization)
            ('vendor', $this->vendor)
            ('package', $this->package)
            ('baseUrl', Url::of($baseUrl))
            ('repository', "https://github.com/{$this->organization}/{$this->repository}/")
            ('menu', $this->menu);
    }
}
