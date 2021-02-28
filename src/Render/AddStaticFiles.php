<?php
declare(strict_types = 1);

namespace Halsey\Journal\Render;

use Halsey\Journal\{
    Render,
    Config,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Filesystem\{
    Directory,
    Name,
};
use Innmind\Url\Path;

final class AddStaticFiles implements Render
{
    private OperatingSystem $os;
    private Path $self;

    public function __construct(OperatingSystem $os, Path $self)
    {
        $this->os = $os;
        $this->self = $self;
    }

    public function __invoke(Config $config, Directory $documentation): Directory
    {
        $static = $this->self->resolve(
            Path::of("templates/{$config->template()->toString()}/static/"),
        );

        return $documentation->add(new Directory\Directory(
            new Name('static'),
            $this->os->filesystem()->mount($static)->all(),
        ));
    }
}
