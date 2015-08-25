# Code Base of General-purposed Restful Api Service (written in Laravel5)
Make you quickly build a restful API service. This project is still work in process.

## Features
    1. User registration, authentication (incl. Social), and password reset
    2. Role based access control
    3. Json web token
    4. Request throttle
    5. Api response transformation and serialization
    6. Localization (English/Korean)
    7. Resource ID obfuscation

## Steps to install
**Step 1** Clone the repository.

```bash
git clone git@github.com:appkr/rest.git
```

**Step 2** Install composer packages.

```bash
cd rest && composer install
```

**Step 3** Database
Make a database named `rest`, migrate, and seed.

```bash
php artisan migrate --seed
```

**Step 4** Download and build the front-end resources.

```bash
npm install && gulp
```

**Step 5** Boot up a test server and open at a browser

```bash
php artisan serve && open http://localhost:8000/auth/login
```

## Example
- See `app/Http/routes.php` to check which endpoints are available.
- See `app/Http/Controllers/TodoController` to learn how to use this code base.

## Test
This project contains an integration test. The integration test requires mailcatcher ruby gem, and you can install it from [http://mailcatcher.me/](http://mailcatcher.me/).

```bash
phpunit
```