<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

**Pickup-Puck**

- **Purpose**: Pickup-Puck is a small Laravel application to schedule and manage pickup hockey games — create games and seasons, register players and guests, collect payments, and generate balanced teams automatically before puck-drop.

**Key Features**
- **Create & manage games**: Admins can create games and seasons via the admin UI.
- **Player & guest registration**: Users can sign up for games and add guests (player or goalie roles).
- **Payments tracking**: Track payments per game and per user.
- **Automatic team generation**: Teams lock 30 minutes before game time and are balanced while respecting goalie placement. See [app/Services/GameTeamsService.php](app/Services/GameTeamsService.php).
- **Manual admin overrides**: Admins can move/remove players or guests and adjust scores.
- **Scheduled team generation**: Artisan command `pp:generate-teams` is scheduled in [app/Console/Kernel.php](app/Console/Kernel.php) and implemented in [app/Console/Commands/GenerateTeams.php](app/Console/Commands/GenerateTeams.php).

**Tech Stack**
- **Backend**: PHP 8.1+, Laravel 10 (see [composer.json](composer.json)).
- **Frontend**: Vite + Bootstrap, jQuery (see [package.json](package.json)).
- **Auth / Roles**: Laravel Sanctum and Spatie Permissions.

**Project Structure (high level)**
- **Models**: [app/Models](app/Models) (games under [app/Models/Games](app/Models/Games)).
- **Services**: [app/Services/GameTeamsService.php](app/Services/GameTeamsService.php) — core team logic.
- **Web routes & controllers**: [routes/web.php](routes/web.php) and controllers in [app/Http/Controllers](app/Http/Controllers).

**Quick Start (Local Development)**
Prerequisites: `php` >= 8.1, `composer`, Node.js & `npm`, and a database (MySQL/MariaDB).

1. Clone the repo

	```bash
	git clone <repo-url> pickuppuck
	cd pickuppuck
	```

2. Install PHP dependencies

	```bash
	composer install --no-interaction --prefer-dist
	```

3. Install frontend dependencies

	```bash
	npm install
	```

4. Create and configure `.env`

	- Copy the example: `cp .env.example .env` (or create `.env`).
	- Update DB and mail settings in `.env` (see existing `.env` for defaults).
	- Generate app key:

	  ```bash
	  php artisan key:generate
	  ```

5. Database setup

	```bash
	php artisan migrate
	php artisan db:seed  # if you have seeders
	```

6. Build assets and run dev server

	```bash
	npm run dev      # development (Vite)
	# or
	npm run build    # production build
	```

7. Serve the app

	```bash
	php artisan serve --host=0.0.0.0 --port=8000
	```

Open http://localhost:8000 and register an account.

**Generating Teams & Scheduling**
- The app automatically runs `php artisan pp:generate-teams` for games within the next 30 minutes. Manual trigger: `php artisan pp:generate-teams`.
- The scheduled command runs every 15 minutes via [app/Console/Kernel.php](app/Console/Kernel.php).

**Testing**
- Run unit/feature tests with PHPUnit:

  ```bash
  ./vendor/bin/phpunit
  ```

Configuration for tests lives in `phpunit.xml`.

**Important Implementation Notes**
- Team locking: teams are only "locked" at T-30 (30 minutes before `game.time`) and assignments made after lock are stable. The logic is in [app/Services/GameTeamsService.php](app/Services/GameTeamsService.php).
- Role limits: goalie slots are limited to two per game (users + guests combined). Validation exists in controllers such as [app/Http/Controllers/GameDetailController.php](app/Http/Controllers/GameDetailController.php).

**Common Commands**
- Run migrations: `php artisan migrate`
- Run scheduled commands locally: `php artisan schedule:work` or use a cron entry for `php artisan schedule:run`.
- Queue worker (if you enable queued jobs): `php artisan queue:work`

**Contributing**
- Please open issues or PRs. For code style, follow existing patterns and run `composer fix`/`npm run lint` if you add front-end code.

**License**
- This project uses the MIT license (see `composer.json`).

**Files to Inspect**
- Team generation logic: [app/Services/GameTeamsService.php](app/Services/GameTeamsService.php)
- Console command: [app/Console/Commands/GenerateTeams.php](app/Console/Commands/GenerateTeams.php)
- Web routes: [routes/web.php](routes/web.php)
- Environment example: [.env](.env)

If you'd like, I can:
- Add badges (build, php version, tests) to the top of this README.
- Create a short CONTRIBUTING guide and an architecture diagram for the team-generation algorithm.

---
Generated from repository analysis on the working workspace.
