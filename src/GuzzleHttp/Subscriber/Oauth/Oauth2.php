<?php

namespace GuzzleHttp\Subscriber\Oauth;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Subscriber\Oauth\GrantType\GrantTypeInterface;

class Oauth2 implements SubscriberInterface
{
    private $accessToken;
    private $grantType;
    private $refreshType;

    public function __construct(GrantTypeInterface $grantType = null, GrantTypeInterface $refreshType = null)
    {
        $this->grantType = $grantType;
        $this->refreshType = $refreshType;
    }

    public function getEvents()
    {
        return [
            'before' => ['onBefore', RequestEvents::SIGN_REQUEST],
            'error'  => ['onError', RequestEvents::EARLY],
        ];
    }

    public function onBefore(BeforeEvent $event)
    {
        $request = $event->getRequest();

        // Only sign requests using "auth"="oauth2"
        if ($request->getConfig()->get('auth') != 'oauth2') {
            return;
        }

        $token = $this->requestAccessToken();
        $header = $this->getAuthorizationHeader($token);

        $request->setHeader('Authorization', $header);
    }

    public function onError(ErrorEvent $event)
    {
echo "ERROR\n";
echo $event->getResponse()->getStatusCode()."\n";
echo $event->getResponse()->getBody()."\n";

        if (401 == $event->getResponse()->getStatusCode()) {
            $request = $event->getRequest();
            if (!$request->getConfig()->get('retried')) {
                if ($this->acquireAccessToken()) {
                    $request->getConfig()->set('retried', true);
                    $this->setHeader($request);
                    $event->intercept($event->getClient()->send($request));
                }
            }
        }
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set access token
     *
     * @param AccessToken $accessToken OAuth2 access token
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Get access token
     *
     * @return AccessToken Oauth2 access token
     */
    public function requestAccessToken()
    {
        if (null === $this->accessToken) {
            $this->acquireAccessToken();
        } elseif ($this->accessToken->hasExpired()) {
            try {
                $this->refreshToken();
            } catch (\Exception $e) {
                echo "==============\n";
                echo "refreshToken failed, try acquireAccessToken()\n";
                echo "==============\n";

                $this->acquireAccessToken();
            }
        }

        return $this->accessToken;
    }

    private function setHeader(RequestInterface $request)
    {
        $token = $this->getAccessToken();
        $header = $this->getAuthorizationHeader($token);
        $request->setHeader('Authorization', $header);
    }

    private function getAuthorizationHeader(AccessToken $token)
    {
        return sprintf('Bearer %s', $token->getAccessToken());
    }

    private function acquireAccessToken()
    {
        echo "==============\n";
        echo "acquireAccessToken()\n";
        echo "==============\n";

        if ($this->grantType) {
            $this->accessToken = $this->grantType->getAccessToken($this->accessToken);
        }

        return $this->accessToken;
    }

    private function refreshToken()
    {
        echo "==============\n";
        echo "refreshToken()\n";
        echo "==============\n";

        if ($this->refreshType) {
            $this->accessToken = $this->refreshType->getAccessToken($this->accessToken);
        }

        return $this->accessToken;
    }
}
