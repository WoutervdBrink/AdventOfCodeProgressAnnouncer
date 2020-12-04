<?php

namespace Knevelina\AocProgressAnnouncer;

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


    public static function query()
    {
        $jar = \GuzzleHttp\Cookie\CookieJar::fromArray([
            'session' => $_ENV['SESSION_COOKIE']
        ], 'adventofcode.com');

        $client = new \GuzzleHttp\Client();
        $response = $client->request(
            'GET',
            sprintf('https://adventofcode.com/2020/leaderboard/private/view/%s.json', $_ENV['LEADERBOARD']),
            [ 'cookies' => $jar ]
        );

        $data = @json_decode($response->getBody());

        $members = [];

        foreach ($data->members as $member) {
            $members[] = new AocMember($member);
        }

        return new static($data->event, $members);
    }
}