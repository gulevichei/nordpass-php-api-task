.PHONY: start stop init tests

start:
	docker-compose up -d

stop:
	docker-compose stop

init:
	docker-compose build
	docker-compose up -d
	docker-compose exec php composer install
	docker-compose exec php php bin/console doctrine:database:create --if-not-exists
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction
	docker-compose exec php php bin/console doctrine:database:create --env=test --if-not-exists
	docker-compose exec php php bin/console doctrine:migrations:migrate --env=test --no-interaction
	docker-compose exec php php bin/console doctrine:fixtures:load --env=test --no-interaction

tests:
	docker-compose exec php php vendor/bin/phpunit -c phpunit.xml.dist
