# PHPinnacle Ensign

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

PHPinnacle Ensign provides tools that allow your application components to communicate with each other by dispatching signals and listening to them.

Thanks to [amphp](https://amphp.org) backend those communication is fully asynchronous.

## Install

Via Composer

```bash
$ composer require phpinnacle/ensign
```

## Basic Usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use PHPinnacle\Ensign\DispatcherBuilder;

Amp\Loop::run(function () {
    $builder = new DispatcherBuilder;
    $builder
        ->register('upper', function (string $text) {
            return \strtoupper($text);
        })
        ->register('lower', function (string $text) {
            return \strtolower($text);
        })
    ;

    $dispatcher = $builder->build();

    $hello = yield $dispatcher->dispatch('upper', 'hello');
    $world = yield $dispatcher->dispatch('lower', 'WORLD');

    echo sprintf('%s %s!', $hello, $world);
});
```

More examples can be found in [`examples`](examples) directory.

## Testing

```bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email dev@phpinnacle.com instead of using the issue tracker.

## Credits

- [PHPinnacle][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/phpinnacle/ensign.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/phpinnacle/ensign.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/phpinnacle/ensign.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/phpinnacle/ensign.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/phpinnacle/ensign
[link-scrutinizer]: https://scrutinizer-ci.com/g/phpinnacle/ensign/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/phpinnacle/ensign
[link-downloads]: https://packagist.org/packages/phpinnacle/ensign
[link-author]: https://github.com/phpinnacle
[link-contributors]: https://github.com/phpinnacle/ensign/graphs/contributors
