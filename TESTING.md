# Testing

The plugin has two layers of automated tests. Both run in CI
(`.github/workflows/tests.yml`) on every pull request.

| Layer | Tool | What it covers |
| ----- | ---- | -------------- |
| Unit | Vitest (via `wp-scripts test-unit-js`) | Pure JS logic: currency formatting, the Markdown helper, the `bm/concierge` data store |
| Integration | PHPUnit + the WordPress test library | Server-side PHP: plugin boot, editor asset loading, pricing from the pricing API, the REST endpoints and order e-mails |

The PHP tests run against a matrix of PHP 8.2–8.5 and WordPress 7.0 (the
minimum supported version) and the latest release. The pricing API is served
from `tests/php/Fixtures/pricing.json`, so no test reaches the real API.

## Prerequisites

```bash
npm install
composer install
```

## Unit tests

```bash
npm run test:unit          # run once
npm run test:unit:watch    # watch mode
```

Tests live in `tests/unit/`.

## Integration tests

Tests live in `tests/php/` and run against a real WordPress install. There are
two ways to run them.

### With wp-env (Docker)

```bash
npm run env:start   # boot the wp-env Docker environment
npm run test:php    # run PHPUnit inside the tests-cli container
npm run env:stop
```

### Without Docker

You need a MySQL/MariaDB server with an empty database. The install script
downloads WordPress and its test library and prints the environment variables
PHPUnit needs:

```bash
export $(bin/install-wp-tests.sh latest wordpress_tests root password 127.0.0.1)
composer test
```

Pass a version such as `7.0` instead of `latest` to test another release.
