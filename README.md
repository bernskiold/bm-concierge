# BM Concierge

Through BM Concierge we provide helpful services right from the WordPress dashboard and editor. Concierge is available for all Company Cloud websites by default, and can be added
to any of our WordPress customers' websites.

## Supported Services

- Proofreading
- On-Page SEO Optimization
- Accessibility Audit
- Page Layout
- Translation (Machine-Learning)

Coming soon:

- Translation (Agency)

## Hooks

The behavior of Concierge can be customized via hooks. Common usecases would be customer specific pricing, disabling concierge for some sites in a multisite, or selecting which
services are available.

### Disabling Concierge

If the filter `bm/concierge/enabled` returns `false`, Concierge will not be loaded for the website.

```
apply_filters('bm/concierge/enabled', function() {

	// Enable concierge on the sites in this list.
	$enabled_on_sites = [ 1,2,3 ];

	if ( in_array( get_current_blog_id(), $enabled_on_sites, true ) ) {
		return true;
	}

	return false;
});
```

### Disabling Specific Concierge Services

All services are enabled by default. To disable a service, return its filter as `false`.

- Proofreading: `bm/concierge/services/proofreading`
- Layout: `bm/concierge/services/layout`
- Accessibility: `bm/concierge/services/accessibility`
- On-Page SEO: `bm/concierge/services/onpage`
- Machine Translation: `bm/concierge/services/machine_translation`

This could be used to either disable a service completely, or selectively based on rules:

```
// Only allow admins to request layout services.
apply_filters('bm/concierge/services/layout', function() {
	return current_user_can( 'manage_options' );
});
```

### Customer-Specific Pricing

By default, Concierge loads a specific API pricing file from our servers for all services.

The `bm/concierge/pricing_api_url` filter can be used to return a different JSON file URL, matching `https://static.bernskioldmedia.com/concierge/api.json` with different pricing.

It takes one argument, apart from the URL, which is the service key. This could be used to load a specific file only for a specific service.

```
// Load customer-specific pricing.
apply_filters('bm/concierge/pricing_api_url', function( string $url, string $service_key ) {
	return 'https://static.bernskioldmedia.com/concierge/clients/client-name.json';
});
```

### Specifying the currency.

By default, the currency used to present prices is based on the locale. We support SEK, GBP and USD.

To override the currency and keep it the same regardless of locale, use the `` filter and return the three-letter ISO currency code.

```
// Force currency.
apply_filters('bm/concierge/currency', function( string $currency, string $service_key ) {
	return 'SEK';
});
```

### Adding the machine learning API key

In order for the machine learning service to work an API key needs to be set in the environment configuration.

```php
define( 'BM_CONCIERGE_ML_TRANSLATION_API_KEY', 'XXXXXX' );
```

For Bernskiold Media projects we store this in our central management system which we generate for all clients. Alternatively you can sign up to [get your own API key with DeepL](https://www.deepl.com/pro-api).

## For Developers

### Quick Start

1. Clone this repository to your plugin folder as `bm-concierge`.
2. Install everything and build by running `npm run setup`. We expect npm and Composer to be installed globally.
3. During development, run `npm start` (or `npm run dev`) to watch for changes and rebuild.

You need Node.js 22.22.2+ (see `.nvmrc`). The editor script is built with the standard `@wordpress/scripts` pipeline from `assets/src/block-editor.js` into `dist/`.

### Linting & Formatting

```bash
npm run lint     # JS (ESLint flat config) + CSS (Stylelint)
npm run format   # Prettier via wp-scripts
```

### Tests

```bash
npm run test:unit   # JS unit tests (Vitest)
composer test       # PHP integration tests, see TESTING.md for setup
```

See [TESTING.md](TESTING.md) for details.

### Preparing for Production

Run `npm run build` to produce a minified production build. The `dist/` folder is committed, so commit the rebuilt files.

### Translations

Run `npm run i18n:pot` to rebuild and regenerate the POT file, and `npm run i18n:json` to generate the JSON files used by the editor script after updating the PO files.
