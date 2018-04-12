
# Chebur Sphinx Bundle

[![PHP requirements](https://img.shields.io/packagist/php-v/chebur/sphinx-bundle.svg)](https://packagist.org/packages/chebur/sphinx-bundle "PHP requirements")
[![Latest version](https://img.shields.io/packagist/v/chebur/sphinx-bundle.svg)](https://packagist.org/packages/chebur/sphinx-bundle "Last version")
[![Total downloads](https://img.shields.io/packagist/dt/chebur/sphinx-bundle.svg)](https://packagist.org/packages/chebur/sphinx-bundle "Total downloads")
[![License](https://img.shields.io/packagist/l/chebur/sphinx-bundle.svg)](https://packagist.org/packages/chebur/sphinx-bundle "License")

## Installation

Require the bundle and its dependencies with composer:
```bash
composer require chebur/sphinx-bundle
```
Enable bundle in your application's kernel:
```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new \Chebur\SphinxBundle\CheburSphinxBundle(),
        // ...
    );
}
```

## License

See [LICENSE](LICENSE).
