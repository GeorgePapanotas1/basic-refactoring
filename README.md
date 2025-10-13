Minimal PHP Development Scaffold

This repository provides a reusable, minimal environment for PHP development and presentations, including Docker Compose, Composer, PHPUnit, .env support, HTTP, and a Laravel-like console for commands.

Contents
- Dockerfile: PHP 8.3 CLI with Composer preinstalled
- docker-compose.yml: App service for CLI and Web service for HTTP (built-in server)
- composer.json: PSR-4 autoloading, PHPUnit dev dependency, dotenv autoload, and console dependency
- phpunit.xml: PHPUnit configuration
- bin/console: Laravel-like console entry point (auto-discovers commands)
- src/: Place your application code here
- src/Console/Commands: Put your console command classes here
- tests/: Place your PHPUnit tests here
- scripts/: Example standalone PHP scripts (optional)
- .env.example: Example environment variables file

Prerequisites
- Docker Desktop (or compatible Docker engine)
- Docker Compose v2 (included with recent Docker Desktop)

Quick start
1) Build the dev image
   docker compose build

2) Install Composer dependencies
   docker compose run --rm app composer install

3) (Optional) Initialize .env from example
   docker compose run --rm app php -r "file_exists('.env') || copy('.env.example', '.env'); echo '.env ready'.PHP_EOL;"

4) Run the test suite
   docker compose run --rm app composer test

5) Use the Laravel-like console
   docker compose run --rm app php bin/console list
   docker compose run --rm app php bin/console greet YourName

   # You can still run standalone scripts if you like:
   docker compose run --rm app php scripts/hello.php YourName

Environment variables (.env)
- This scaffold uses vlucas/phpdotenv to load environment variables from .env and .env.local automatically.
- The loader is wired via Composer autoload (src/bootstrap.php), so any script that includes vendor/autoload.php will get .env variables.
- To use:
  1) Copy .env.example to .env
  2) Edit variables as needed (you can override locally in .env.local)
  3) Run your scripts/tests as usual; variables are available via $_ENV / getenv()
- Example:
  Set GREETING=Hola in .env, then run:
    docker compose run --rm app php bin/console greet YourName
  Output:
    Hola, YourName!

Project layout
- bin/console: Console entry point (auto-discovers commands)
- src/Console/Commands/GreetCommand.php: Example console command
- src/Example.php: Example class
- tests/ExampleTest.php: PHPUnit tests for Example
- scripts/hello.php: Example standalone script (uses GREETING env var)
- public/index.php: Minimal HTTP front controller

Adding your own commands
- Create a new class under src/Console/Commands, e.g. src/Console/Commands/FooCommand.php
- Extend Symfony\Component\Console\Command\Command and either set protected static $defaultName = 'foo' or use #[AsCommand(name: 'foo')]
- Implement configure() and execute() to define arguments/options and behavior
- Commands are auto-discovered from App\\Console\\Commands. No Composer script aliases are required.
- Run it via:
  docker compose run --rm app php bin/console foo

Notes
- The container mounts the project directory, so changes on the host are reflected immediately.
- Autoloading: Run "composer dump-autoload" after adding new classes/namespaces.
- PHPUnit version targets ^11; adjust in composer.json if you need a different version.

Troubleshooting
- If you see permissions issues on generated files, you can adjust user IDs in the Dockerfile to match your host.
- If Composer is slow or has network issues, you might add cache volumes for COMPOSER_HOME.


HTTP server (built-in)
- Start the dev web server on port 8080 using the PHP built-in server:
  docker compose up web
- Then open your browser at:
  http://localhost:8080
- The document root is the public/ directory and the front controller is public/index.php.
- Environment variables from .env are available since the front controller loads vendor/autoload.php which triggers src/bootstrap.php.
- The container watches your mounted files, so changes are reflected on refresh.
