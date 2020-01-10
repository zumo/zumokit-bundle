# Zumokit Bundle - Documentation

## Installation

Install the latest stable version of the ZumoKit Bundle via Composer:

```bash
composer require zumo/zumokit-bundle
```

This will choose the best version for your project, add it to composer.json and download its code into the vendor/ directory. If you need a specific version, include it as the second argument of the composer require command:

```bash
composer require zumo/zumokit-bundle "~1.0"
```

### If yor application is not based on Symfony Flex

If your application is not based on Symfony Flex you need to manually enable bundle by adding it to the list of registered bundles in `config/bundles.php`.

```php
return [
    // ...
    Zumo\ZumokitBundle\ZumoZumokitBundle::class => ['all' => true],
];
```

## Documentation index

- Installation
- [Getting started](getting-started.md)
