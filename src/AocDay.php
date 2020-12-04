<?php


namespace Knevelina\AocProgressAnnouncer;


class AocDay
{
    protected int $day;
    protected $part1;
    protected $part2;

    public function __construct(int $day, ?int $part1, ?int $part2)
    {
        $this->day = $day;
        $this->part1 = $part1;
        $this->part2 = $part2;
    }

    public function hasPart1(): bool
    {
        return !is_null($this->part1);
    }

    public function hasPart2(): bool
    {
        return !is_null($this->part2);
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function getPart1(): ?int
    {
        return $this->part1;
    }

    public function getPart2(): ?int
    {
        return $this->part2;
    }
}