<?php

/*
 * Authenticates with the IGDB API
 */

namespace UpcomingGames;

class Authentication
{
    private $clientId;
    private $clientSecret;

    public function __construct($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function getAccessToken()
    {
        $url = "https://id.twitch.tv/oauth2/token?client_id={$this->clientId}&client_secret={$this->clientSecret}&grant_type=client_credentials";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            return null;
        }

        curl_close($curl);

        $responseData = json_decode($response, true);

        return $responseData['access_token'] ?? null;
    }
}
