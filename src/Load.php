<?php
declare(strict_types = 1);

namespace Halsey\Journal;

use Innmind\OperatingSystem\Filesystem;
use Innmind\Url\Path;

final class Load
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function __invoke(Path $workingDirectory): Config
    {
        /**
         * @psalm-suppress MixedArgumentTypeCoercion
         * @var callable(Config): Config
         */
        $configure = $this
            ->filesystem
            ->require($workingDirectory->resolve(Path::of('.journal')))
            ->match(
                static fn(callable $configure) => $configure,
                static fn() => static fn(Config $config) => $config,
            );

        return $configure(Config::of($workingDirectory));
    }
}
