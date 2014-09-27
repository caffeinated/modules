Caffeinated Modules
===================
Before I begin, I want to start out by saying that this package is under **heavy development** and is not ready for production.

Caffeinated Modules is a simple package to allow the means to seperate your Laravel application out into modules.

Example Folder Structure
------------------------
```
laravel-project/
	app/
		Modules/
			Blog/
			Navigation/
			Pages/
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

And that's it! Take a sip of your coffee and start coding.