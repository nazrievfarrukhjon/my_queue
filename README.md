
## Project Installation Rule

****


```bash 
git clone git@github.com:alibokiev/queue-system.git
```
```bash
cd queue-system

git checkout dev
```
```bash
composer install
```
If something doesn't work contact the developer. Next:
```bash
cp .env.dev .env
```
```bash
./vendor/bin/sail up -d
```
If you run app in prod, set up the .env yourself. Next:
```bash
sail artisan key:generate
```
```bash
sail artisan migrate
```
```bash
sail artisan db:seed
```

The application will be available at address 127.0.0.1:80 (localhost). 
> **! It's important that port 80 and 3306 for mysql should not be busy**

***

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
