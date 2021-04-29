<?php

namespace Tests;

use Meema\Flysystem\MeemaAdapter;
use Meema\MeemaClient\Client;
use Orchestra\Testbench\TestCase;

class FlysystemTest extends TestCase
{
    /**
     * @var \Meema\Flysystem\MeemaAdapter
     */
    public $adapter;

    public function initializeDotEnv(): void
    {
        if (! file_exists(__DIR__.'/../.env')) {
            return;
        }

        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();
    }

    public function initializeAdapter()
    {
        $teamId = env('PUBLISHABLE_KEY');

        $client = new Client($teamId, ['base_url' => env('BASE_URL_TEST')]);

        $adapter = new MeemaAdapter($client);

        $this->adapter = $adapter;

        return $this;
    }
}
