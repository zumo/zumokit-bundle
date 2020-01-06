# Zumokit Bundle

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
