# Getting started

## Installation

You can choose to either install the tool locally or globally via composer. By installing it locally you may encounter problems due to versions conflicts with dependencies of your package, that's why it is preferred to install it globally.

Globally:
```sh
composer global require halsey/journal
```

Locally:
```sh
composer require halsey/journal
```

To use the command line tool you can either use `composer global exec 'journal'` or `vendor/bin/journal`.

**Note**: if you added the composer global bin directory in your `$PATH` you can simply use `journal`.

## First steps

Before using the cli tool you need to add 2 things to your project in order to make all this work:
- create a `documentation/` folder at the root of your repository
- create a `.journal` file at the root of your repository

The `documentation/` folder will contain all your markdown files the same way you would normally write them (no special format required). The folder can also contain any extra file you may want such as images, videos, audio or anything else and will be also published to the website so you can safely reference them in your markdown files.

The `.journal` file is a PHP file that will contain the [configuration](configuration.md) of the website. It must look like this:

```php
use Halsey\Journal\Config;

return static function(Config $config): Config
{
    return $config->package('{vendor}', '{package}');
}
```
