## Deploy 
```
chown www-data:www-data storage -R
composer install
php artisan key:generate
php artisan jwt:secret
php artisan init
php artisan storage:link
```

##Important
- fill right APP_URL

### Bot testing with ngrock
- `docker-compose -f docker-compose.yml -f docker-compose.ngrock.yml up -d`
- go to http://demo.local:4551(or another domain)
- get ngrock https url and put it in APP_URL
- fill TELEGRAM_BOT_TEST_TOKEN
- `php artisan init --reset=true --seed=true`

##### For MainBot testing
- fill TELEGRAM_MAIN_BOT_TEST_TOKEN and TELEGRAM_MAIN_BOT_TEST_SELLER_CREATE_PASSWORD (bcrypt)

##### It will create the seller with domain from APP_URL and telegram bot with token from TELEGRAM_BOT_TEST_TOKEN
- when you end work with ngrock - `docker-compose -f docker-compose.yml -f docker-compose.ngrock.yml down`

### Commands
```
admin:create
```

### After changing models
```
php artisan ide-helper:models
```
