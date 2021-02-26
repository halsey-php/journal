<?php
declare(strict_types = 1);

namespace Halsey\Journal\File;

use Halsey\Journal\RewriteUrl;
use Innmind\Filesystem\{
    File,
    Name,
};
use Innmind\Url\Url;
use Innmind\MediaType\MediaType;
use Innmind\Stream\Readable;

final class Markdown implements File
{
    private RewriteUrl $rewrite;
    private File $markdown;

    public function __construct(RewriteUrl $rewrite, File $markdown)
    {
        $this->rewrite = $rewrite;
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
        return Readable\Stream::ofContent(
            (string) (new \Parsedown)->text($this->markdown->content()->toString()),
        );
    }

    public function mediaType(): MediaType
    {
        return new MediaType('text', 'html');
    }
}
