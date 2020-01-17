# Zumokit Bundle - Documentation

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```bash
composer require zumo/zumokit-bundle
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

If you need a specific version, include it as the second argument of the composer require command:

```bash
composer require zumo/zumokit-bundle "~1.0"
```

> In a default Symfony application that uses Symfony Flex, bundles are enabled/disabled automatically for you when installing/removing them. In case you are not using Symfony Flex you need to look at or edit `config/bundles.php` file.

```php
return [
    // ...
    Zumo\ZumokitBundle\ZumoZumokitBundle::class => ['all' => true],
];
```

## Documentation index

- Installation
- [Getting started](getting-started.md)
