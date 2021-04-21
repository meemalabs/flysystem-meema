<?php

namespace Meema\Flysystem;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Config;
use Meema\MeemaApi\Client;

class MeemaAdapter extends AbstractAdapter
{
    /**
     * @var \Meema\Flysystem\Client
     */
    protected $client;

    public function __construct()
    {
        $this->client = new Client('pk_live|2|1|0dtrJyDb4FHoIIeKm4r0J9R9OX9WZyKlRpEamnEz', ['base_url' => 'http://meema-api.test/api/']);
    }

    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config   Config object
     *
     * @return false|array false on failure file meta data on success
     */
    public function write($path, $contents, Config $config)
    {
        return $this->upload($path);
    }

    /**
     * Write a new file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config)
    {
        return $this->upload($path);
    }

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents, Config $config)
    {
        return [];
    }

    /**
     * Update a file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource, Config $config)
    {
        return [];
    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath)
    {
        return [];
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        return [];
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        return [];
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        return [];
    }

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config)
    {
        return [];
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility)
    {
        return $this->client->request('POST', 'aws/set-visibility', compact('path', 'visibility'));
    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        $data = $this->client->request('POST', 'aws/has', compact('path'));

        return $data['exists'] ?? false;
    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return false|array
     */
    public function read($path)
    {
        return false;
    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        return [];
    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {
        return [];
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return false|array
     */
    public function getMetadata($path)
    {
        return $this->client->request('POST', 'aws/metadata', compact('path'));
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return false|array
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return false|array
     */
    public function getMimetype($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return false|array
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Upload a media file.
     *
     * @param string $path
     *
     * @return array
     */
    protected function upload($path)
    {
        $file = fopen($path, 'r');
        $stream = Psr7\stream_for($file);

        $fileName = basename($path);
        $mimeType = mime_content_type($file);

        $vaporParams = ['content_type' => $mimeType];

        $signedUrl = $this->client->request('POST', 'vapor/signed-storage-url', $vaporParams);

        if (is_array($signedUrl) && $signedUrl['url']) {
            $headers = $signedUrl['headers'];
            unset($headers['Host']);

            $this->uploadToS3($signedUrl['url'], $headers, $fileName, $stream);

            $uploadData = ['key' => $signedUrl['key'], 'file_name' => $fileName];

            $response = $this->client->request('POST', 'upload', $uploadData);
        }

        return $response;
    }

    /**
     * Upload the file stream to s3.
     *
     * @param string          $signedUrl
     * @param array           $headers
     * @param string          $fileName
     * @param GuzzleHttp\Psr7 $stream
     *
     * @return void
     */
    protected function uploadToS3($signedUrl, $headers, $fileName, $stream)
    {
        $client = new GuzzleClient();
        $request = new Request(
            'PUT',
            $signedUrl,
            ['headers' => json_encode($headers)],
            new Psr7\MultipartStream(
                [
                    [
                        'name'     => $fileName,
                        'contents' => $stream,
                    ],
                ]
            )
        );

        $client->send($request);
    }
}
