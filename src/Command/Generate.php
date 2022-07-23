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
    Console,
};
use Innmind\Url\Path;

final class Generate implements Command
{
    private GenerateWebsite $generate;
    private Load $load;

    public function __construct(
        GenerateWebsite $generate,
        Load $load,
    ) {
        $this->generate = $generate;
        $this->load = $load;
    }

    public function __invoke(Console $console): Console
    {
        $config = ($this->load)($console->workingDirectory());

        ($this->generate)(
            $config,
            $console->workingDirectory()->resolve(Path::of('.tmp_journal/')),
        );

        return $console;
    }

    /**
     * @psalm-pure
     */
    public function usage(): string
    {
        return 'generate';
    }
}
