<?php
declare(strict_types = 1);

namespace Halsey\Journal\Command;

use Halsey\Journal\{
    Config,
    Generate as GenerateWebsite,
    Load,
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
    private Load $load;

    public function __construct(
        OperatingSystem $os,
        GenerateWebsite $generate,
        Load $load
    ) {
        $this->os = $os;
        $this->generate = $generate;
        $this->load = $load;
    }

    public function __invoke(Environment $env, Arguments $arguments, Options $options): void
    {
        $config = ($this->load)($env->workingDirectory());

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
