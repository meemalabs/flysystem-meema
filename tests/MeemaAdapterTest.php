<?php

uses(Tests\FlysystemTest::class);

beforeEach(function () {
    $this->initializeDotEnv();
    $this->initializeAdapter();
});

it('can get storage path metadata', function () {
    $path = 'flysystem-test/butterfly.jpg';

    $response = $this->adapter->getMetadata($path);

    $this->assertTrue(is_array($response));
    $this->assertTrue(array_key_exists('metadata', $response));
});

it('can get storage path visibility', function () {
    $path = 'flysystem-test/butterfly.jpg';

    $response = $this->adapter->getVisibility($path);

    $this->assertTrue(is_array($response));
    $this->assertTrue(array_key_exists('visibility', $response));
});

it('can get storage path size', function () {
    $path = 'flysystem-test/butterfly.jpg';

    $response = $this->adapter->getSize($path);

    $this->assertTrue(is_array($response));
    $this->assertTrue(array_key_exists('size', $response));
});

it('can get storage path mime type', function () {
    $path = 'flysystem-test/butterfly.jpg';

    $response = $this->adapter->getMimetype($path);

    $this->assertTrue(is_array($response));
    $this->assertTrue(array_key_exists('mimetype', $response));
});

it('can get storage path timestamp', function () {
    $path = 'flysystem-test/butterfly.jpg';

    $response = $this->adapter->getTimestamp($path);

    $this->assertTrue(is_array($response));
    $this->assertTrue(array_key_exists('timestamp', $response));
});

it('can detect if storage path path exists', function () {
    $path = 'flysystem-test/butterfly.jpg';

    $response = $this->adapter->has($path);

    $this->assertTrue(is_bool($response));
    $this->assertTrue($response);
});

it('can get contents of a directory', function () {
    $directory = 'flysystem-test';

    $response = $this->adapter->listContents($directory);

    $this->assertTrue(is_array($response));
    $this->assertTrue(count($response) > 0);
});

it('can set storage path visibility to private', function () {
    $visibility = 'private';

    $path = 'flysystem-test/butterfly.jpg';

    $response = $this->adapter->setVisibility($path, $visibility);

    $this->assertTrue(is_array($response));
    $this->assertTrue(array_key_exists('visibility', $response));
    $this->assertTrue($response['visibility'] === $visibility);
});

it('can set storage path visibility to public', function () {
    $visibility = 'public';

    $path = 'flysystem-test/butterfly.jpg';

    $response = $this->adapter->setVisibility($path, $visibility);

    $this->assertTrue(is_array($response));
    $this->assertTrue(array_key_exists('visibility', $response));
    $this->assertTrue($response['visibility'] === $visibility);
});

it('can set copy a file', function () {
    $path = 'flysystem-test/butterfly.jpg';
    $newPath = 'flysystem-test/butterfly-copy.jpg';

    $response = $this->adapter->copy($path, $newPath);

    $this->assertTrue(is_array($response));
    $this->assertTrue($response['message'] === 'File successfully copied.');
});

it('can set rename a file', function () {
    $path = 'flysystem-test/butterfly-copy.jpg';
    $newPath = 'flysystem-test/butterfly1.jpg';

    $response = $this->adapter->rename($path, $newPath);

    $this->assertTrue(is_array($response));
    $this->assertTrue($response['message'] === 'File successfully renamed.');
});

it('can set delete a file', function () {
    $path = 'flysystem-test/butterfly1.jpg';

    $response = $this->adapter->delete($path);

    $this->assertTrue(is_array($response));
    $this->assertTrue($response['message'] === 'File successfully deleted.');
});
