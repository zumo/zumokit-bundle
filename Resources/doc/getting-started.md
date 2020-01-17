# Zumokit Bundle - Documentation

## Configure the Bundle

Add the following parameters into your Symfony's framework configuration (`config/framework.yml`) and implement appropriate values.

```yaml
# ZumoKit Bundle minimal configuration
blockstar_zumokit:
    app_name: ~                         # The name of the integrator's app
    app_id: ~                           # The ID of the integrator's app
    api_key: ~                          # The API key
    api_url: ~                          # Url of the ZumoKit API service
```

Some parameters below act as credentials and should be considered secret. The recommended way is to have the sensitive values stored in environment variables, which you then reference here using Symfony %env% notation. See the example below.

```yaml
# ZumoKit Bundle minimal configuration
zumokit:
    app_name: ~
    app_id: '%env(ZUMO_APP_ID)%'
    api_key: '%env(ZUMO_API_KEY)%'
    api_url: '%env(ZUMO_API_URL)%'
```

## Configure the Firewall

ZumoKit Bundle exposes a few endpoints that must be added as protected endpoints in your application firewall.

All bundle endpoints share their route prefix: `wallet`.

Add the following parameters into your Symfony's security configuration (`config/packages/security.yaml`).

```yaml
security:
    # ...
    providers:
        jwt_user_provider:
            id: security.jwt_user_provider

    firewalls:
        zumokit:
            pattern: ^/wallet
            security: true
            anonymous: false
            provider: jwt_user_provider
            lexik_jwt:
                authorization_header:
                    enabled: true
                    prefix: Bearer
                throw_exceptions: true
                create_entry_point: true
                authentication_provider: lexik_jwt_authentication.security.authentication.provider

    access_control:
        - { path: ^/wallet, roles: IS_AUTHENTICATED_FULLY }
        # ...
```

If you have multiple authentication mechanisms implemented on your platform, make sure bundle’s endpoints are attached to the correct firewall.

## Integration

Your system must implement interfaces and use traits provided by the Bundle on the entities described below.

Implement UserInerface and use UserTrait on entity representing user (`src\Entity\User.php`).

```php
namespace App\Entity;

use Zumo\ZumokitBundle\Model\UserInterface;
use Zumo\ZumokitBundle\Model\UserTrait;
use Zumo\ZumokitBundle\Model\WalletInterface;

class User implements UserInterface
{
    use UserTrait;

    /**
     * App user's cryptographic wallets/accounts
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Wallet", mappedBy="user", cascade={"persist"})
     * @ORM\OrderBy({"symbol" = "DESC"})
     * @Groups({"settings_account", "settings", "user_profile_public", "address_book", "fund_request"})
     */
    private $wallets;

    public function __construct()
    {
        // ...
        $this->wallets = new ArrayCollection();
    }

    /**
     * Gets all wallets
     *
     * @return Collection|WalletInterface[]
     */
    public function getWallets(): Collection
    {
        return $this->wallets;
    }

    /**
     * Adds wallet
     *
     * @param WalletInterface $wallet
     * @return User
     */
    public function addWallet(WalletInterface $wallet): self
    {
        if (!$this->wallets->contains($wallet)) {
            $this->wallets[] = $wallet;
            $wallet->setUser($this);
        }
        return $this;
    }

    /**
     * Remove wallet
     *
     * @param WalletInterface $wallet
     * @return User
     */
    public function removeWallet(WalletInterface $wallet): self
    {
        if ($this->wallets->contains($wallet)) {
            $this->wallets->removeElement($wallet);
            // set the owning side to null (unless already changed)
            if ($wallet->getUser() === $this) {
                $wallet->setUser(null);
            }
        }
        return $this;
    }

    // ...
}
```

Implement WalletInerface and use WalletTrait on entity representing wallet (`src\Entity\Wallet.php`).

```php
use Zumo\ZumokitBundle\Model\WalletInterface;
use Zumo\ZumokitBundle\Model\WalletTrait;
use Zumo\ZumokitBundle\Model\UserInterface;

class Wallet implements WalletInterface
{
    use WalletTrait;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="wallets")
     * @Groups({"wallet_root"})
     */
    private $user;

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface|null $user
     * @return Wallet
     */
    public function setUser(?UserInterface $user = null): self
    {
        $this->user = $user;
        return $this;
    }
}
```

Implement all other required methods which are required by the implemented entity interfaces provided by ZumoKit Bundle.

## Authentication mechanism

Currently the only supported authentication mechanism is with JWT (JSON web token). This means your backend must
authenticate users by issuing and validating JWT tokens. The bundle will hook up to the authentication success event.

The recommended way to get JWT support to your Symfony backend is to use the Lexik JWT Authentication Bundle for
Symfony (`lexik/jwt-authentication-bundle`, tested with version 2.6).

## Verifying that integration works

The Healthcheck endpoint allows clients to check the status of the integration with ZumoKit. To use this endpoint, make the following request:

`GET <YOUR-APP-PLATFORM-URI>/wallet/healthcheck`

You must include the ZumoKit API KEY in the request header (header name is `Api-Key`). The response will return
information about the app for which the API key was given. On success the response is:

`{"status" => "OK", "message" => "Integration health check passed."}`

And the status code is 200.

There are two error responses possible. The first means the health check was performed
but has failed - for instance your app connected but disabled.

`{"status" => "Error", "message" => "Integration health check failed."}`

The status code is 400.

The following error response means that the health check itself could not
be completed. This is a usually a request-related error.

`{"status" => "Error", "message" => "Could not perform health check."}`

The status code is 400.

## Documentation index

- [Installation](installation.md)
- Getting started
