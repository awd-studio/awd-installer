# AWD Installer

## A plugin to use custom installers for composer

### Installation:
```bash
composer install awd-studio/awd-installer
```

### Usage:

Add a block to the `extra` section with a name `awd-additions`, and set the path that you need to install extra-libraries to.

```json
{
    "extra": {
        "awd-additions": "path_that_you_need/{$name}/"
    }
}
```

Then, add an extra-library to `repository` section, with the type `awd-addition`.

```json
{
    "repositories": [    
        {
            "type": "package",
            "package": {
                "name": "name-of/my-package",
                "version": "1.0",
                "type": "awd-addition",
                "dist": {
                    "url": "https://my.repo/extra-lib..zip",
                    "type": "zip"
                },
                "bin": [
                    "runme"
                ]
            }
        }
    ]
}
```

If you don't need to use library's binaries - that section is nor required.

After those actions you can just require the extra library either with the cli, or in `required` section:

```bash
composer require name-of/my-package
``` 
or
```json
{
    "require": {
        "name-of/my-package": "^1.0"
    }
}
```

Plugin installs the package into the directory, that was set in `extra` section, with all binaries from the package settings.
