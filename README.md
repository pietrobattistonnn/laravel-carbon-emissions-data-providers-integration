# Emissions Ecosystem

This repository contains:

* `packages/emissions-core`
* `packages/lune-module`

Each package follows Spatie's Laravel package skeleton conventions.

---

#Emissions Ecosystem

Modular emissions provider system for Laravel 10 applications.

This system is **deployment-based**, not runtime multi-tenant.

Each Laravel installation runs independently per customer server and selects its emissions provider via environment configuration.

---

# Architecture Overview

## High-Level Structure

```
Laravel App (Customer Deployment)
   ↓
config/emissions.php
   ↓
ceedbox/emissions-core
   ↓
Container resolves configured provider
   ↓
Concrete Provider (e.g. LuneClient)
```

Core does not reference any provider directly.
Providers are defined via configuration and resolved through Laravel's container.

---

# Configuration

## 1️. Publish Configuration

```bash
php artisan vendor:publish --tag=emissions-config
```

This generates a **single file**:

```
config/emissions.php
```

---

## 2️. config/emissions.php

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active Provider
    |--------------------------------------------------------------------------
    */

    'provider' => env('EMISSIONS_PROVIDER', 'lune'),

    /*
    |--------------------------------------------------------------------------
    | Provider Definitions
    |--------------------------------------------------------------------------
    */

    'providers' => [

        'lune' => [

            'class' => \Ceedbox\LuneModule\LuneClient::class,

            'config' => [
                'orgId'   => env('LUNE_ORG_ID'),
                'apiKey'  => env('LUNE_API_KEY'),
                'baseUrl' => env('LUNE_BASE_URL', 'https://sustainability.lune.co'),
                'ttl'     => env('LUNE_TOKEN_TTL', 3600),
            ],

        ],

    ],

];
```

All provider configuration lives in this single file.

---

## 3️. .env Per Customer Deployment

```env
EMISSIONS_PROVIDER=lune

LUNE_ORG_ID=...
LUNE_API_KEY=...
LUNE_BASE_URL=https://sustainability.lune.co
LUNE_TOKEN_TTL=3600
```

Each Laravel installation is isolated by deployment.

---

# Example Redirect Flow (SPA)

```
User clicks "View Emissions"
    ↓
Backend validates user ↔ client access
    ↓
EmissionsManager resolves active provider
    ↓
Provider generates URL with 1-hour JWT
    ↓
redirect()->away(external_url)
```

Example generated URL:

```
https://sustainability.lune.co/logistics/ORG/CLIENT?access_token=JWT&offset=true
```

---

# 📦 Package Breakdown

## 1️⃣ ceedbox/emissions-core

### Responsibilities

* Defines `EmissionsProviderInterface`
* Provides `EmissionsManager`
* Reads provider selection from config
* Resolves provider via Laravel container
* Remains provider-agnostic

### Does NOT

* Know about Lune
* Manage tenants
* Contain provider-specific logic
* Handle JWT generation

---

## 2️⃣ ceedbox/lune-module

### Responsibilities

* Implements `EmissionsProviderInterface`
* Generates JWT (HS256)
* Scopes token to client handle
* Appends `offset=true`
* Contains no Laravel-specific logic

---

# 🧪 Testing Without Laravel

Packages can be tested independently using the dev runner.

Run interactive URL generator:

```bash
make dev-run
```
Example:

```bash
make dev-run

    cd packages/dev-runner && composer install
    Installing dependencies from lock file (including require-dev)
    Verifying lock file contents can be installed on current platform.
    Nothing to install, update or remove
    Generating autoload files
    8 packages you are using are looking for funding.
    Use the `composer fund` command to find out more!
    cd packages/dev-runner && php bin/lune generate


    Org ID [ORG123]: 1234
    Client Handle [CLIENT1]: client-124
    API Secret [secret]:

    Generated URL:
    https://sustainability.lune.co/logistics/1234/client-124?access_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzE4NjE3MjEsImV4cCI6MTc3MTg2NTMyMSwic2NvcGUiOnsiaGFuZGxlcyI6WyJjbGllbnQtMTI0Il19fQ.E019HE9MfuiG0pZB30mfh6bzusM9eolgvzwLi1KxDk4
```


Example:

```bash
cd packages/dev-runner && php bin/lune generate
```

JWT debug:

```bash
php packages/dev-runner/bin/lune debug:jwt "JWT_OR_URL"
```

Signature verification:

```bash
php packages/dev-runner/bin/lune debug:jwt "JWT" --verify --secret="..."
```

Works without Laravel.

---

# 🚀 Installation (Laravel 10 App)

Install packages:

```bash
composer require ceedbox/emissions-core
composer require ceedbox/lune-module
```

Publish config:

```bash
php artisan vendor:publish --tag=emissions-config
```

Inject manager:

```php
use Ceedbox\EmissionsCore\EmissionsManager;

public function __construct(
    private EmissionsManager $emissions
) {}
```

## Example Usage
Generate URL:

```php
$url = $this->emissions
    ->provider()
    ->dashboardUrl($clientHandle);

return redirect()->away($url);
```

---

# 🔐 Security Notes

### 1. Always validate user-client relationship

Before generating emissions URLs, ensure:

* Authenticated user belongs to the requested client
* Or is authorized to access it

---

### 2. Never return JWT URLs in JSON

External URLs contain:

```
?access_token=JWT
```

Always redirect server-side.

---

### 3. Use HTTPS only

Access tokens are transmitted via query string.

---

### 4. Avoid logging full URLs

Do not log URLs containing `access_token`.

Log metadata only:

* user_id
* client
* provider
* emissions_id

---

### 5. Token lifetime

Lune JWT lifetime: 1 hour
Signed internal route lifetime (if used): ~120 seconds

These are independent.

---

# 🛠 Local Development (Mono-Repo)

Install dependencies:

```bash
make install
```

Run tests:

```bash
make test
```

Run dev CLI:

```bash
make dev-run
```

Debug JWT:

```bash
make dev-jwt TOKEN="..."
```

---

# 📈 Adding a New Provider

To support a new emissions provider:

1. Create new provider module
2. Implement `EmissionsProviderInterface`
3. Add provider definition to `config/emissions.php`
4. Set:

```env
EMISSIONS_PROVIDER=new-provider
```

No changes required in core.

---
# TODO
[ ] Improve config.php scaffholding

[ ] Write Unit Tests

[ ] Add more integrations

[ ] Add a Generator to scaffhold new integrations

