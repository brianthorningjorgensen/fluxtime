<?php

namespace Fluxuser\Utils;

use Google_Auth_LoginTicket;
use Google_Client;
use Google_Service_Plus;
use Zend\Session\Container;

/**
 * Class to hold Google Client and helper-methods to perform various actions 
 * with the Google API.
 *
 * @author Anders Bo Rasmussen <ara@supeo.dk>
 */
class GoogleAuth {

    protected $client;

    /**
     * Creates a Google Client baed on the provided Google Client.
     * 
     * @param Google_Client $googleClient 
     */
    public function __construct(Google_Client $googleClient) {
        $this->client = $googleClient;
    }

    /**
     * Checks if the user is loggen in.
     * 
     * @return boolean
     */
    public function isLoggedIn() {
        $session = new Container('google_auth');
        return isset($session->google_access_token);
    }

    /**
     * Creates a Google authentication URL and returns it.
     * The URL leads to Google's own website where the authentication of the 
     * user is performed.
     * 
     * @return URL
     */
    public function getAuthUrl() {
        return $this->client->createAuthUrl();
    }

    /**
     * If the GET request contains the Google Authentication code, we 
     * authenticates the Google User and store his Acces token via the 
     * helper method and return true, otherwise we return false.
     * 
     * Helper method @see setToken($token)
     * @return boolean
     */
    public function checkRedirectCode() {
        $code = filter_input(INPUT_GET, 'code');
        if (isset($code)) {
            $this->client->authenticate($code);

            $this->setToken($this->client->getAccessToken());

            return true;
        }
        return false;
    }

    /**
     * Helper method for @see checkRedirectCode()
     * Store the access token in a session for later use.
     * 
     * @param string $token @see Google_Client::getAccessToken() and 
     * @see Google_Client::setAccessToken($accessToken).
     * @access protected
     */
    protected function setToken($token) {
        $session = new Container('google_auth');
        $session->google_access_token = $token;

        $this->client->setAccessToken($token);
    }

    /**
     * Logs the user out by removing his access token in the session.
     */
    public function logout() {
        $session = new Container('google_auth');
        unset($session->google_access_token);
    }

    /**
     * Gets the attributes in the payload part of the login ticket.
     * 
     * @see Google_Auth_LoginTicket::getAttributes()
     * @return Array
     */
    public function getPayload() {
        $payload = $this->client->verifyIdToken()->getAttributes()['payload'];
        return $payload;
    }

    /**
     * Request user info from the Google Plus service.
     * @see <a href="https://developers.google.com/+/api/latest/people/get">Google Docs</a> 
     * @return Array
     */
    public function getUserInfo() {
        $plus = new Google_Service_Plus($this->client);
        return $plus->people->get('me');
    }
    
}
