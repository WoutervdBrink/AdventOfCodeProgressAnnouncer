<?php

namespace Knevelina\AocProgressAnnouncer;

use GuzzleHttp\Exception\GuzzleException;

class AocLeaderboard
{
    private string $event;

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getMembers(): array
    {
        return $this->members;
    }
    private array $members;

    public function __construct(string $event, array $members)
    {
        $this->event = $event;
        $this->members = $members;
    }


    /**
     * @throws GuzzleException
     */
    public static function query(int $year): AocLeaderboard
    {
        $jar = \GuzzleHttp\Cookie\CookieJar::fromArray([
            'session' => $_ENV['SESSION_COOKIE']
        ], 'adventofcode.com');

        $client = new \GuzzleHttp\Client();
        $response = $client->request(
            'GET',
            sprintf('https://adventofcode.com/%d/leaderboard/private/view/%s.json', $year, $_ENV['LEADERBOARD']),
            [ 'cookies' => $jar ]
        );

        if ($response->getStatusCode() != 200) {
            throw new \Exception("Request failed with ".$response->getStatusCode().": ".$response->getBody());
        }

        $data = @json_decode($response->getBody());

        $members = [];

        foreach ($data->members as $member) {
            $members[] = new AocMember($member);
        }

        return new static($data->event, $members);
    }
}