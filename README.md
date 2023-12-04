![Build Status](https://github.com/thecodezone/dt-plugin/actions/workflows/ci.yml/badge.svg?branch=master)

# Disciple.Tools - Plugin

__Kick start your Disciple.Tools plugin project with this template!__

__This plugin is a modern opinionated extension starter template inspired by Laravel.__

> **Tip:** You can safely delete this README.md file and replace it with your own. You can always view this readme
> at [github.com/thecodezone/dt-plugin](https://github.com/thecodezone/dt-plugin).

## Purpose

At CodeZone, we recognize a developer or team might love Disciple.Tools, but __miss the modern tooling found in PHP
Application frameworks.__

There growing community of Laravel and Symfony developers who bring exceptional expertise to the table.

Our purpose is to bridge the gap between these seasoned developers and the dynamic environment of Disciple.Tools.
We strive to make Disciple.Tools plugin development not only accessible but also a delightful experience for those
already well-versed in Laravel and Symfony frameworks.

> **Are you a WordPress developer?** You may feel more at home using
> the [Disciple Tools Starter Template](https://github.com/thecodezone/dt-plugin/).

## Included

### Framework

1. WordPress code style requirements. ```phpcs.xml```
1. PHP Code Sniffer support (composer) @use ```/vendor/bin/phpcs``` and ```/vendor/bin/phpcbf```
1. GitHub Actions Continuous Integration ```.githbub/workflows/ci.yml```
1. Disciple.Tools Theme presence check. ```\DT\Plugin\plugin()->is_dt_theme(); ```
1. Remote upgrade system for ongoing updates outside the Wordpress Directory.
1. Multilingual support. ```/languages``` & ```default.pot```
1. [Composer](https://getcomposer.org/) support. ```composer.json```
1. Scoped dependency autoloading using [PHPScoper](https://github.com/humbug/php-scoper). ```/composer.scoped.json```
1. Laravel-style service providers. ```/src/Providers```
1. Laravel-style controllers. ```/src/Controllers```
1. Vite build system. ```/vite.config.js``` & ```/resources/js```
1. Inversion of control container
   using [Laravel's Service Container](https://laravel.com/docs/master/container#main-content). ```/src/Container.php```
1. Routing system using [FastRoute](https://github.com/nikic/FastRoute). ```/routes/web/routes.php```

### Components

1. Sample admin menu and admin page with starter tabs component.
1. Sample post-type class and hooks.
1. Sample REST api.
1. Sample Magic Link.

> **Tip:** This starter plugin does not attempt to provide every component provided by
> the [Disciple Tools Starter Template](https://github.com/thecodezone/dt-plugin/). See
> the [Disciple Tools Starter Template](https://github.com/thecodezone/dt-plugin/) for
> implementation examples of charts, tiles and other components.

#### Scoped Dependency Autoloading

This plugin uses [Composer](https://getcomposer.org/) to manage dependencies. The plugin's dependencies are scoped to
your plugin's namespace. This means you can use any package you want without worrying about conflicts with other
plugins. For example, if you want to use the [Guzzle](http://docs.guzzlephp.org/en/stable/) HTTP client, you can simply
add it to your `composer.scoped.json` file, instead of the `composer.json` file.
Guzzle would then be installed in the `vendor-scoped` directory, instead of the `vendor` directory. This allows you to
use Guzzle without worrying about conflicts with other plugins that may also use Guzzle.
See [PHPScoper](https://github.com/humbug/php-scoper) for more information.

#### Multilingual Support

WordPress's [Internationalization](https://developer.wordpress.org/themes/functionality/internationalization/)
functionality and [Weblate](https://weblate.org/en/) via [translate.disciple.tools](https://translate.disciple.tools/)
are used to provide multilingual support.

Hard-coded strings should be wrapped in the `__()` function. For example:

```php
__( 'Hello World!', 'dt-plugin' );
```

#### Service Providers

Service providers are used to register services into the
plugin's [inversion of control container](https://laravel.com/docs/master/container#main-content) or with
Disciple.Tools. Service providers are located in the `src/Providers` directory. The `register()` method is called when
the plugin is first loaded. The `boot()` method is called after the theme have been loaded.
See [Laravel's Service Providers](https://laravel.com/docs/master/providers) for more information.

See [Laravel's Service Container](https://laravel.com/docs/master/container#main-content) for more information on
registering and resolving services.

```php
namespace DT\Plugin\Providers;

use DT\Plugin\Plugin;

class ExampleServiceProvider extends ServiceProvider
{
    /**
     * Called when the plugin is first loaded.
     *
     * @return void
     */
    public function register()
    {
        // Register a service into the plugin's container.
        $this->container->bind( 'example', function () {
            return new Example();
        } );
        
        add_filter( 'some/filter', function () {
           //some filter
        });
    }

    /**
     * Called when the theme is loaded.
     *
     * @return void
     */
    public function boot()
    {
        add_action( 'some/action', function () {
            //Some action
        });
    }
}

```

#### Routing

Routing is handled by [FastRoute](https://github.com/nikic/FastRoute). Routes are located in the `routes/web` directory.
The `routes.php` file is loaded when the plugin is first loaded. Separate route files can be loaded using the Router.php
service.

##### Loading a route file

Custom route files can be registered when loading routes in specific hook or in magic links. For example:

```php
$router = app()->container->make( /DT/Plugin/Services/Router::class );
$router->from_file( 'web/custom-routes-file.php' );
```

##### Routing using a query parameter

The query string is not used when matching routes. Do allow &action=some-page or &page=some-page to be used as a route,
you can specify a query parameter to use when matching routes.

```php
$router = app()->container->make( /DT/Plugin/Services/Router::class );
$router->from_file( 'web/custom-routes-file.php',[
    'param' => 'page',
] );
```

##### Web Routes

See fast route documentation for more information
on [defining routes](https://github.com/nikic/FastRoute#defining-routes). To map a route to a controller, use the `@`
symbol after the controller class name followed by the method name.

```php
    use DT\Plugin\Controllers\HelloController;

    $r->get( 'dt/plugin/hello', HelloController::class . '@show' );
```

##### Rest Routes

The plugin uses [WP REST API](https://developer.wordpress.org/rest-api/) to provide REST routes. Routes are located in
the routes `routes/admin` directory.

```json
{
  "require": {
    "guzzlehttp/guzzle": "^7.0"
  }
}
```

#### Controllers

Controllers are located in the `src/Controllers` directory. Controllers are responsible for handling requests and
returning responses. Controllers are basic PHP classes with no parent class or base controller. Controllers are resolved
from the container using the controller's fully qualified class name. Controllers can be resolved from the container to
make use of automatic dependency injection.

> **Tip:** Keep your controllers thin. Business logic should be moved to services. Controllers should only be
> responsible for handling requests and returning responses. Anything more than basic logic should be moved to a
> service.

#### Templating

The template service located at `src/Services/Template.php` is used to bootstrap a blank template for your plugin.
Routes are mapped to controllers which load basic PHP templates from the `resources/templates` directory.

> **Tip:** Be sure to use WordPress escaping functions when outputting data in your templates.
> See [Data Validation](https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/) for more
> information.

#### Magic Links

A basic user-based magic link is provided. See the [DT starter plugin template](
Then, run `composer update` to install the new dependency. You can now use Guzzle in your plugin:) for more examples of
magic links.

#### Code Style

[PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) (`phpcs`) is used for static code analysis
and [PHP_CodeSniffer Beautifier](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Usage-Advanced#fixing-errors-automatically) (`phpcbf`)
for automatic code formatting. Before committing your code, run the following commands to check and fix coding
standards:

```bash
/vendor/bin/phpcs
/vendor/bin/phpcbf
```

> PHP_CodeSniffer and Beautifier work best when integrated with your IDE.
> See [PHPSTORM](https://www.jetbrains.com/help/phpstorm/using-php-code-sniffer.html)
> and [VSCode](https://marketplace.visualstudio.com/items?itemName=ValeryanM.vscode-phpsab) for more information.

##### Integration with your IDE

#### Testing

This plugin uses [PHPUnit](https://phpunit.de/) for testing. Tests are located in the `test` directory.

Before running tests you must install a local version of WordPress to test against using the following script:

```php
./tests/install-wp-tests.sh
```

After running the install script, You can run tests using the following command:

```bash
phpunit
```

## Recommended

- Disciple.Tools Theme installed on a local Wordpress
  Server [DDEV](https://ddev.readthedocs.io/en/latest/users/quickstart/#wordpress)
  or [localwp.com](https://localwp.com).

## Contribution

Contributions welcome. You can report issues and bugs in the
[Issues](https://github.com/thecodezone/dt-plugin/issues) section of the repo. You can
present ideas
in the [Discussions](https://github.com/thecodezone/dt-plugin/discussions) section of the
repo. And
code contributions are welcome using
the [Pull Request](https://github.com/thecodezone/dt-plugin/pulls)
system for git. For a more details on contribution see the
[contribution guidelines](https://github.com/thecodezone/dt-plugin/blob/master/CONTRIBUTING.md).

## Screenshots

![screenshot](https://via.placeholder.com/600x150)