<!-- Pickup-Puck README -->

<p align="center">
	<img src="https://raw.githubusercontent.com/edent/SuperTinyIcons/master/images/svg/hockey.svg" alt="Pickup-Puck" width="120" />
	<h1>Pickup-Puck 🏒</h1>
	<p>Lightweight app to schedule pickup hockey, track attendees, payments, and auto-generate balanced teams.</p>

	<!-- badges -->
	<p>
		<img src="https://img.shields.io/badge/php-8.1%2B-8892BF.svg" alt="PHP" />
		<img src="https://img.shields.io/badge/laravel-10-orange.svg" alt="Laravel" />
		<img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License" />
		<img src="https://img.shields.io/badge/tests-phpunit-lightgrey.svg" alt="Tests" />
	</p>
</p>

## What is Pickup-Puck?

Pickup-Puck is an opinionated, web-based tool for organizing pickup hockey: create games and seasons, register players and guests, collect payments, and—most importantly—generate balanced teams just before puck-drop while ensuring goalie slots are respected.

Why you'll like it:
- Simple admin flows for game & season creation
- Player + guest registration with role support (player/goalie)
- Payments tracking per game
- Automatic team generation that locks 30 minutes before game time

## Features

- 🏒 Create games & seasons
- 👥 Player & guest registration (guest name suggestions)
- 💳 Payment collection and tracking per game
- ⚖️ Auto-balanced teams with goalie-aware placement
- 🔧 Admin overrides (move/remove players, set scores)
- ⏱️ Scheduled team generation via Artisan command `pp:generate-teams`

## Quick Start (local)

Prereqs: `php` >= 8.1, `composer`, Node.js & `npm`, and a MySQL-compatible DB.

1. Clone and install

```bash
git clone <repo-url> pickuppuck
cd pickuppuck
composer install
npm install
```

2. Copy and edit environment

```bash
cp .env.example .env
# update DB credentials, mail, etc. See existing .env for defaults
php artisan key:generate
```

3. Migrate DB

```bash
php artisan migrate
php artisan db:seed   # optional, if seeders exist
```

4. Build assets and run

```bash
npm run dev
php artisan serve --port=8000
```

Open http://localhost:8000 and create an account.

## Important Commands

- Run scheduled job locally: `php artisan schedule:work`
- Trigger team generation manually: `php artisan pp:generate-teams`
- Run tests: `./vendor/bin/phpunit`

## How Team Generation Works (overview)

- Teams are only "locked" 30 minutes before `game.time`.
- When locking, the system ensures `game_teams` exist and assigns players/guests into two teams (Dark / Light).
- Goalies are prioritized: the algorithm attempts to place one goalie per team when possible.
- If both teams already have a goalie, new players fill the smaller team to keep balance.
- Once teams are locked, assignments remain stable; admins can still manually move members.

Core implementation: [app/Services/GameTeamsService.php](app/Services/GameTeamsService.php)

## Data Model (high level)

- `games`, `seasons`, `game_players` (users), `game_players_guests` (guest attendees)
- `game_teams_players`, `game_teams_guests` persist team assignments

See [app/Models](app/Models) and [app/Models/Games](app/Models/Games).

## Admin Notes

- A one-time setup route exists to promote the first admin: `/setup/elevate-me-puck-admin` (throttled).
- Admin routes are under `/admin` and require the `admin` role.

## Contributing

Love to have contributions! Suggested first steps:

1. Open an issue describing the feature or bug.
2. Fork and branch from `main`.
3. Follow existing code style. Run tests before submitting.

If you want, I can add a `CONTRIBUTING.md` and a small CI pipeline.

## License

MIT — see `composer.json`.

---
If you'd like, I can now:
- Add CI/test badges tied to this repo
- Create `CONTRIBUTING.md` and a short developer guide
- Add a simple SVG architecture diagram for the team-generation logic

Generated from repository analysis.
5. Database setup
