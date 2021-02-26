<?php
declare(strict_types = 1);

namespace Halsey\Journal;

use Innmind\Filesystem\Directory;

interface Render
{
    public function __invoke(Config $config, Directory $documentation): Directory;
}
