# Ceedbox Emissions Ecosystem (Spatie Skeleton Style)

This repository contains:

- packages/emissions-core
- packages/lune-module

Each package follows Spatie's Laravel package skeleton conventions.



# Ceedbox Emissions Ecosystem

Modular emissions provider system for Laravel 10 applications.

This repository contains two packages:

- `ceedbox/emissions-core`
- `ceedbox/lune-module`

The application never references a specific provider (e.g. Lune).  
Provider selection is resolved per tenant at runtime.

---

# 🧱 Architecture Overview

## High-Level Structure

```
App (Laravel 10)
   ↓
ceedbox/emissions-core
   ↓
Provider module (e.g. ceedbox/lune-module)
```

---

## Provider Resolution Flow

```
Tenant (Laravel instance)
  └── many Clients
        └── many Users

TenantResolverInterface (app)
  └── returns provider name (e.g. "lune")

EmissionsManager (core)
  └── resolves concrete provider

Provider module (e.g. LuneClient)
  └── builds final external URL
```

---

# 🔐 Secure Redirect Flow (Recommended)

This ecosystem is designed for SPA environments where users may stay logged in for hours.

### Flow Diagram

```
SPA loads
   ↓
User clicks "View Emissions"
   ↓
Backend generates temporary signed route (120s)
   ↓
User opens signed URL
   ↓
Core validates signature + auth
   ↓
Provider builds external URL with 1-hour JWT
   ↓
redirect()->away(external_url)
```

---

# 📦 Package Breakdown

---

## 1️⃣ ceedbox/emissions-core

### Responsibilities

- Defines provider contract
- Defines tenant resolver contract
- Resolves provider per tenant
- Exposes signed redirect endpoints
- Provider-agnostic

### Does NOT:

- Generate JWT
- Know about Lune
- Know external URL structure

---

## 2️⃣ ceedbox/lune-module

### Responsibilities

- Implements EmissionsProviderInterface
- Generates Lune JWT
- Scopes token to client handle
- Builds final dashboard URL

### Does NOT:

- Handle routing
- Handle auth
- Know about tenant resolution

---
# 🧪 Testing Without Laravel

Packages can be tested in isolation, without Laravel.

```bash
 make dev-quick-test
```

## Manual dry run example:

```bash
    emissions$ make dev-quick-test
    cd packages/dev-runner && composer install
    Installing dependencies from lock file (including require-dev)
    Verifying lock file contents can be installed on current platform.
    Nothing to install, update or remove
    Generating autoload files
    8 packages you are using are looking for funding.
    Use the `composer fund` command to find out more!
    cd packages/dev-runner && php bin/lune generate
    Org ID [ORG123]: 1245
    Client Handle [CLIENT1]: 07856
    API Secret [secret]:

    Generated URL:
    https://sustainability.lune.co/logistics/1245/07856?access_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzE4NTYzMjcsImV4cCI6MTc3MTg1OTkyNywic2NvcGUiOnsiaGFuZGxlcyI6WyIwNzg1NiJdfX0.WYInofIqhC7eFOV54gGw7SNMY7xQzyOn7_QdRiNhduM
```

This works without Laravel.


---

# 🚀 Installation (Laravel 10 App)

Install both packages:

```bash
composer require ceedbox/emissions-core
composer require ceedbox/lune-module
```

---

v
# 🔗 Generating Signed Redirect URLs (SPA)

Dashboard:

```php
URL::temporarySignedRoute(
    'emissions.redirect.dashboard',
    now()->addSeconds(120),
    ['client' => $clientHandle]
);
```

## NOT ENABLED FOR NOW!
Per emissions:

```php
URL::temporarySignedRoute(
    'emissions.redirect.emissions',
    now()->addSeconds(120),
    [
        'client' => $clientHandle,
        'emissionsId' => $emissionsId
    ]
);
```

Frontend:

```html
<a href="SIGNED_URL" target="_blank" rel="noreferrer">
    View Emissions
</a>
```

---

# 🔐 Security Notes

## 1. Always validate user-client relationship

The core package does not know your domain rules.

You must ensure:

- Authenticated user belongs to the requested client
- Or is authorized to access it

Add middleware or policy checks.

---

## 2. Tokens must never be returned via JSON

The external URL contains:

```
?access_token=JWT
```

Never send this URL in API responses. Always redirect.

---

## 3. Use HTTPS only

Access tokens are in query string. HTTPS is mandatory.

---

## 4. Avoid logging full URLs

Do not log URLs containing `access_token`.
Log metadata only:

- user_id
- client
- provider
- emissions_id

---

## 5. Token lifetime

Lune JWT lifetime: 1 hour  
Internal signed redirect lifetime: ~120 seconds  

These are independent.

---

# 🛠 Local Development (Mono-Repo Style)

Install dependencies:

```bash
make install
```

Run tests:

```bash
make test
```

Run per package:

```bash
make test-core
make test-lune
```

---

# 📈 Future Expansion

Add new provider:

```
ceedbox/another-provider-module
```

Steps:

1. Implement `EmissionsProviderInterface`
2. Register provider in config
3. Set tenant `emissions_provider` to new value

No app changes required.

---

# 🧩 Design Principles

- Provider-agnostic core
- Signed redirect security pattern
- No frontend token storage
- Stateless JWT generation
- Laravel 10 compatible
- Spatie-style package structure

---

