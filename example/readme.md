# README

You can use images:

![](https://media.giphy.com/media/11ISwbgCxEzMyY/giphy.gif)

Or [links](https://github.com/halsey-php), `inline-code` and multiline code:

```php
$curry = fn(callable $fn, ...$first) => fn(...$second) => $fn(...$first, ...$second);
$add = fn($a, $b) => $a + $b;
$add5 = $curry($add, 5);
$sum = $add5(4); // 9
```

Lists
- of
- elements

---

> Some quote

Link to [sub readme](sub/readme.md)

| Table |
|-|
| first line |
| second line |
| `third line` |
