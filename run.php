<?php

use Dotenv\Dotenv;
use Knevelina\AocProgressAnnouncer\AocDay;
use Knevelina\AocProgressAnnouncer\AocLeaderboard;
use Knevelina\AocProgressAnnouncer\AocMember;
use Knevelina\AocProgressAnnouncer\Database;
use Knevelina\AocProgressAnnouncer\Slack;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db = new Database();

function processYears(int $startYear, int $endYear): array
{
    global $db;

    $newMembers = [];

    for ($year = $startYear; $year <= $endYear; $year++) {
        $newStars = [];

        $leaderboard = AocLeaderboard::query($year);

        /** @var AocMember $member */
        foreach ($leaderboard->getMembers() as $member) {
            $dbMember = $db->getMember($year, $member->getId());

            if ($dbMember === null) {
                if (!$db->memberInSystem($member->getId())) {
                    $newMembers[] = $member;
                }
                $db->storeMember($year, $member->getId(), $member->getName(), $member->getScore());
                $dbMember = $db->getMember($year, $member->getId());
            }

            if ($dbMember['last_star_ts'] === $member->getLastStarTs()) {
                continue;
            }

            /** @var AocDay $day */
            foreach ($member->getDays() as $day) {
                if ($day->hasPart1() && !$db->hasStar($year, $member->getId(), $day->getDay(), 1)) {
                    $newStars[] = ['member' => $member, 'day' => $day->getDay(), 'part' => 1];
                    $db->storeStar($year, $member->getId(), $day->getDay(), 1, $day->getPart1());
                }

                if ($day->hasPart2() && !$db->hasStar($year, $member->getId(), $day->getDay(), 2)) {
                    $newStars[] = ['member' => $member, 'day' => $day->getDay(), 'part' => 2];
                    $db->storeStar($year, $member->getId(), $day->getDay(), 2, $day->getPart2());
                }
            }

            $db->updateMember($year, $member->getId(), $member->getName(), $member->getScore(), $member->getLastStarTs());
        }

        foreach ($newStars as $star) {
            $member = $star['member'];
            $newMemberIds = array_map(fn ($a) => $a->getId(), $newMembers);
            if (!in_array($member->getId(), $newMemberIds)) {
                $day = $star['day'];
                $part = $star['part'];

                Slack::post(sprintf('[%04d] %s has just completed day %d, part %d!', $year, $member->getName(), $day, $part));
                sleep(1);
            }
        }
    }

    return array_map(fn (AocMember $member): string => $member->getName(), $newMembers);
}

$newMemberNames = processYears(2015, date('m') > 11 ? date('Y') : date('Y') - 1);

sort($newMemberNames);
$newMemberNames = array_unique($newMemberNames);

foreach ($newMemberNames as $member) {
    Slack::post('Welcome, ' . $member. '!');
}