<?php

namespace Tests;

use Sf\PayboxGateway\Providers\PayboxServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Orchestra\Testbench\TestCase;

class UnitTestCase extends TestCase
{
  use RefreshDatabase, DatabaseMigrations;

  /**
   * Define environment setup.
   *
   * @param  \Illuminate\Foundation\Application $app
   *
   * @return void
   */
  protected function getEnvironmentSetUp($app)
  {
    // Setup default database to use sqlite :memory:
    $app['config']->set('database.default', 'testbench');
    $app['config']->set('database.connections.testbench', [
      'driver'   => 'sqlite',
      'database' => ':memory:',
      'prefix'   => '',
      'strict'   => false
    ]);
  }

  protected function getPackageProviders($app)
  {
    return [
      PayboxServiceProvider::class,
    ];
  }

  public function setUp(): void
  {
    parent::setUp();
  }

  public function tearDown(): void
  {
    Mockery::close();
  }
}
