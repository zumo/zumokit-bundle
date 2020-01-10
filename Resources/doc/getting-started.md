# Zumokit Bundle - Documentation

## Configure the Bundle

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

## Implement interfaces and use traits

### User entity (src/Entity/User.php)

```php
use Zumo\ZumokitBundle\Model\UserInterface;
use Zumo\ZumokitBundle\Model\UserTrait;

class User implements UserInterface
{
    use UserTrait;

    ...

}
```

If you need to use some user model then extend it with your User entity:

```php
use Some\User\Model as BaseUser; // FOS\UserBundle\Model\User

class User extends BaseUser implements UserInterface
{
    ...
}
```

Implement all methods required by the user interface.

### Wallet entity (src/Entity/Wallet.php)

```php

use Zumo\ZumokitBundle\Model\WalletInterface;
use Zumo\ZumokitBundle\Model\WalletTrait;

class User extends BaseUser implements EntityInterface, WalletInterface
{
    use WalletTrait;

    ...

}
```

## Implement methods to implement interfaces

Implement all required methods to your entities which uses Zumokit Bundle entity interfaces.

## Documentation index

- [Installation](installation.md)
- Getting started
