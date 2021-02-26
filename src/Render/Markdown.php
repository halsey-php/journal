<?php
declare(strict_types = 1);

namespace Halsey\Journal\Render;

use Halsey\Journal\{
    Render,
    Config,
    File\Markdown as MarkdownFile,
    RewriteUrl,
};
use Innmind\Filesystem\{
    Directory,
    File,
};
use Innmind\Immutable\Str;

final class Markdown implements Render
{
    private RewriteUrl $rewrite;

    public function __construct(RewriteUrl $rewrite)
    {
        $this->rewrite = $rewrite;
    }

    public function __invoke(Config $config, Directory $documentation): Directory
    {
        return $this->map($documentation);
    }

    private function map(Directory $directory): Directory
    {
        return $directory->reduce(
            new Directory\Directory($directory->name()),
            function(Directory $directory, File $file): Directory {
                if ($file instanceof Directory) {
                    return $directory->add($this->map($file));
                }

                if (Str::of($file->name()->toString())->matches('~\.md$~')) {
                    return $directory->add(new MarkdownFile(
                        $this->rewrite,
                        $file,
                    ));
                }

                return $directory->add($file);
            },
        );
    }
}
