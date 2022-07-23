# CLI commands

`halsey/journal` comes with only 2 commands: `preview` and `generate`.

Each command must be run at the root of your repository so it can correctly find the [`.journal` configuration](configuration.md).

## `journal preview`

This command will start a webserver available at `http://localhost:2492/` and will regenerate the website every time you modify the documentation.

**Note**: the `.journal` configuration is also reloaded each time, however it is not watched for changes.

**Note 2**: the webserver is automatically killed when you stop the preview command.

## `journal generate`

This command will generate the website in the `.tmp_journal/` folder at the root of your repository. This command should only be used to [publish the website via a GitHub Action](publication/github_action.md).
