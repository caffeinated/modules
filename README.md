Caffeinated Modules
===================
[![Build Status](https://travis-ci.org/caffeinated/modules.svg?branch=master)](https://travis-ci.org/caffeinated/modules)
[![Latest Stable Version](https://poser.pugx.org/caffeinated/modules/v/stable.svg)](https://packagist.org/packages/caffeinated/modules)
[![Total Downloads](https://poser.pugx.org/caffeinated/modules/downloads.svg)](https://packagist.org/packages/caffeinated/modules)
[![Latest Unstable Version](https://poser.pugx.org/caffeinated/modules/v/unstable.svg)](https://packagist.org/packages/caffeinated/modules)
[![License](https://poser.pugx.org/caffeinated/modules/license.svg)](https://packagist.org/packages/caffeinated/modules)

Before you delve into this, I want to start out by saying that this package is under **heavy development** and is not ready for production use.

Table of Contents
-----------------

- [Introduction](#introduction)
- [Conventions](#conventions)
- [Installation](#installation)
- [Artisan Commands](#artisan-commands)
- [Roadmap](#roadmap)
- [Changelog](#changelog)

---

Introduction
------------

Caffeinated Modules is a simple package to allow the means to seperate your Laravel application out into modules. Each module is completely self-contained allowing the ability to simply drop a module in for use. Every module has a `module.json` detail file to outline information such as the description, version, author(s), and anything else you'd like to store pertaining to the module at hand.

### Example Folder Structure
```
laravel-project/
	app/
	|--	Modules/
		|--	Blog/
			|-- Config/
			|--	Console/
			|-- Database/
				|-- Migrations/
				|-- Seeds/
			|--	Http/
				|--	Controllers/
				|--	Filters/
				|--	Requests/
				|--	routes.php
			|--	Providers/
				|-- BlogServiceProvider.php
				|-- RouteServiceProvider.php
			|--	Resources/
				|--	Lang/
				|--	Views/
		|--	module.json
```

---

Conventions
-----------
* PSR-1
* PSR-2
* PSR-4
* PHP 5.4+
* Laravel 5.0

---

Installation
------------
Begin by installing the package through Composer. Edit your project's `composer.json` file to require `caffeinated/modules`:

```
"require": {
	"caffeinated/modules": "dev-master"
}
```

Next, update Composer from your Terminal:

```
composer update
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

---

Artisan Commands
----------------
The Caffeinated Modules package comes with a handful of Artisan commands to make creating and managing modules easy.

- [`module:disable`](#moduledisable-module)
- [`module:enable`](#moduleenable-module)
- [`module:make`](#modulemake-module)
- [`module:make-migration`](#modulemake-migration-module-table)

---

#### module:disable [MODULE]
Disable a module. Disabling a module ensures it is not loaded during the boot process of your application.

##### Parameters
- [MODULE] - Module slug

##### Example
```
php artisan module:disable blog
```

---

#### module:enable [MODULE]
Enable a module.

##### Parameters
- [MODULE] - Module slug

##### Example
```
php artisan module:enable blog
```

---

#### module:make [MODULE]
Generate a new module. This will generate all the necessary folders and files needed to bootstrap your new module. The new module will be automatically enabled and work out of the box.

##### Parameters
- [MODULE] - Module slug

##### Example
```
php artisan module:make blog
```

---

#### module:make-migration [MODULE] [TABLE]
Create a new module migration file.

##### Parameters
- [MODULE] - Module slug
- [TABLE] - Table to be created by migration file

##### Example
```
php artisan module:make-migration blog posts
```

---

Roadmap
-------
The following are the planned features to be developed for each version.

### 1.0

- [x] Proper use of Service Providers to autoload modules
- [x] `module:disable` Artisan command
- [x] `module:enable` Artisan command
- [x] `module:make` Artisan command
- [ ] `module:make-controller` Artisan command
- [x] `module:make-migration` Artisan command
- [ ] `module:make-seed` Artisan command
- [ ] `module:migrate` Artisan command
- [ ] `module:migrate-refresh` Artisan command
- [ ] `module:migrate-reset` Artisan command
- [ ] `module:migrate-rollback` Artisan command
- [ ] `module:seed` Artisan command

### 1.1

- [ ] Maintain modules either through the `module.json` files (flat) or via a database (database).
- [ ] Add the ability to see multiple module locations. Great for when you want to maintain a "core" set of modules vs. add-on modules seperate from each other.

---

Changelog
---------

Nothing at the moment.
