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
use Innmind\Xml\{
    Reader,
    Node,
    Attribute,
};
use Innmind\Html\Element\A;
use Innmind\MediaType\MediaType;
use Innmind\Stream\Readable;
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

        return Readable\Stream::ofContent(
            $this->map(($this->read)($html))->toString(),
        );
    }

    public function mediaType(): MediaType
    {
        return $this->markdown->mediaType();
    }

    private function map(Node $node): Node
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
            $node = $node->replaceChild($position, $this->map($child));
        }

        return $node;
    }
}
