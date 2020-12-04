# Advent of Code Progress Announcer
Posts messages to Slack whenever someone gets a new star in Advent of Code.

## Installation
Install the requirements with `composer install`.
 
Copy `.env.example` to `.env` and edit it:

- `SESSION_COOKIE` is a valid AoC session cookie - use your browser's inspector.
- `LEADERBOARD` is the ID of the leaderboard. It's in the leaderboard URL, but it's also the ID of the owner.
- `SLACK_URL` is a valid Slack Incoming Webhook.

Now configure something, like cron, to run `run.php` every 15 minutes. Don't run it more often than that - the AoC servers don't like that.