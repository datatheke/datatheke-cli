<?php

namespace Datatheke\Component\RestApi;

use GuzzleHttp;

class Client
{
    const DEFAULT_URL = 'https://www.datatheke.com/';
    
    /**
     * Url
     */
    protected $url;

    /**
     * Token
     */
    protected $accessToken;
    protected $refreshToken;
    protected $expiresIn;

    public function __construct($accessToken, $refreshToken = null, $expiresIn = null, $url = null)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresIn = $expiresIn;
        $this->url = ($url ?: self::DEFAULT_URL).'api/v2/';
    }

    public static function createFromUserCredentials($username, $password, $url = null)
    {
        $response = GuzzleHttp\post(($url ?: self::DEFAULT_URL).'api/v2/token', [
            'body' => [
                'grant_type' => 'password',
                'username' => $username,
                'password' => $password
            ]
        ]);
        
        $token = $response->json();
        
        return new self($token['access_token'], $token['refresh_token'], $token['expires_in'], $url);
    }

    public function getToken()
    {
        return [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'expires_in' => $this->expiresIn
        ];
    }

    protected function getClient()
    {
        static $client;
        
        if (!$client) {
            $client = new GuzzleHttp\Client([
                'base_url' => $this->url,
                'defaults' => [
                    'verify' => false,
                    'headers' => [
                        'Authorization' => 'Bearer '.$this->accessToken,
                        // 'Content-type'  => 'application/json'
                    ]
                ]
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
