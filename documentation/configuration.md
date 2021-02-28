# Configuration

All the configuration is done the `.journal` file at the root of your repository.

## Specify the package the documentation is for

The first config to specify is the vendor and package name so that the tool can generate the correct urls for your website. You do so like this.

```php
use Halsey\Journal\Config;

return static function(Config $config): Config
{
    return $config->package('{vendor}', '{package}');
}
```

`{vendor}` and `{package}` are to be replaced by the values you use in your `composer.json` to reference your package.

**Note**: In some cases the vendor and the package names are not identical to the organization/user and repository names, you can specify them with the 3rd and 4th arguments respectively.

## Add links in the menu

You can add 3 types of elements to the menu: links, sections and external links. Each one can be composed with other 2 types.

**Note**: depending on the template used the displayed tree may be limited, the default `raw` template only allow 1 level of nested entries.

**Note 2**: entries with sub entries can be configured to always be opened by calling the `->alwaysOpen()` method on the parent entry.

### Links

The links are relative paths to the markdown files inside your documentation folder, you can add them like so:

```php
use Halsey\Journal\{
    Config,
    Menu\Entry,
};
use Innmind\Url\Path;

return static function(Config $config): Config
{
    return $config
        // other config options
        ->menu(Entry::markdown(
            'The name to display in the menu',
            Path::of('relative/path/to/markdown.md'),
        ));
}
```

To add a sub menu to this entry you can additional entries as 3rd argument.

### Sections

A section is a collection of entries and can be configured like so:

```php
use Halsey\Journal\{
    Config,
    Menu\Entry,
};
use Innmind\Url\Path;

return static function(Config $config): Config
{
    return $config
        // other config options
        ->menu(Entry::section(
            'Section name to display in the menu',
            Entry::markdown(
                'Sub entry',
                Path::of('relative/path/to/markdown.md'),
            ),
            // you can add as many as you want
        ));
}
```

### External links

Use this kind of entry to point elsewhere than the local documentation.

```php
use Halsey\Journal\{
    Config,
    Menu\Entry,
};
use Innmind\Url\Url;

return static function(Config $config): Config
{
    return $config
        // other config options
        ->menu(Entry::externalLink(
            'The name to display in the menu',
            Url::of('http://example.com'),
        ));
}
```

## Changing the documentation folder

By default the folder used is `documentation/` but you can change it in case you already have a documentation named differently.

```php
use Halsey\Journal\Config;
use Innmind\Url\Path;

return static function(Config $config): Config
{
    return $config
        // other config options
        ->locatedAt(Path::of('docs/'));
}
```

**Important**: the specified path must be relative from the root of your repository and must end with a `/` to indicate it's a directory. If you don't the config will throw an exception.
