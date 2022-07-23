<?php
declare(strict_types = 1);

namespace Halsey\Journal;

use Innmind\Url\Path;

final class Load
{
    public function __invoke(Path $workingDirectory): Config
    {
        /**
         * @psalm-suppress UnresolvableInclude
         * @var callable(Config): Config
         */
        $configure = require $workingDirectory->resolve(Path::of('.journal'))->toString();

        return $configure(Config::of($workingDirectory));
    }
}
