<?php
declare(strict_types = 1);

namespace Halsey\Journal\File;

use Halsey\Journal\RewriteUrl;
use Innmind\Filesystem\{
    File,
    File\Content,
    Name,
};
use Innmind\Url\Url;
use Innmind\Xml\{
    Reader,
    Node,
    Element,
    Attribute,
    Visitor\Text,
};
use Innmind\Html\Element\A;
use Innmind\MediaType\MediaType;
use Innmind\Immutable\{
    Set,
    Str,
    Sequence,
};

/**
 * @psalm-immutable
 */
final class Html implements File
{
    private RewriteUrl $rewrite;
    private Reader $read;
    private File $markdown;

    public function __construct(
        RewriteUrl $rewrite,
        Reader $reader,
        Markdown $markdown,
    ) {
        $this->rewrite = $rewrite;
        $this->read = $reader;
        $this->markdown = $markdown;
    }

    public function name(): Name
    {
        return $this->markdown->name();
    }

    public function content(): Content
    {
        $html = $this->markdown->content();
        // if new maps needs to be added maybe it would be a good thing to
        // extract them behind a interface
        $html = $this->mapUrls(($this->read)($html)->match(
            static fn($node) => $node,
            static fn() => throw new \RuntimeException,
        ));
        $html = $this->mapHeaders($html);

        return Content\Lines::ofContent($html->toString());
    }

    public function mediaType(): MediaType
    {
        return $this->markdown->mediaType();
    }

    private function mapUrls(Node $node): Node
    {
        if ($node instanceof A) {
            /** @psalm-suppress ImpureMethodCall */
            $href = ($this->rewrite)($node->href());

            return A::of(
                $href,
                Set::of(...$node->attributes()->values()->toList()),
                $node->children(),
            );
        }

        return $node->mapChild($this->mapUrls(...));
    }

    private function mapHeaders(Node $node): Node
    {
        if ($node instanceof Element && Str::of($node->name())->matches('~^h\d$~')) {
            $anchor = $this->slugify(Text::of()($node));

            /** @psalm-suppress InvalidArgument */
            return $node
                ->addAttribute(Attribute::of(
                    'id',
                    $anchor,
                ))
                ->prependChild(A::of(
                    Url::of("#$anchor"),
                    Set::of(
                        Attribute::of('href', "#$anchor"),
                        Attribute::of('class', 'anchor'),
                    ),
                    Sequence::of(Node\Text::of('#')),
                ));
        }

        return $node->mapChild($this->mapHeaders(...));
    }

    private function slugify(string $header): string
    {
        return Str::of($header)
            ->toLower()
            ->pregReplace('~[^a-z0-9 ]~', '')
            ->replace(' ', '-')
            ->toString();
    }
}
