# Laravel API

A powerful package designed to make developing APIs very easy. This package takes care of all the boiler-plate code to provide support for routes, controller methods and associated logic out of the box.

Please note, this package is still in development and the documentation below is subject to change in ways which can make it incompatible with previous versions. Please DO NOT use in production until 1.0 release.

## Installation

The package is currently under development and hence is not available on composer. Please use the following method to install and help build this package.

Clone the repository inside your Laravel installation as follows:
```
git clone https://github.com/asahasrabuddhe/laravel-api packages/asahasrabuddhe/laravelapi
```

Add the following lines in your composer.json file to help install the package

```
"repositories": [
        {
            "type": "path",
            "url": "packages/asahasrabuddhe/laravel-api",
            "options": {
                "symlink": true
            }
        }
    ],
```

Then, add the following lines in the require section of your composer file and run composer update.

```
"asahasrabuddhe/laravel-api": "dev-master"
```

You are now ready to use the package.

## Basic Usage

1. To enable using this package, replace the following:
>>>a. **Models**: Replace all instances of `use Illuminate\Database\Eloquent\Model;` with `use Asahasrabuddhe\LaravelAPI\BaseModel;`. For the Laravel default user model, replace `use Illuminate\Foundation\Auth\User as Authenticatable;` with `use Asahasrabuddhe\LaravelAPI\BaseUser as Authenticatable;`

>>>b. **Controllers**: Add the following line to the top of your controller: `use Asahasrabuddhe\LaravelAPI\BaseController as Controller`

2.**Routing**: Please define the routes as following in routes/api.php file:

```
ApiRoute::group([
    'middleware' => ['api'],
    'namespace' => 'App\Http\Controllers'], function () {
        ApiRoute::resource('user', 'UserController');
        ApiRoute::resource(<route>, <controller>);
    }
);
```