# Project Setup
Follow these steps to set up and run the project:

## 1. Install Dependencies

Update the project dependencies:
composer update

Install Node dependencies:
npm install

2. Compile Assets
npm run build

3. Install and Publish Telescope & Pulse

Install Laravel Telescope:
php artisan telescope:install

Publish the Laravel Pulse configuration:
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"

4. Storage and Database Setup
Create a symbolic link for storage:
php artisan storage:link

5. Install the latest version of ChromeDriver for your OS:
php artisan dusk:chrome-driver

6. Run migrations:
php artisan migrate

7. Run the Pint command below to fix code style issues using the Pint binary located in your project's vendor/bin directory:
./vendor/bin/pint

8. Run the PHPStan command below, review the output, and if any errors appear, manually fix them in your code
vendor/bin/phpstan analyse

9. Run the Enlightn command below, review the output, and if any errors appear, manually fix them in your code
php artisan enlightn

10. Kindly review all seeders before running the database seeding process:
php artisan db:seed

11. Please check the permissions in the portal from your end and execute the seeder command.

12. Environment Variables
Update the .env file with the following keys:
GOOGLE_RECAPTCHA_KEY=
GOOGLE_RECAPTCHA_SECRET=
TINIFY_API_KEY=

13. Generate Passport Key
php artisan passport:client --password
php artisan passport:client --personal

14. Run Browser Automation Tests (Laravel Dusk)
Run the Laravel Dusk command below to execute browser-based (end-to-end) automation tests:
php artisan dusk

----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# If you’re running the project using Docker Desktop, please follow the steps below:

1. If Docker Desktop is not installed on your Windows system, download it from the link below:
https://docs.docker.com/desktop/setup/install/windows-install

2. Open a terminal inside the project directory and run the following command:
docker-compose up -d --build

3. Open Docker Desktop and check the following tabs to verify everything is running properly:
  - Containers
  - Images
  - Builds

4. Visit the app in browser → http://localhost:8000
5. Visit phpMyAdmin         → http://localhost:8080

-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------

# If you're logging in with a mobile number, please follow the steps below:

php artisan db:seed --class=SmsTemplateSeeder

Environment Variables =
Update the .env file with the following keys:
SMS_API_KEY=
SMS_SENDER_ID=
SMS_URL=


