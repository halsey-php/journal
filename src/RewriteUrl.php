<?php
declare(strict_types = 1);

namespace Halsey\Journal;

use Innmind\Url\{
    Url,
    Authority,
    Path,
};
use Innmind\Immutable\Str;

/**
 * @psalm-immutable
 */
final class RewriteUrl
{
    public function __invoke(Url $url): Url
    {
        if (!$url->authority()->equals(Authority::none())) {
            // then it must be an external link
            return $url;
        }

        $path = Str::of($url->path()->toString());

        if ($path->toLower()->endsWith('readme.md')) {
            return $url->withPath(
                Path::of($path->dropEnd(9)->append('index.html')->toString()),
            );
        }

        if ($path->takeEnd(3)->toLower()->equals(Str::of('.md'))) {
            return $url->withPath(
                Path::of($path->dropEnd(3)->append('.html')->toString()),
            );
        }

        return $url;
    }
}
