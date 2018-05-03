<?php

namespace Asahasrabuddhe\LaravelAPI\Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Asahasrabuddhe\LaravelAPI\Tests\Models\Post;
use Asahasrabuddhe\LaravelAPI\Tests\Models\User;
use Asahasrabuddhe\LaravelAPI\Routing\BaseRouter;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Asahasrabuddhe\LaravelAPI\Tests\Models\Address;
use Asahasrabuddhe\LaravelAPI\Tests\Models\Comment;
use Asahasrabuddhe\LaravelAPI\Tests\Http\Controllers\PostController;
use Asahasrabuddhe\LaravelAPI\Tests\Http\Controllers\UserController;
use Asahasrabuddhe\LaravelAPI\Tests\Http\Controllers\AddressController;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return ['Asahasrabuddhe\LaravelAPI\Providers\APIServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return [
            'ApiRoute' => 'Asaharabuddhe\LaravelAPI\Facades\ApiRoute',
        ];
    }

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--path'     => './database/migrations',
        ]);

        $this->migrateDatabase();

        $this->seedDatabase();

        $this->app->make(BaseRouter::class)->resource('users', UserController::class);
        $this->app->make(BaseRouter::class)->resource('posts', PostController::class);
        $this->app->make(BaseRouter::class)->resource('address', AddressController::class);
        $this->app->make(BaseRouter::class)->resource('comments', CommentController::class);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
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
        ]);
        $app['config']->set('api.perPage', 10);
    }

    protected function migrateDatabase()
    {
        /** @var \Illuminate\Database\Schema\Builder $schemaBuilder */
        $schemaBuilder = $this->app['db']->connection()->getSchemaBuilder();
        $schemaBuilder->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $schemaBuilder->create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        $schemaBuilder->create('addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->text('line_1');
            $table->text('line_2');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('zip_code');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        $schemaBuilder->create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('content');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        $schemaBuilder->create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->integer('post_id')->unsigned();
            $table->foreign('post_id')->references('id')->on('posts');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    protected function seedDatabase()
    {
        $faker = \Faker\Factory::create();
        for ($i = 1; $i <= 50; $i++) {
            User::create([
                'name'           => $faker->name,
                'email'          => $faker->unique()->safeEmail,
                'password'       => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
                'remember_token' => str_random(10),
            ]);
        }

        for ($i = 1; $i <= 50; $i++) {
            Address::create([
                'line_1'   => $faker->streetAddress,
                'line_2'   => $faker->secondaryAddress,
                'city'     => $faker->city,
                'state'    => $faker->state,
                'country'  => $faker->country,
                'zip_code' => $faker->postcode,
                'user_id'  => $i,
            ]);
        }

        for ($i = 1; $i <= 200; $i++) {
            Post::create([
                'title'   => $faker->realText($maxNbChars = 200, $indexSize = 2),
                'content' => $faker->text($maxNbChars = 200),
                'user_id' => $faker->numberBetween(1, 50),
            ]);
        }

        for ($i = 1; $i <= 400; $i++) {
            Comment::create([
                'content' => $faker->text($maxNbChars = 100),
                'post_id' => $faker->numberBetween(1, 200),
                'user_id' => $faker->numberBetween(1, 50),
            ]);
        }
    }
}
