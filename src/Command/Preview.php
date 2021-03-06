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
    Command\Arguments,
    Command\Options,
    Environment,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Server\Control\Server;
use Innmind\Url\Path;
use Innmind\Immutable\Str;

final class Preview implements Command
{
    private OperatingSystem $os;
    private Generate $generate;
    private Load $load;

    public function __construct(
        OperatingSystem $os,
        Generate $generate,
        Load $load
    ) {
        $this->os = $os;
        $this->generate = $generate;
        $this->load = $load;
    }

    public function __invoke(Environment $env, Arguments $arguments, Options $options): void
    {
        $config = ($this->load)($env->workingDirectory());

        $watch = $this->os->filesystem()->watch($config->documentation());
        $tmp = $this->os->status()->tmp()->resolve(
            Path::of("halsey-joural-{$this->os->process()->id()->toString()}/"),
        );
        $output = static function(string $out) use ($env): void {
            $env->output()->write(Str::of($out));
        };
        ($this->generate)($config, $tmp);
        $this->os->control()->processes()->execute(
            Server\Command::background('php')
                ->withShortOption('S', 'localhost:2492')
                ->withWorkingDirectory($tmp),
        );
        $this->openBrowser();
        $output("Webserver available at: http://localhost:2492\n");

        $watch(function() use ($output, $tmp, $env): void {
            $output('folder changed, regenerating...');
            $config = ($this->load)($env->workingDirectory());
            ($this->generate)($config, $tmp);
            $output(" ok\n");
            $this->openBrowser();
        });
    }

    public function toString(): string
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
