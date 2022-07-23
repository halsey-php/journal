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
use Innmind\Templating\Engine;
use Innmind\Url\Path;
use Innmind\Immutable\Str;

final class Markdown implements Render
{
    private RewriteUrl $rewrite;
    private Engine $templating;
    private bool $preview;

    public function __construct(
        RewriteUrl $rewrite,
        Engine $templating,
        bool $preview,
    ) {
        $this->rewrite = $rewrite;
        $this->templating = $templating;
        $this->preview = $preview;
    }

    public function __invoke(Config $config, Directory $documentation): Directory
    {
        return $this->map($config, $documentation);
    }

    private function map(
        Config $config,
        Directory $directory,
        Path $parent = null,
    ): Directory {
        return $directory->reduce(
            new Directory\Directory($directory->name()),
            function(Directory $directory, File $file) use ($config, $parent): Directory {
                if ($file instanceof Directory) {
                    $name = Path::of($file->name()->toString().'/');

                    return $directory->add($this->map(
                        $config,
                        $file,
                        $parent ? $parent->resolve($name) : $name,
                    ));
                }

                if (Str::of($file->name()->toString())->matches('~\.md$~')) {
                    return $directory->add(new MarkdownFile(
                        $this->rewrite,
                        $this->templating,
                        $config,
                        $file,
                        $parent,
                        $this->preview,
                    ));
                }

                return $directory->add($file);
            },
        );
    }
}
