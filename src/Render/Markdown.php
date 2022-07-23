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

    public function __construct(
        RewriteUrl $rewrite,
        Engine $templating,
    ) {
        $this->rewrite = $rewrite;
        $this->templating = $templating;
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
            Directory\Directory::of($directory->name()),
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
                    ));
                }

                return $directory->add($file);
            },
        );
    }
}
