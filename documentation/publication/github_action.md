# Publish via GitHub Action (recommended)

The easiest way to automatically publish your documentation to GitHub Pages is to make it run in your CI so you don't to think of it anymore.

Do so by creating the file `.github/workflows/documentation.yml` (the actual yaml file name doesn't have much of an importance) with the following content:

```yml
name: Documentation

on:
    push:
        branches: [master] # you can modify this to change when the website is published

jobs:
  publish:
    runs-on: ubuntu-latest
    name: 'Publish documentation'
    steps:
      - name: Checkout
        uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4' # change the version to the one supported by your project
      - name: Install halsey/journal
        run: composer global require halsey/journal
      - name: Generate
        run: composer global exec 'journal generate'
      - name: Push
        uses: peaceiris/actions-gh-pages@v3
        with:
          github_token: ${{ secrets.GITHUB_TOKEN }}
          publish_dir: ./.tmp_journal/
```

**Note**: even though you can change the php version to fit the one you use, it must also be one compatible with [`halsey/journal`](https://packagist.org/packages/halsey/journal)
