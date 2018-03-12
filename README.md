# PHPinnacle Ensign

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what
PSRs you support to avoid any confusion with users and contributors.

## Install

Via Composer

``` bash
$ composer require phpinnacle/ensign
```

## Basic Usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

Amp\Loop::run(function () {
    ensign_signal('upper', function ($text) {
        return strtoupper($text);
    });

    ensign_signal('lower', function ($text) {
        return strtolower($text);
    });

    $hello = yield ensign_dispatch('upper', 'hello');
    $world = yield ensign_dispatch('lower', 'WORLD');

    echo sprintf('%s %s!', $hello, $world);
});
```

More examples can be found in `examples` directory.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
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
[link-contributors]: ../../contributors
