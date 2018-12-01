# Caffeinated Modules
[![Source](https://img.shields.io/badge/source-caffeinated/modules-blue.svg?style=flat-square)](https://github.com/caffeinated/modules)
[![Latest Stable Version](https://poser.pugx.org/caffeinated/modules/v/stable?format=flat-square)](https://packagist.org/packages/caffeinated/modules)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)
[![Total Downloads](https://img.shields.io/packagist/dt/caffeinated/modules.svg?style=flat-square)](https://packagist.org/packages/caffeinated/modules)
[![Travis (.org)](https://img.shields.io/travis/caffeinated/modules.svg?style=flat-square)](https://travis-ci.org/caffeinated/modules)

Extract and modularize your code for maintainability. Essentially creates "mini-laravel" structures to organize your application.

## Documentation
You will find user friendly and updated documentation on the [Caffeinated website](https://caffeinatedpackages.com/guide/packages/modules.html).

## Installation
Simply install the package through Composer. From here the package will automatically register its service provider and `Module` facade.

```
composer require caffeinated/modules
```

### Config
To publish the config file, run the following:

```
php artisan vendor:publish --provider="Caffeinated\Modules\ModulesServiceProvider" --tag="config"
```

## Changelog
You will find a complete changelog history within the [CHANGELOG](CHANGELOG.md) file.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Testing
Run tests with PHPUnit:

```bash
vendor/bin/phpunit
```

## Security
If you discover any security related issues, please email shea.lewis89@gmail.com directly instead of using the issue tracker.

## Credits
- [Shea Lewis](https://github.com/kaidesu)
- [All Contributors](../../contributors)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
