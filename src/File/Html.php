<?php
declare(strict_types = 1);

namespace Halsey\Journal\File;

use Halsey\Journal\{
    RewriteUrl,
};
use Innmind\Filesystem\{
    File,
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
use Innmind\Stream\Readable;
use Innmind\Immutable\{
    Set,
    Str,
};
use function Innmind\Immutable\unwrap;

final class Html implements File
{
    private RewriteUrl $rewrite;
    private Reader $read;
    private File $markdown;

    public function __construct(
        RewriteUrl $rewrite,
        Reader $reader,
        Markdown $markdown
    ) {
        $this->rewrite = $rewrite;
        $this->read = $reader;
        $this->markdown = $markdown;
    }

    public function name(): Name
    {
        return $this->markdown->name();
    }

    public function content(): Readable
    {
        $html = $this->markdown->content();
        // if new maps needs to be added maybe it would be a good thing to
        // extract them behind a interface
        $html = $this->mapUrls(($this->read)($html));
        $html = $this->mapHeaders($html);

        return Readable\Stream::ofContent($html->toString());
    }

    public function mediaType(): MediaType
    {
        return $this->markdown->mediaType();
    }

    private function mapUrls(Node $node): Node
    {
        if ($node instanceof A) {
            $href = ($this->rewrite)($node->href());

            return new A(
                $href,
                $node
                    ->attributes()
                    ->put('href', new Attribute('href', $href->toString()))
                    ->values()
                    ->toSetOf(Attribute::class),
                ...unwrap($node->children()),
            );
        }

        $children = unwrap($node->children());

        foreach ($children as $position => $child) {
            $node = $node->replaceChild($position, $this->mapUrls($child));
        }

        return $node;
    }

    private function mapHeaders(Node $node): Node
    {
        if ($node instanceof Element && Str::of($node->name())->matches('~^h\d$~')) {
            $anchor = $this->slugify((new Text)($node));

            return $node
                ->addAttribute(new Attribute(
                    'id',
                    $anchor,
                ))
                ->prependChild(new A(
                    Url::of("#$anchor"),
                    Set::of(
                        Attribute::class,
                        new Attribute('href', "#$anchor"),
                        new Attribute('class', 'anchor'),
                    ),
                    new Node\Text('#'),
                ));
        }

        $children = unwrap($node->children());

        foreach ($children as $position => $child) {
            $node = $node->replaceChild($position, $this->mapHeaders($child));
        }

        return $node;
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
