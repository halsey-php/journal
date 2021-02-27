<?php
declare(strict_types = 1);

namespace Halsey\Journal;

use Innmind\Url\Url;
use Innmind\Immutable\Str;

final class RewriteUrl
{
    public function __invoke(Url $url): Url
    {
        $string = Str::of($url->toString());

        if ($string->toLower()->endsWith('readme.md')) {
            return Url::of($string->dropEnd(9)->append('index.html')->toString());
        }

        if ($string->takeEnd(3)->toLower()->equals(Str::of('.md'))) {
            return Url::of($string->dropEnd(3)->append('.html')->toString());
        }

        return $url;
    }
}
