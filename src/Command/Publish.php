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
    private Load $load;

    public function __construct(
        OperatingSystem $os,
        Git $git,
        Generate $generate,
        Load $load,
    ) {
        $this->os = $os;
        $this->git = $git;
        $this->generate = $generate;
        $this->load = $load;
    }

    public function __invoke(Console $console): Console
    {
        $config = ($this->load)($console->workingDirectory());

        $tmp = $this->os->status()->tmp()->resolve(
            Path::of("halsey-joural-{$this->os->process()->id()->toString()}/"),
        );
        $website = ($this->generate)($config, $tmp);
        $repository = $this->git->repository($console->workingDirectory())->match(
            static fn($repository) => $repository,
            static fn() => throw new \RuntimeException,
        );
        $head = $repository->head()->match(
            static fn($head) => $head,
            static fn() => throw new \RuntimeException,
        );

        $this->checkout($repository);
        $this->commit(
            $repository,
            $this->os->filesystem()->mount($console->workingDirectory()),
            $website,
        );
        $this->push($repository);
        $repository->checkout()->revision($head);

        return $console;
    }

    /**
     * @psalm-pure
     */
    public function usage(): string
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
                ->newOrphan(Branch::of('gh-pages'));

            return;
        }

        $repository
            ->checkout()
            ->revision(Branch::of('gh-pages'));
    }

    private function commit(
        Repository $repository,
        Adapter $files,
        Directory $website,
    ): void {
        $_ = $files
            ->all()
            ->filter(static fn(File $file) => !$file->name()->equals(new Name('.git')))
            ->filter(static fn(File $file) => !$file->name()->equals(new Name('vendor')))
            ->filter(static fn(File $file) => !$file->name()->equals(new Name('.gitignore')))
            ->foreach(static fn(File $file) => $files->remove($file->name()));

        $_ = $website->foreach(static fn(File $file) => $files->add($file));
        $repository->add(Path::of('.'));

        $_ = $repository->commit(Message::of('Publish new documentation'))->match(
            static fn() => null,
            static fn() => null, // nothing to do if it failed, this happens when there is nothing to commit
        );
    }

    private function push(Repository $repository): void
    {
        $repository->push();
    }
}
