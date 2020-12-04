<?php

namespace Knevelina\AocProgressAnnouncer;


class AocMember
{
    protected int $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getStars(): int
    {
        return $this->stars;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getLastStarTs(): int
    {
        return $this->lastStarTs;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return array
     */
    public function getDays(): array
    {
        return $this->days;
    }

    protected int $stars;
    protected string $name;
    protected int $lastStarTs;
    protected int $score;
    protected array $days;

    public function __construct($apiData)
    {
        $this->id = $apiData->id;
        $this->stars = $apiData->stars;
        $this->name = $apiData->name;
        $this->lastStarTs = $apiData->last_star_ts;
        $this->score = $apiData->local_score;

        $this->days = [];

        foreach ($apiData->completion_day_level as $day => $stars) {
            $this->days[] = new AocDay(
                $day,
                $stars->{1}->get_star_ts ?? null,
                $stars->{2}->get_star_ts ?? null
            );
        }
    }
}