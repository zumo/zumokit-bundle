# Zumo ZumoKit Bundle for Symfony

[![License: MIT](https://img.shields.io/badge/License-MIT-blue?style=flat-square)](LICENSE)

Symfony bundle for Zumo's ZumoKit service. This repository contains code for integration into application based on Symfony 4.x or newer.

> Please note, during Zumo's alpha and beta period, the API is subject to change and may impact bundle.

[ZUMO](https://zumo.money/) · [ZumoKit for Developers](https://developers.zumo.money/)

--------

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)

## Requirements

- [Application based on Symfony 4.1.x](https://symfony.com/)
- [Composer](https://getcomposer.org/)

## Installation

### 1. Install the bundle

Install the latest stable version of the ZumoKit Bundle via Composer:

```bash
composer require zumo/zumokit-bundle
```

This will choose the best version for your project, add it to composer.json and download its code into the vendor/ directory. If you need a specific version, include it as the second argument of the composer require command:

```bash
composer require zumo/zumokit-bundle "~1.0"
```

#### If yor application is not based on Symfony Flex

If your application is not based on Symfony Flex you need to manually enable bundle by adding it to the list of registered bundles in `config/bundles.php`.

```php
return [
    // ...
    Zumo\ZumokitBundle\ZumoZumokitBundle::class => ['all' => true],
];
```

### 2. Configure the Bundle

Open `config/packages/framework.yml` in your Symfony project root, and add in the following
configuration parameters:

> **Some parameters below act as credentials and should be considered secret. The recommended
way is to have the sensitive values stored in environment variables, which you then reference
here using Symfony %env(ENV_VAR_NAME)% notation.**

```yaml
zumokit:
    public_key: '...'                             # Path to the public key file for signing tokens.
    private_key: ...'                             # Path to the private key file for signing tokens.
    passphrase: '...'                             # The private key passphrase.
    app_name: '...'                               # The app's name, available in the admin panel.
    app_id: '...'                                 # The ID of the app, available in the admin panel.
    api_key: '...'                                # The API key, available in the admin panel.
    api_url: '...'                                # The URL of API.
    primary_domain: '...'                         # Primary domain name.
    domains:                                      # List of domains the app is allowed to connect from.
      - ...
      - ...
    security:
        verify_ssl: true
        user_class: App\Entity\User               # FQ class name of the User entity.
        repository_class: App\Repository\UserRepository
        login_event: Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent   # FQ class name of the login event to handle. ; For stateful logins use Symfony\Security\InteractiveLoginEvent
        jwt:
            private_key: '...'
            public_key: '...'
            passphrase: '...'
            shared_key: '...'
            shared_secret: '...'
            keyset: '...'
            well_known_url: 'adsf'
    metadata:                                     # Machine metadata string (generated).
        enable: true
        id: ...
        secret: 'o'
        endpoint_url: '/m/api/machine/ID/metadata.enc'
        root: 'dd'
    user_registration:                            # User registration settings.
        enable: true                              # Enable/disable remote user registration.
        event: 'App\Event\Reg'
```

### 3. Run tests make sure everything works

Run `phpunit run` in the root folder.
