<?php
declare(strict_types = 1);

namespace Halsey\Journal\Command;

use Halsey\Journal\Config;
use Innmind\CLI\{
    Command,
    Command\Arguments,
    Command\Options,
    Environment,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Url\Path;
use Innmind\Immutable\Str;

final class Preview implements Command
{
    private OperatingSystem $os;

    public function __construct(OperatingSystem $os)
    {
        $this->os = $os;
    }

    public function __invoke(Environment $env, Arguments $arguments, Options $options): void
    {
        /**
         * @psalm-suppress UnresolvableInclude
         * @var callable(Config): Config
         */
        $configure = require ($env->workingDirectory()->resolve(Path::of('.journal'))->toString());
        $config = $configure(new Config);

        $watch = $this->os->filesystem()->watch(
            $env->workingDirectory()->resolve($config->documentation()),
        );

        $watch(function() use ($env): void {
            $env->output()->write(Str::of("folder changed\n"));
        });
    }

    public function toString(): string
    {
        return 'preview';
    }
}
