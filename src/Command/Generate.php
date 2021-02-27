<?php
declare(strict_types = 1);

namespace Halsey\Journal\Command;

use Halsey\Journal\{
    Config,
    Generate as GenerateWebsite,
};
use Innmind\CLI\{
    Command,
    Command\Arguments,
    Command\Options,
    Environment,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Url\Path;

final class Generate implements Command
{
    private OperatingSystem $os;
    private GenerateWebsite $generate;

    public function __construct(
        OperatingSystem $os,
        GenerateWebsite $generate
    ) {
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

        ($this->generate)(
            $config,
            $env->workingDirectory()->resolve(Path::of('.tmp_journal/')),
        );
    }

    public function toString(): string
    {
        return 'generate';
    }
}
