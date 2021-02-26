<?php
declare(strict_types = 1);

namespace Halsey\Journal\Render;

use Halsey\Journal\{
    Render,
    Config,
};
use Innmind\Filesystem\Directory;

final class All implements Render
{
    /** @var list<Render> */
    private array $renders;

    public function __construct(Render $first, Render ...$rest)
    {
        $this->renders = [$first, ...$rest];
    }

    public function __invoke(Config $config, Directory $documentation): Directory
    {
        foreach ($this->renders as $render) {
            $documentation = $render($config, $documentation);
        }

        return $documentation;
    }
}
