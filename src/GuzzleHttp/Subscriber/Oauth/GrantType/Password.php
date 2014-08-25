<?php

namespace GuzzleHttp\Subscriber\Oauth\GrantType;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Subscriber\Oauth\AccessToken;

class Password implements GrantTypeInterface
{
    protected $credentials;

    public function __construct(ClientInterface $client, $credentials)
    {
        $this->client = $client;
        $this->credentials = $credentials;
    }

    public function getAccessToken(AccessToken $accessToken = null)
    {
        list($username, $password) = $this->getCredentials();

        $body = [
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password
        ];

        $response = $this->client->post(null, ['body' => $body]);
        $data = $response->json();

        return AccessToken::createFromArray($data);
    }

    protected function getCredentials()
    {
        if (is_array($this->credentials)) {
            return $this->credentials;
        }

        if (is_callable($this->credentials)) {
            return call_user_func($this->credentials);
        }

        throw new \Exception('Credentials required');
    }
}
