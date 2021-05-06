# Coming Soon - Flysystem adapter for Meema.io

This package contains a [Flysystem](https://flysystem.thephpleague.com/) adapter for Meema. Under the hood, the Meema API is used.

## Installation

You can install the package via composer:

``` bash
composer require meemaio/flysystem-meema
```

## Usage

The first thing you need to do is get an authorization token at Meema.io. A token can be generated in the [App Console](https://meema.io/) for any Meema API app.

``` php
use League\Flysystem\Filesystem;
use Meema\Client;
use Meema\FlysystemMeema\MeemaAdapter;

$client = new Client($authorizationToken);

$adapter = new MeemaAdapter($client);

$filesystem = new Filesystem($adapter);
```
For extending the storage, you have to put this in your service provider.

```php
use League\Flysystem\Filesystem;
use Meema\Client as MeemaClient;
use Meema\FlysystemMeema\MeemaAdapter;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    Storage::extend('meema', function ($app, $config) {
        $client = new MeemaClient(
            $config['api_secret']
        );

        return new Filesystem(new MeemaAdapter($client));
    });
}
```
After extending the storage you will have to put the `meema` as you filesystem driver in your `.env`

```
FILESYSTEM_DRIVER=meema
```

After extending the storage and defining `meema` as your drive in `.env` you will have to put the `meema` driver in your `config/filesystems.php`

Read more about custom filesystems [here](https://laravel.com/docs/8.x/filesystem#custom-filesystems)

```php
'disks' => [
    ...
    'meema' => [
        'driver' => 'meema',
        'api_secret' => env('MEEMA_API_SECRET'),
    ],
]
```
After extending and defining the filesystem driver, you can then use the Laravel Storage facade as such:

```php
use Illuminate\Support\Facades\Storage;

Storage::disk('meema')->put('photos/image.jpg', $file);
Storage::disk('meema')->getMetadata('photos/image.jpg');
Storage::disk('meema')->getVisibility('photos/image.jpg');
Storage::disk('meema')->setVisibility('photos/image.jpg', 'private');
Storage::disk('meema')->path('photos/image.jpg');
Storage::disk('meema')->copy('photos/image.jpg', 'photos/copied-image.jpg');
Storage::disk('meema')->rename('photos/image.jpg', 'photos/renamed-image.jpg');
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
./vendor/bin/pest
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email chris@cion.agency instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
