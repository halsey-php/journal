<?php
declare(strict_types = 1);

namespace Halsey\Journal\Command;

use Halsey\Journal\{
    Config,
    Generate,
};
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
    private Generate $generate;

    public function __construct(OperatingSystem $os, Generate $generate)
    {
        $this->os = $os;
        $this->generate = $generate;
    }

    public function __invoke(Environment $env, Arguments $arguments, Options $options): void
    {
        /**
         * @psalm-suppress UnresolvableInclude
         * @var callable(Config): Config
         */
        $configure = require ($env->workingDirectory()->resolve(Path::of('.journal'))->toString());
        $config = $configure(new Config($env->workingDirectory()));

        $watch = $this->os->filesystem()->watch($config->documentation());
        $tmp = $this->os->status()->tmp()->resolve(
            Path::of("halsey-joural-{$this->os->process()->id()->toString()}/"),
        );
        $output = static function(string $out) use ($env): void {
            $env->output()->write(Str::of($out));
        };

        $watch(function() use ($output, $tmp, $config): void {
            $output('folder changed, regenerating...');
            ($this->generate)($config, $tmp);
            $output(" ok\n");
        });
    }

    public function toString(): string
    {
        return 'preview';
    }
}
