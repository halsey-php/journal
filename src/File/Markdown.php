<?php
declare(strict_types = 1);

namespace Halsey\Journal\File;

use Halsey\Journal\{
    RewriteUrl,
    Config,
};
use Innmind\Filesystem\{
    File,
    Name,
};
use Innmind\Templating\Engine;
use Innmind\Url\Url;
use Innmind\MediaType\MediaType;
use Innmind\Stream\Readable;
use Innmind\Immutable\Map;

final class Markdown implements File
{
    private RewriteUrl $rewrite;
    private Engine $render;
    private Config $config;
    private File $markdown;

    public function __construct(
        RewriteUrl $rewrite,
        Engine $templating,
        Config $config,
        File $markdown
    ) {
        $this->rewrite = $rewrite;
        $this->render = $templating;
        $this->config = $config;
        $this->markdown = $markdown;
    }

    public function name(): Name
    {
        return new Name(
            ($this->rewrite)(Url::of($this->markdown->name()->toString()))->toString(),
        );
    }

    public function content(): Readable
    {
        /** @var Map<string, mixed> */
        $parameters = Map::of('string', 'mixed');

        return ($this->render)(
            $this->config->template()->entrypoint(),
            ($this->config->forTemplating())
                (
                    'documentation',
                    (string) (new \Parsedown)->text(
                        $this->markdown->content()->toString(),
                    ),
                ),
        );
    }

    public function mediaType(): MediaType
    {
        return new MediaType('text', 'html');
    }
}
