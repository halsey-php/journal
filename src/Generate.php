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

    public function __invoke(Config $config, Path $generateAt): void
    {
        $tmp = $this->os->filesystem()->mount($generateAt);
        $_ = $tmp->all()->foreach(static fn($file) => $tmp->remove($file->name()));
        $documentation = Directory\Directory::of(
            new Name('root'),
            $this->os->filesystem()->mount($config->documentation())->all(),
        );

        $_ = ($this->render)($config, $documentation)->foreach(
            static fn($file) => $tmp->add($file),
        );
    }
}
