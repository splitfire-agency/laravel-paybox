<?php

namespace Sf\PayboxGateway\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use Sf\PayboxGateway\HttpClient\GuzzleHttpClient;

class PayboxServiceProvider extends ServiceProvider
{
  public function boot()
  {
    $this->loadTranslationsFrom(__DIR__ . "/../../translations", "paybox");
  }

  /**
   * Register service provider.
   */
  public function register()
  {
    // merge module config if it's not published or some entries are missing
    $this->mergeConfigFrom($this->configFile(), "paybox");

    // run migrations
    $this->loadMigrationsFrom(__DIR__ . "/../../database/migrations");

    // publish configuration file
    $this->publishes(
      [
        $this->configFile() =>
          $this->app["path.config"] . DIRECTORY_SEPARATOR . "paybox.php",
      ],
      "config"
    );

    // configure GuzzleHttpClient
    $this->app->singleton(GuzzleHttpClient::class, function ($app) {
      $guzzle = new Client($app["config"]->get("paybox.guzzle_options", []));
      return new GuzzleHttpClient($guzzle);
    });
  }

  /**
   * Get module config file.
   *
   * @return string
   */
  protected function configFile()
  {
    return realpath(__DIR__ . "/../../config/paybox.php");
  }
}
