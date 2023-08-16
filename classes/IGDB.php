<?php

/*
 * Retrieves information from the IGDB API
 */

namespace UpcomingGames;

class IGDB
{
    private $accessToken;
    private $clientId;

    public function __construct($clientId, $accessToken)
    {
        $this->clientId = $clientId;
        $this->accessToken = $accessToken;
    }


    public function fetchUpcomingGames($platformIds = [], $limit = 50)
    {

        // If no platform IDs are provided, default to PS4 and PS5.
        if (empty($platformIds)) {
            $platformIds = [48, 167]; // Default IDs for PS4 and PS5
        }

        $currentTimestamp = time();

        $body = "
    fields name,release_dates.date,platforms.name,cover.image_id;
    where release_dates.platform = (".implode(',', $platformIds).") & release_dates.date > {$currentTimestamp};
    sort release_dates.date asc;
    limit {$limit};";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.igdb.com/v4/games",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => [
                "Client-ID: {$this->clientId}",
                "Authorization: Bearer {$this->accessToken}",
                "Content-Type: text/plain"
            ],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            return ['error' => curl_error($curl)];
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode != 200) {
            return ['error' => "HTTP Code: {$httpCode}, Response: {$response}"];
        }

        curl_close($curl);

        $games = json_decode($response, true);

        // Construct the image URL for each game
        foreach ($games as &$game) {
            if (isset($game['cover']['image_id'])) {
                $game['cover_url'] = "https://images.igdb.com/igdb/image/upload/t_cover_big/{$game['cover']['image_id']}.jpg";
            } else {
                $game['cover_url'] = null;
            }
        }

        return $games;
    }

    public function sort_list($array) {
        usort($array, function($a, $b) {
            $dateA = isset($a['release_dates'][0]['date']) ? $a['release_dates'][0]['date'] : null;
            $dateB = isset($b['release_dates'][0]['date']) ? $b['release_dates'][0]['date'] : null;

            // If both dates are set, compare them.
            if ($dateA !== null && $dateB !== null) {
                return $dateA <=> $dateB;
            }

            // If one of the dates is not set, push that item to the end.
            if ($dateA === null) {
                return 1;  // Push $a to the end
            }
            if ($dateB === null) {
                return -1; // Push $b to the end
            }

            return 0;  // If both dates are null, they are equal.
        });

        return $array;
    }

}
