<?php
declare(strict_types = 1);

namespace Halsey\Journal;

use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Filesystem\{
    File,
    Directory,
    Name,
};
use Innmind\Url\Path;

final class Generate
{
    private OperatingSystem $os;
    private Render $render;

    public function __construct(OperatingSystem $os, Render $render)
    {
        $this->os = $os;
        $this->render = $render;
    }

    public function __invoke(Config $config, Path $generateAt): Directory
    {
        $tmp = $this->os->filesystem()->mount($generateAt);
        $tmp->all()->foreach(static fn($file) => $tmp->remove($file->name()));
        $documentation = new Directory\Directory(
            new Name('root'),
            $this->os->filesystem()->mount($config->documentation())->all(),
        );

        $documentation = ($this->render)($config, $documentation);

        $documentation->foreach(static function(File $file) use ($tmp): void {
            $tmp->add($file);
        });

        return new Directory\Directory(
            new Name('root'),
            $tmp->all(),
        );
    }
}
