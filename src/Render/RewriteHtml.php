<?php
declare(strict_types = 1);

namespace Halsey\Journal\Render;

use Halsey\Journal\{
    Render,
    Config,
    File\Markdown,
    File\Html,
    RewriteUrl,
};
use Innmind\Filesystem\{
    Directory,
    File,
};
use Innmind\Xml\Reader;

final class RewriteHtml implements Render
{
    private RewriteUrl $rewrite;
    private Reader $reader;

    public function __construct(
        RewriteUrl $rewrite,
        Reader $reader,
    ) {
        $this->rewrite = $rewrite;
        $this->reader = $reader;
    }

    public function __invoke(Config $config, Directory $documentation): Directory
    {
        return $this->map($config, $documentation);
    }

    private function map(Config $config, Directory $directory): Directory
    {
        return $directory->reduce(
            Directory\Directory::of($directory->name()),
            function(Directory $directory, File $file) use ($config): Directory {
                if ($file instanceof Directory) {
                    return $directory->add($this->map($config, $file));
                }

                if ($file instanceof Markdown) {
                    return $directory->add(new Html(
                        $this->rewrite,
                        $this->reader,
                        $file,
                    ));
                }

                return $directory->add($file);
            },
        );
    }
}
