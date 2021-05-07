<p align="center">
  <a href="https://meema.io">
    <img alt="Meema for Laravel" src="https://raw.githubusercontent.com/meema/meemasearch-client-common/master/banners/php.png" >
  </a>

<h4 align="center">The most simple way to integrate <a href="https://meema.io" target="_blank">Meema</a> and your Laravel project</h4>

<p align="center">
    <a href="https://scrutinizer-ci.com/g/meemalabs/flysystem-meema/badges/quality-score.png?b=main"><img src="https://scrutinizer-ci.com/g/meemalabs/flysystem-meema/badges/quality-score.png?b=main" alt="Scrutinizer" /></a>
    <a href="https://packagist.org/packages/meema/flysystem-meema"><img src="https://poser.pugx.org/meema/flysystem-meema/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/meema/flysystem-meema"><img src="https://poser.pugx.org/meema/flysystem-meema/v/stable.svg" alt="Latest Version"></a>
    <a href="https://packagist.org/packages/meema/flysystem-meema"><img src="https://poser.pugx.org/meema/flysystem-meema/license.svg" alt="License"></a>
</p>

<p align="center">
    <a href="https://docs.meema.io" target="_blank">Documentation</a>  •
    <a href="https://github.com/meemalabs/meema-client-php" target="_blank">PHP Client</a>  •
    <a href="http://stackoverflow.com/questions/tagged/meema" target="_blank">Stack Overflow</a>  •
    <a href="https://github.com/meemalabs/laravel-meema/issues" target="_blank">Report a bug</a>  •
    <a href="https://docs.meema.io" target="_blank">FAQ</a>  •
    <a href="https://discord.meema.io" target="_blank">Discord</a>
</p>

## Usage

This package contains a [Flysystem](https://flysystem.thephpleague.com/) adapter for Meema. Under the hood, the Meema API is utilized.

You can install the package via composer:

``` bash
composer require meema/flysystem-meema
```

The first thing you need to do is get an API Key at Meema.io. A token can easily be generated in Meema's [Dashboard](https://meema.io/) once you are logged in.

``` php
use League\Flysystem\Filesystem;
use Meema\Client;
use Meema\FlysystemMeema\MeemaAdapter;

$client = new Client($authorizationToken);

$adapter = new MeemaAdapter($client);

$filesystem = new Filesystem($adapter);
```

For extending the storage, you have to simply put following into the boot-method of your service provider:

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

After extending the storage, you can set `meema` as the filesystem driver in your `.env`-file or in your filesystem's config file.

```
FILESYSTEM_DRIVER=meema
```

```php
'disks' => [
    ...
    'meema' => [
        'driver' => 'meema',
        'api_secret' => env('MEEMA_API_SECRET'),
    ],
]
```

Read more about custom filesystems [here](https://laravel.com/docs/8.x/filesystem#custom-filesystems).

After extending and defining the filesystem driver, you can then use Laravel's "Storage"-facade as follows:

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

## 📈 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 💪🏼 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## ❓ Troubleshooting

Encountering an issue? Before reaching out to support, we recommend heading to our [FAQ](https://docs.meema.io/) where you will find answers for the most commonly asked about questions/issues and gotchas with this Meema client. Feel free to join our Discord channel, we & the community can help this way as well.

## 🚨 Security

If you discover any security related issues, please email chris@cion.agency instead of using the issue tracker.

## 🙏🏼 Credits

- [Chris Breuer](https://github.com/Chris1904)
- [All Contributors](../../contributors)

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
