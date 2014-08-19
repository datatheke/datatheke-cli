<?php

namespace Datatheke\Component\Api;

use GuzzleHttp;

class Client
{
    const DEFAULT_URL = 'https://www.datatheke.com/';

    /**
     * Url of the datatheke instance (including trailing slash)
     */
    protected $url;

    /**
     * Extra config for Guzzle client (proxy, ssl certificate, ...)
     */
    protected $config;

    /**
     * Token
     */
    protected $accessToken;
    protected $refreshToken;
    protected $expiresAt;

    public function __construct($accessToken, $refreshToken = null, $expiresAt = null, $url = null, array $config = [])
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresAt = $expiresAt;

        $this->url = ($url ?: self::DEFAULT_URL).'api/v2/';
        $this->config = $config;
    }

    public static function createFromUserCredentials($username, $password, $url = null, array $config = [])
    {
        $options = array_merge(['verify' => false], $config, [
            'body' => [
                'grant_type' => 'password',
                'username' => $username,
                'password' => $password
            ]
        ]);

        $token = GuzzleHttp\post(($url ?: self::DEFAULT_URL).'api/v2/token', $options)->json();
        $expiresAt = strtotime(sprintf('+%d seconds', $token['expires_in']));

        return new self($token['access_token'], $token['refresh_token'], $expiresAt, $url, $config);
    }

    public function getToken()
    {
        return [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'expires_at' => $this->expiresAt
        ];
    }

    protected function getClient()
    {
        static $client;

        if (!$client) {
            $defaults = array_merge(['verify' => false], $this->config, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    // 'Content-type'  => 'application/json'
                ]
            ]);

            $client = new GuzzleHttp\Client([
                'base_url' => $this->url,
                'defaults' => $defaults
            ]);
        }

        return $client;
    }

    public function getLibraries($page = 1)
    {
        $response = $this->getClient()->get('library?page='.(int) $page);

        return $response->json();
    }

    public function getLibrary($id)
    {
        $response = $this->getClient()->get(array('library/{id}', array('id' => $id)));

        return $response->json();
    }

    public function getLibraryCollections($id, $page = 1)
    {
        $response = $this->getClient()->get(array('library/{id}/collection?page='.(int) $page, array('id' => $id)));

        return $response->json();
    }

    public function getCollection($id)
    {
        $response = $this->getClient()->get(array('collection/{id}', array('id' => $id)));

        return $response->json();
    }

    public function getCollectionItems($id, $page = 1)
    {
        $response = $this->getClient()->get(array('collection/{id}/item?page='.(int) $page, array('id' => $id)));

        return $response->json();
    }

    public function getItem($collectionId, $id)
    {
        $response = $this->getClient()->get(array('collection/{collectionId}/item/{id}', array('collectionId' => $collectionId, 'id' => $id)));

        return $response->json();
    }

    public function createLibrary($library)
    {
        $response = $this->getClient()->post('library', null, json_encode(array('library' => $library)));

        return $response->json();
    }

    public function createCollection($libraryId, $collection)
    {
        $response = $this->getClient()->post(array('library/{id}', array('id' => $libraryId)), null, json_encode(array('collection' => $collection)));

        return $response->json();
    }

    public function createItem($collectionId, $item)
    {
        $response = $this->getClient()->post(array('collection/{id}', array('id' => $collectionId)), null, json_encode(array('_'.$collectionId => $item)));

        return $response->json();
    }

    public function updateLibrary($libraryId, $library)
    {
        $response = $this->getClient()->put(array('library/{id}', array('id' => $libraryId)), null, json_encode(array('library' => $library)));

        return $response->json();
    }

    public function updateCollection($collectionId, $collection)
    {
        $response = $this->getClient()->put(array('collection/{id}', array('id' => $collectionId)), null, json_encode(array('collection' => $collection)));

        return $response->json();
    }

    public function updateItem($collectionId, $itemId, $item)
    {
        $response = $this->getClient()->put(array('collection/{collectionId}/item/{id}', array('collectionId' => $collectionId, 'id' => $itemId)), null, json_encode(array('_'.$collectionId => $item)));

        return $response->json();
    }

    public function deleteLibrary($id)
    {
        $response = $this->getClient()->delete(array('library/{id}', array('id' => $id)));

        return $response->json();
    }

    public function deleteCollection($id)
    {
        $response = $this->getClient()->delete(array('collection/{id}', array('id' => $id)));

        return $response->json();
    }

    public function deleteItem($collectionId, $id)
    {
        $response = $this->getClient()->delete(array('collection/{collectionId}/item/{id}', array('collectionId' => $collectionId, 'id' => $id)));

        return $response->json();
    }
}
