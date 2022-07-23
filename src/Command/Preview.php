<?php
declare(strict_types = 1);

namespace Halsey\Journal\Command;

use Halsey\Journal\{
    Config,
    Generate,
    Load,
};
use Innmind\CLI\{
    Command,
    Console,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Server\Control\Server;
use Innmind\Url\Path;
use Innmind\Immutable\{
    Str,
    Either,
};

final class Preview implements Command
{
    private OperatingSystem $os;
    private Generate $generate;
    private Load $load;

    public function __construct(
        OperatingSystem $os,
        Generate $generate,
        Load $load,
    ) {
        $this->os = $os;
        $this->generate = $generate;
        $this->load = $load;
    }

    public function __invoke(Console $console): Console
    {
        $config = ($this->load)($console->workingDirectory())->preview();

        $watch = $this->os->filesystem()->watch($config->documentation());
        $tmp = $this->os->status()->tmp()->resolve(
            Path::of("halsey-joural-{$this->os->process()->id()->toString()}/"),
        );
        ($this->generate)($config, $tmp);
        $this->os->control()->processes()->execute(
            Server\Command::background('php')
                ->withShortOption('S', 'localhost:2492')
                ->withWorkingDirectory($tmp),
        );
        $this->openBrowser();
        $console = $console->output(Str::of("Webserver available at: http://localhost:2492\n"));

        return $watch(
            $console,
            function(Console $console) use ($tmp) {
                $console = $console->output(Str::of('folder changed, regenerating...'));
                $config = ($this->load)($console->workingDirectory());
                ($this->generate)($config, $tmp);

                return Either::right($console->output(Str::of(" ok\n")));
            },
        )->match(
            static fn($console) => $console,
            static fn() => throw new \RuntimeException,
        );
    }

    /**
     * @psalm-pure
     */
    public function usage(): string
    {
        return 'preview';
    }

    private function openBrowser(): void
    {
        $this->os->control()->processes()->execute(
            Server\Command::foreground('open')
                ->withArgument('http://localhost:2492'),
        )->wait();
    }
}
