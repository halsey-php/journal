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
use Innmind\Filesystem\{
    Adapter,
    Directory,
    File,
    Name,
};
use Innmind\Git\{
    Git,
    Repository,
    Revision\Branch,
    Message,
};
use Innmind\Url\Path;

final class Publish implements Command
{
    private OperatingSystem $os;
    private Git $git;
    private Generate $generate;
    private Message $message;

    public function __construct(
        OperatingSystem $os,
        Git $git,
        Generate $generate
    ) {
        $this->os = $os;
        $this->git = $git;
        $this->generate = $generate;
        // we create the message here to make sure the class is loaded before
        // we remove the vendor directory when committing the website
        $this->message = new Message('Publish new documentation');
    }

    public function __invoke(Environment $env, Arguments $arguments, Options $options): void
    {
        /**
         * @psalm-suppress UnresolvableInclude
         * @var callable(Config): Config
         */
        $configure = require ($env->workingDirectory()->resolve(Path::of('.journal'))->toString());
        $config = $configure(new Config($env->workingDirectory()));

        $tmp = $this->os->status()->tmp()->resolve(
            Path::of("halsey-joural-{$this->os->process()->id()->toString()}/"),
        );
        $website = ($this->generate)($config, $tmp);
        $repository = $this->git->repository($env->workingDirectory());

        $this->checkout($repository);
        $this->commit(
            $repository,
            $this->os->filesystem()->mount($env->workingDirectory()),
            $website,
        );
        $this->push($repository);
    }

    public function toString(): string
    {
        return 'publish';
    }

    private function checkout(Repository $repository): void
    {
        $branches = $repository
            ->branches()
            ->all()
            ->filter(static fn($branch) => $branch->toString() === 'gh-pages');

        if ($branches->empty()) {
            $repository
                ->branches()
                ->newOrphan(new Branch('gh-pages'));

            return;
        }

        $repository
            ->checkout()
            ->revision(new Branch('gh-pages'));
    }

    private function commit(
        Repository $repository,
        Adapter $files,
        Directory $website
    ): void {
        $files
            ->all()
            ->filter(static fn(File $file) => !$file->name()->equals(new Name('.git')))
            ->foreach(static fn(File $file) => $files->remove($file->name()));

        $website->foreach(static fn(File $file) => $files->add($file));
        $repository->add(Path::of('.'));
        $repository->commit($this->message);
    }

    private function push(Repository $repository): void
    {
        $repository->push();
    }
}
