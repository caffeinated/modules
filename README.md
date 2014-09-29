Caffeinated Modules
===================
Before I begin, I want to start out by saying that this package is under **heavy development** and is not ready for production.

---

Caffeinated Modules is a simple package to allow the means to seperate your Laravel application out into modules.

Example Folder Structure
------------------------
```
laravel-project/
	app/
	|--	Modules/
		|--	Blog/
			|-- Config/
			|--	Console/
			|-- Database/
				|-- Migrations/
				|-- Seeders/
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

Conventions
-----------
* PSR-1
* PSR-2
* PSR-4
* PHP 5.4.0+
* Laravel 5.0

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

Console Commands
----------------
The Caffeinated Modules package comes with a handful of commands to make managing and creating modules with ease.

- [`module:make`](#module:make)
- [`module:enable`](#module:enable)
- [`module:disable`](#module:disable)

---

#### module:make [MODULE]
Generate a new module. This will generate all the necessary folders and files needed to bootstrap your new module. The new module will be automatically enabled and work out of the box. Feel free to add or remove any un-needed folders and files per your needs.

##### Parameters
- [MODULE] - Module slug

```
php artisan module:make blog
```

#### module:enable [MODULE]
Enable a module.

##### Parameters
- [MODULE] - Module slug

```
php artisan module:enable blog
```

#### module:disable [MODULE]
Disable a module. Disabling a module ensures it is not loaded during the boot process of your application.

##### Parameters
- [MODULE] - Module slug

```
php artisan module:disable blog
```

#### module:migration [MODULE] [TABLE]
Create a new module migration file.

##### Parameters
- [MODULE] - Module slug
- [TABLE] - Table to be created by migration file

```
php artisan module:migration blog posts
```