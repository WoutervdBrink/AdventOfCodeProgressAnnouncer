<?php


namespace Knevelina\AocProgressAnnouncer;


use GuzzleHttp\Client;

class Slack
{
    public static function post(string $message)
    {
        $client = new Client();
        $urls = explode(",", $_ENV['SLACK_URLS']);
        foreach ($urls as $url) {
            $client->request('POST', $url, ['json' => [
                'text' => $message
            ]]);
        }
    }
}
