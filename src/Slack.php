<?php


namespace Knevelina\AocProgressAnnouncer;


use GuzzleHttp\Client;

class Slack
{
    public static function post(string $message)
    {
        $client = new Client();
        $client->request('POST', $_ENV['SLACK_URL'], ['json' => [
            'text' => $message
        ]]);
    }
}