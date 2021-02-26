<?php
declare(strict_types = 1);

namespace Halsey\Journal\Templating;

use Halsey\Journal\RewriteUrl;
use Innmind\Url\Url;

final class Helper
{
    private RewriteUrl $rewrite;

    public function __construct(RewriteUrl $rewrite)
    {
        $this->rewrite = $rewrite;
    }

    /**
     * This is a hack as twig doesn't like invokable objects
     */
    public function rewrite(Url $url): Url
    {
        return ($this->rewrite)($url);
    }
}
