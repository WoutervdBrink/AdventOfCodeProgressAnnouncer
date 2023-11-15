<?php

namespace Knevelina\AocProgressAnnouncer;

use Medoo\Medoo;

class Database
{
    private $database;

    public function __construct()
    {
        $this->database = new Medoo(
            [
                'database_type' => 'sqlite',
                'database_file' => __DIR__ . '/../data/database.sqlite'
            ]
        );

        $this->database->query(
            'CREATE TABLE IF NOT EXISTS members (
            id integer,
            year integer,
            name varchar,
            score integer,
            last_star_ts integer,
            UNIQUE(id, year)
        );'
        );

        $this->database->query(
            'CREATE TABLE IF NOT EXISTS stars (
            member_id integer,
            year integer,
            day integer,
            part integer,
            ts integer
        );
        '
        );
    }

    public function __call($method, $args)
    {
        if (method_exists($this->database, $method)) {
            return call_user_func_array([$this->database, $method], $args);
        }

        trigger_error('Called undefined database method ' . $method . '!');
    }

    public function getMember(int $year, int $id)
    {
        return $this->select('members', ['id', 'name', 'score', 'last_star_ts'], compact('year', 'id'))[0] ?? null;
    }

    public function memberInSystem(int $id): bool
    {
        echo $id."\n";
        echo count($this->select('members', ['id'], compact('id')))."\n";
        return count($this->select('members', ['id'], compact('id'))) > 0;
    }

    public function storeMember(int $year, int $id, string $name, int $score)
    {
        $this->insert('members', compact('year', 'id', 'name', 'score'));
    }

    public function updateMember(int $year, int $id, string $name, int $score, int $last_star_ts)
    {
        $this->update('members', compact('year', 'id', 'name', 'score', 'last_star_ts'), compact('id', 'year'));
    }

    public function hasStar(int $year, int $member_id, int $day, int $part)
    {
        return $this->count('stars', compact('year', 'member_id', 'day', 'part')) > 0;
    }

    public function storeStar(int $year, int $member_id, int $day, int $part, int $ts)
    {
        $this->insert('stars', compact('year', 'member_id', 'day', 'part', 'ts'));
    }
}