<?php
declare(strict_types = 1);

namespace Halsey\Journal\Menu;

use Halsey\Journal\RewriteUrl;
use Innmind\Url\{
    Url,
    Path,
    RelativePath,
    Scheme,
    Authority,
    Query,
    Fragment,
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

    public static function section(string $name, self $first, self ...$entries): self
    {
        return new self($name, Url::of('#'), false, $first, ...$entries);
    }

    public static function markdown(
        string $name,
        RelativePath $markdown,
        self ...$entries
    ): self {
        return new self(
            $name,
            new Url(
                Scheme::none(),
                Authority::none(),
                $markdown,
                Query::none(),
                Fragment::none(),
            ),
            false,
            ...$entries,
        );
    }

    public function alwaysOpen(): self
    {
        $self = clone $this;
        $self->alwaysOpen = true;

        return $self;
    }

    public function openFor(Path $markdown): bool
    {
        if ($this->alwaysOpen) {
            return true;
        }

        foreach ($this->entries as $entry) {
            if ($entry->openFor($markdown)) {
                return true;
            }
        }

        if ($this->externalLink) {
            return false;
        }

        return $this->url->path()->equals($markdown);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function url(): Url
    {
        return $this->url;
    }

    public function resolve(RewriteUrl $rewrite, Url $baseUrl): Url
    {
        if ($this->externalLink) {
            return $this->url;
        }

        $rewritten = $rewrite($this->url);

        return $baseUrl
            ->withPath($baseUrl->path()->resolve($rewritten->path()))
            ->withFragment($rewritten->fragment());
    }

    public function pointsElsewhere(): bool
    {
        return $this->externalLink;
    }

    public function pointsSomewhere(): bool
    {
        return !$this->url->equals(Url::of('#'));
    }

    /**
     * @return list<self>
     */
    public function entries(): array
    {
        return $this->entries;
    }
}
