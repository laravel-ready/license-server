# Laravel Theme Store

[![EgoistDeveloper Laravel License Server](https://preview.dragon-code.pro/EgoistDeveloper/Laravel-License-Server.svg?brand=laravel)](https://github.com/laravel-ready/license-server)

[![Stable Version][badge_stable]][link_packagist]
[![Unstable Version][badge_unstable]][link_packagist]
[![Total Downloads][badge_downloads]][link_packagist]
[![License][badge_license]][link_license]


**License Server** package, which is a Laravel package that allows you to manage your Laravel applications license. You can use it with any product or service. License Server comes with the agnostic license management system, which allows you to manage your licenses in a simple and easy way. Just add license relation to any product model then you can work in your logic.

This package requires [license-connector](https://github.com/laravel-ready/license-connector) package. **License Connector** is client implementation for License Server. Package for the client makes a request to **License Server** and gets a response.

## Installation (for Host App)

Get via composer

`composer require laravel-ready/license-server`

Publish store migrations and migrate

```bash
# publish migrations
php artisan vendor:publish --tag=license-server-migrations

# apply migrations
php artisan migrate --path=/database/migrations/laravel-ready/license-server
```

Configs are very important. You can find them in [license-server.php](config/license-server.php) file. You should read all configs and configure for your needs.

```bash
#publish configs
php artisan vendor:publish --tag=license-server-configs
```

## Model Relations

Every license must-have product, because we need to know what it is licensed for. The client application will send this information to the License Server. Then we can check if the license is valid for given the product.

Product model can be any model that you want to be licensed. Add [Licensable](src/Traits/Licensable.php) trait to your product model.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use LaravelReady\LicenseServer\Traits\Licensable;

class Product extends Model
{
    use HasFactory, Licensable;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        ...
    ];

    ...
}
```

## Service Methods

Add in your namespace list:

`use LaravelReady\LicenseServer\Services\LicenseServer;`

and product model

`use App\Models\Product;`

### addLicense

First, we need to know licensing models. This package supports two types of licensing models: **to Domain** and **to User**. Both of them are valid. If you want to add license to domain, you must pass `domain` parameter. If you want to add license to user, you must pass `userId` parameter. Also, when you pass both of them, you will get domain license.

```php
// get licensable product
$product = Product::find(1);
$user = User::find(1);

// add license to domain
$license = LicenseService::addLicense($product, 'example.com', $user->id);

// add license to user
$license = LicenseService::addLicense($product, null, $user->id);

// with expiration in days (see configs for defaults)
$license = LicenseService::addLicense($product, null, $user->id, 999);

// with lifetime license (see configs for defaults)
$license = LicenseService::addLicense($product, null, $user->id, null, true);

// with trial license (see configs for defaults)
$license = LicenseService::addLicense($product, null, $user->id, null, false, true);
```

- If you provide domain, then the license will be added to the domain. If you don't provide domain, then the license will be added to the user (*in this case user id is required.*).
- Other parameters are optional and do not forget to configure configs.
- This method returns `LaravelReady\LicenseServer\Models\License` model.
- All license keys are in UUID format.

### getLicenseBy*

- *getLicenseByKey*: get license by license key.
  - `LicenseService::getLicenseByKey(string $licenseKey)`
- *getLicenseByUserId*: get license by user id and license key.
  - `LicenseService::getLicenseByUserId(int $userId, string $licenseKey = null)`
- *getLicenseByDomain*: get license by domain and license key.
  - `LicenseService::getLicenseByDomain(string $domain, string $licenseKey = null)`

### checkLicenseStatus

```php
// license key in uuid format
$licenseKey = "46fad906-bc51-435f-9929-db46cb4baf13";

// check license status
$licenseStatus = LicenseService::checkLicenseStatus($licenseKey);
```

Returns "active", "inactive", "suspended", "expired", "invalid-license-key" and "no-license-found".

### setLicenseStatus

```php
// license key in uuid format
$licenseKey = "46fad906-bc51-435f-9929-db46cb4baf13";

// check license status
$licenseStatus = LicenseService::setLicenseStatus($licenseKey, "suspended");
```

You can only set `active`, `inactive`, `suspended` status.


## Domain Validation

License Server uses a cache to store the public tld list. See tld list at https://data.iana.org/TLD/tlds-alpha-by-domain.txt

The tls cache is will be stored at the `storage/license-server/iana-tld-list.txt` file.

⚠️ **Warning**: In development you may use domain like `example.test` etc. but you won't pass domain validation because the `test` is not valid tld.


[badge_downloads]:      https://img.shields.io/packagist/dt/laravel-ready/license-server.svg?style=flat-square

[badge_license]:        https://img.shields.io/packagist/l/laravel-ready/license-server.svg?style=flat-square

[badge_stable]:         https://img.shields.io/github/v/release/laravel-ready/license-server?label=stable&style=flat-square

[badge_unstable]:       https://img.shields.io/badge/unstable-dev--main-orange?style=flat-square

[link_license]:         LICENSE

[link_packagist]:       https://packagist.org/packages/laravel-ready/license-server

