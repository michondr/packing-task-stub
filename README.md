task: https://docs.google.com/document/d/1QbXgRr4T8-9GjjpvpotzzlWodhz8N50HTFwtCwHh-TI/edit

### init
- `printf "UID=$(id -u)\nGID=$(id -g)" > .env`
- `docker-compose up -d`
- `docker-compose run shipmonk-packing-app bash`
- `composer install && vendor/bin/doctrine orm:schema-tool:create && vendor/bin/doctrine dbal:run-sql "$(cat data/packaging-data.sql)"`

### run
- `php run.php "$(cat sample.json)"`

### adminer
- Open `http://localhost:8080/?server=mysql&username=root&db=packing`
- Password: secret
