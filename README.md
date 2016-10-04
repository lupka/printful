# Printful API for Laravel

## Installation

Install this package via composer using this command:

```bash
composer require lupka/printful
```

Install the service provider in config/app.php:

```php
'providers' => [
    ...
    Lupka\Printful\PrintfulServiceProvider::class,
];
```

Publish the config file:
```bash
php artisan vendor:publish --provider="Lupka\Printful\PrintfulServiceProvider" --tag="config"
```
