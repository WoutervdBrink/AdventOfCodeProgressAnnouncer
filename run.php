<?php

use Knevelina\AocProgressAnnouncer\AocDay;
use Knevelina\AocProgressAnnouncer\AocLeaderboard;
use Dotenv\Dotenv;
use Knevelina\AocProgressAnnouncer\AocMember;
use Knevelina\AocProgressAnnouncer\Database;
use Knevelina\AocProgressAnnouncer\Slack;

require_once __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$leaderboard = AocLeaderboard::query();

$db = new Database();
$newMembers = [];
$newStars = [];

/** @var AocMember $member */
foreach ($leaderboard->getMembers() as $member) {
    $dbMember = $db->getMember($member->getId());

    if ($dbMember === null) {
        $newMembers[] = $member;
        $db->storeMember($member->getId(), $member->getName(), $member->getScore());
        $dbMember = $db->getMember($member->getId());
    }

    if ($dbMember['last_star_ts'] === $member->getLastStarTs()) {
        continue;
    }

    /** @var AocDay $day */
    foreach ($member->getDays() as $day) {
        if ($day->hasPart1() && !$db->hasStar($member->getId(), $day->getDay(), 1)) {
            $newStars[] = ['member' => $member, 'day' => $day->getDay(), 'part' => 1];
            $db->storeStar($member->getId(), $day->getDay(), 1, $day->getPart1());
        }

        if ($day->hasPart2() && !$db->hasStar($member->getId(), $day->getDay(), 2)) {
            $newStars[] = ['member' => $member, 'day' => $day->getDay(), 'part' => 2];
            $db->storeStar($member->getId(), $day->getDay(), 2, $day->getPart2());
        }
    }

    $db->updateMember($member->getId(), $member->getName(), $member->getScore(), $member->getLastStarTs());
}

/** @var AocMember $member */
foreach ($newMembers as $member) {
    Slack::post('Welcome, '.$member->getName().'!');
}

foreach ($newStars as $star) {
    $member = $star['member'];
    $day = $star['day'];
    $part = $star['part'];

    Slack::post(sprintf('%s has just completed day %d, part %d!', $member->getName(), $day, $part));
}