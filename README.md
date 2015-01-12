Caffeinated Modules
===================
[![Build Status](https://travis-ci.org/caffeinated/modules.svg?branch=master)](https://travis-ci.org/caffeinated/modules)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/caffeinated/modules/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/caffeinated/modules/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/caffeinated/modules/v/stable.svg)](https://packagist.org/packages/caffeinated/modules)
[![Total Downloads](https://poser.pugx.org/caffeinated/modules/downloads.svg)](https://packagist.org/packages/caffeinated/modules)
[![Latest Unstable Version](https://poser.pugx.org/caffeinated/modules/v/unstable.svg)](https://packagist.org/packages/caffeinated/modules)
[![License](https://poser.pugx.org/caffeinated/modules/license.svg)](https://packagist.org/packages/caffeinated/modules)

All features for the initial release as outlined within the [roadmap](http://codex.caffeinated.ninja/modules/master/roadmap) have been completed. I'm simply waiting until Laravel 5.0 is officially released to tag this as stable v1.0. In the meantime I will continue to clean up the code that currently stands.

You can read more about the development and updates behind the package on my blog [here](http://caffeinated.ninja).

**Update:** The Caffeinated Modules package now supports the *latest* version of the Laravel development build! The way the packages config files are registered have been re-worked to make up for the removal of the `$this->package('vendor/package')` function.

---

To learn more about the usage of this package, please refer to the full set of [documentation](http://codex.caffeinated.ninja/modules/master). You will find quick installation instructions below.

---

Installation
------------
Begin by installing the package through Composer. The best way to do this is through your terminal via Composer itself:

```
composer require caffeinated/modules
```

Once this operation is complete, simply add both the service provider and facade classes to your project's `config/app.php` file:

#### Service Provider
```
'Caffeinated\Modules\ModulesServiceProvider'
```

#### Facade
```
'Module' => 'Caffeinated\Modules\Facades\Module'
```

And that's it! With your coffee in reach, start building out some awesome modules!
