<?php

namespace Tests\Providers;

use Illuminate\Config\Repository as Config;
use Sf\PayboxGateway\HttpClient\GuzzleHttpClient;
use Sf\PayboxGateway\Providers\PayboxServiceProvider;
use Illuminate\Foundation\Application;
use Mockery;
use Tests\UnitTestCase;

/**
 * Class PayboxServiceProviderTest
 * @package Tests\Providers
 * @group PayboxServiceProviderTest
 */
class PayboxServiceProviderTest extends UnitTestCase
{
  /**
   *
   */
  public function testDoesAllRequiredActionsWhenRegistering()
  {
    $app = Mockery::mock(Application::class);

    $moduleConfigFile = realpath(__DIR__ . "/../../config/paybox.php");
    $configPath = "dummy/config/path";

    $payboxProvider = Mockery::mock(PayboxServiceProvider::class, [$app])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();

    // merge config
    $payboxProvider
      ->shouldReceive("mergeConfigFrom")
      ->with($moduleConfigFile, "paybox")
      ->once();

    // publishing configuration files
    $app
      ->shouldReceive("offsetGet")
      ->with("path.config")
      ->once()
      ->andReturn($configPath);

    $payboxProvider->shouldReceive("loadMigrationsFrom")->once();

    // Configure http client
    $config = Mockery::mock(Config::class);
    $config
      ->shouldReceive("get")
      ->with("paybox.guzzle_options", [])
      ->andReturn([
        "timeout" => 30.0,
        "connect_timeout" => 10.0,
      ]);

    $app
      ->shouldReceive("singleton")
      ->once()
      ->with(
        GuzzleHttpClient::class,
        Mockery::on(function ($closure) use ($app, $config) {
          $app
            ->shouldReceive("offsetGet")
            ->with("config")
            ->andReturn($config);

          $result = $closure($app);
          return $result instanceof GuzzleHttpClient;
        })
      );

    $payboxProvider->register();
    $this->assertTrue(true);
  }
}
