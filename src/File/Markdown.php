<?php
declare(strict_types = 1);

namespace Halsey\Journal\File;

use Innmind\Filesystem\{
    File,
    Name,
};
use Innmind\MediaType\MediaType;
use Innmind\Stream\Readable;

final class Markdown implements File
{
    private File $markdown;

    public function __construct(File $markdown)
    {
        $this->markdown = $markdown;
    }

    public function name(): Name
    {
        return $this->markdown->name();
    }

    public function content(): Readable
    {
        return $this->markdown->content();
    }

    public function mediaType(): MediaType
    {
        return $this->markdown->mediaType();
    }
}
