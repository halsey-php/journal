<?php
declare(strict_types = 1);

namespace Halsey\Journal\File;

use Halsey\Journal\{
    RewriteUrl,
    Config,
};
use Innmind\Filesystem\{
    File,
    File\Content,
    Name,
};
use Innmind\Templating\Engine;
use Innmind\Url\{
    Url,
    Path,
};
use Innmind\MediaType\MediaType;

/**
 * @psalm-immutable
 */
final class Markdown implements File
{
    private RewriteUrl $rewrite;
    private Engine $render;
    private Config $config;
    private File $markdown;
    private Path $path;

    public function __construct(
        RewriteUrl $rewrite,
        Engine $templating,
        Config $config,
        File $markdown,
        ?Path $parent,
    ) {
        $name = Path::of($markdown->name()->toString());
        $this->rewrite = $rewrite;
        $this->render = $templating;
        $this->config = $config;
        $this->markdown = $markdown;
        $this->path = $parent ? $parent->resolve($name) : $name;
    }

    public function name(): Name
    {
        /** @psalm-suppress ImpureMethodCall */
        return new Name(
            ($this->rewrite)(Url::of($this->markdown->name()->toString()))->toString(),
        );
    }

    public function content(): Content
    {
        /** @psalm-suppress ImpureMethodCall */
        $parameters = ($this->config->forTemplating())
            (
                'documentation',
                (string) (new \Parsedown)->text(
                    $this->markdown->content()->toString(),
                ),
            )
            ('currentFile', $this->path);

        /** @psalm-suppress ImpureMethodCall */
        return ($this->render)(
            $this->config->template()->entrypoint(),
            $parameters,
        );
    }

    public function mediaType(): MediaType
    {
        return new MediaType('text', 'html');
    }
}
