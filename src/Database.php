<?php

namespace Knevelina\AocProgressAnnouncer;

use Medoo\Medoo;

class Database
{
    private $database;

    public function __construct()
    {
        $this->database = new Medoo([
            'database_type' => 'sqlite',
            'database_file' => __DIR__.'/../data/database.sqlite'
        ]);

        $this->database->query('CREATE TABLE members (
            id integer,
            name varchar,
            score integer,
            last_star_ts integer
        );');
        
        $this->database->query('CREATE TABLE stars (
            member_id integer,
            day integer,
            part integer,
            ts integer
        );
        ');
    }

    public function __call($method, $args)
    {
        if (method_exists($this->database, $method)) {
            return call_user_func_array([$this->database, $method], $args);
        }

        trigger_error('Called undefined database method '.$method.'!');
    }

    public function getMembers(): array
    {
        return $this->select('members', ['id', 'name', 'score', 'last_star_ts']);
    }

    public function getMember(int $id)
    {
        return $this->select('members', ['id', 'name', 'score', 'last_star_ts'], ['id' => $id])[0] ?? null;
    }

    public function storeMember(int $id, string $name, int $score)
    {
        $this->insert('members', ['id' => $id, 'name' => $name, 'score' => $score]);
    }

    public function updateMember(int $id, string $name, int $score, int $lastStarTs)
    {
        $this->update('members', ['name' => $name, 'score' => $score, 'last_star_ts' => $lastStarTs], ['id' => $id]);
    }

    public function hasStar(int $memberId, int $day, int $part)
    {
        return $this->count('stars', ['member_id' => $memberId, 'day' => $day, 'part' => $part]) > 0;
    }

    public function storeStar(int $memberId, int $day, int $part, int $ts)
    {
        $this->insert('stars', ['member_id' => $memberId, 'day' => $day, 'part' => $part, 'ts' => $ts]);
    }
}