<?php

namespace Datatheke\Component\Api;

use GuzzleHttp;
use GuzzleHttp\Subscriber\Oauth\Oauth2;
use GuzzleHttp\Subscriber\Oauth\AccessToken;
use GuzzleHttp\Subscriber\Oauth\GrantType\Password;
use GuzzleHttp\Subscriber\Oauth\GrantType\Refresh;

class Client
{
    const DEFAULT_URL = 'https://www.datatheke.com/';

    /**
     * GuzzleHttp\Client
     */
    protected $client;

    /**
     * GuzzleHttp\Subscriber\Oauth\Oauth2
     */
    protected $oauth2;

    /**
     * Url of the datatheke instance (including trailing slash)
     */
    protected $url;

    /**
     * Extra config for Guzzle client (proxy, ssl certificate, ...)
     */
    protected $config;

    /**
     * An array with username and password, or a callback wich returns username and password
     */
    protected $credentials;

    /**
     * Token data
     */
    protected $accessToken;
    protected $refreshToken;
    protected $expiresAt;

    public function __construct($accessToken = null, $refreshToken = null, $expiresAt = null)
    {
        $this->setTokenData($accessToken, $refreshToken, $expiresAt);
        $this->setUrl(self::DEFAULT_URL);
        $this->setConfig([]);
    }

    public function setTokenData($accessToken, $refreshToken, $expiresAt)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresAt = $expiresAt;
    }

    public function setUrl($url)
    {
        $this->url = $url.'api/v2/';
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function setCredentials($credentials)
    {
        if (!is_array($credentials) && !is_callable($credentials)) {
            throw new \Exception('Credentials must be an an array or callable');
        }

        $this->credentials = $credentials;

        return $this;
    }

    public function getLastToken()
    {
        if (null === $this->oauth2 || null === $this->oauth2->getAccessToken()) {
            return null;
        }

        return $this->oauth2->getAccessToken()->toArray();
    }

    protected function getClient()
    {
        if (null === $this->client) {
            $this->client = $this->createClient();
        }

        return $this->client;
    }

    protected function createClient()
    {
        $grantClient = new GuzzleHttp\Client([
            'base_url' => $this->url.'token',
            'defaults' => array_merge(['verify' => false], $this->config)
        ]);

        $client = new GuzzleHttp\Client([
            'base_url' => $this->url,
            'defaults' => array_merge(['verify' => false], $this->config, [
                'auth' => 'oauth2',
                'headers' => [
                    'Content-type'  => 'application/json'
                ]
            ])
        ]);

        $grantType = $this->credentials ? new Password($grantClient, $this->credentials) : null;
        $this->oauth2 = new Oauth2($grantType, new Refresh($grantClient));
        if ($this->accessToken) {
            $this->oauth2->setAccessToken(new AccessToken($this->accessToken, $this->refreshToken, $this->expiresAt));
        }
        $client->getEmitter()->attach($this->oauth2);

        return $client;
    }

    public function getLibraries($page = 1)
    {
        $response = $this->getClient()->get('libraries?page='.(int) $page);

        return $response->json();
    }

    public function getLibrary($id)
    {
        $response = $this->getClient()->get(array('libraries/{id}', array('id' => $id)));

        return $response->json();
    }

    public function getLibraryCollections($id, $page = 1)
    {
        $response = $this->getClient()->get(array('libraries/{id}/collections?page='.(int) $page, array('id' => $id)));

        return $response->json();
    }

    public function getCollection($id)
    {
        $response = $this->getClient()->get(array('collections/{id}', array('id' => $id)));

        return $response->json();
    }

    public function getCollectionItems($id, $page = 1)
    {
        $response = $this->getClient()->get(array('collections/{id}/items?page='.(int) $page, array('id' => $id)));

        return $response->json();
    }

    public function getItem($collectionId, $id)
    {
        $response = $this->getClient()->get(array('collections/{collectionId}/items/{id}', array('collectionId' => $collectionId, 'id' => $id)));

        return $response->json();
    }

    public function createLibrary($library)
    {
        $response = $this->getClient()->post('libraries', null, json_encode(array('library' => $library)));

        return $response->json();
    }

    public function createCollection($libraryId, $collection)
    {
        $response = $this->getClient()->post(array('libraries/{id}', array('id' => $libraryId)), null, json_encode(array('collection' => $collection)));

        return $response->json();
    }

    public function createItem($collectionId, $item)
    {
        $response = $this->getClient()->post(array('collections/{id}', array('id' => $collectionId)), null, json_encode(array('_'.$collectionId => $item)));

        return $response->json();
    }

    public function updateLibrary($libraryId, $library)
    {
        $response = $this->getClient()->put(array('libraries/{id}', array('id' => $libraryId)), null, json_encode(array('library' => $library)));

        return $response->json();
    }

    public function updateCollection($collectionId, $collection)
    {
        $response = $this->getClient()->put(array('collections/{id}', array('id' => $collectionId)), null, json_encode(array('collection' => $collection)));

        return $response->json();
    }

    public function updateItem($collectionId, $itemId, $item)
    {
        $response = $this->getClient()->put(array('collections/{collectionId}/items/{id}', array('collectionId' => $collectionId, 'id' => $itemId)), null, json_encode(array('_'.$collectionId => $item)));

        return $response->json();
    }

    public function deleteLibrary($id)
    {
        $response = $this->getClient()->delete(array('libraries/{id}', array('id' => $id)));

        return $response->json();
    }

    public function deleteCollection($id)
    {
        $response = $this->getClient()->delete(array('collections/{id}', array('id' => $id)));

        return $response->json();
    }

    public function deleteItem($collectionId, $id)
    {
        $response = $this->getClient()->delete(array('collections/{collectionId}/items/{id}', array('collectionId' => $collectionId, 'id' => $id)));

        return $response->json();
    }
}
