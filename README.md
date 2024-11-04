## Getting started

Laravel money object

### Creds
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root
```

## Running the application
```
docker exec -it app bash
composer install
php artisan key:generate
php artisan migrate
```