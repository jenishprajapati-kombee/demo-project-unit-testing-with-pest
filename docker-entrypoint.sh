#!/bin/sh
set -e

# Wait for database to be ready
echo "Waiting for database..."
while ! nc -z mysql 3306; do
  sleep 1
done
echo "Database is ready!"

# Install PHP dependencies if vendor is missing or needs update
if [ ! -d "vendor" ]; then
    echo "Vendor directory missing. Installing dependencies..."
    composer install --no-interaction --prefer-dist
fi

# Telescope and Pulse setup
echo "Setting up Telescope and Pulse..."
php artisan telescope:install --no-interaction || true
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider" --no-interaction --force || true

# Run migrations and seeders
echo "Running migrations..."
php artisan migrate --force

echo "Running seeders..."
# Check if we should run default seeders
php artisan db:seed --force

# Install Node dependencies if node_modules is missing
if [ ! -d "node_modules" ]; then
    echo "node_modules directory missing. Installing dependencies..."
    npm install
fi

# Compile assets
echo "Compiling assets..."
npm run build || echo "Build failed, but continuing..."

# Storage link
echo "Creating storage link..."
php artisan storage:link --no-interaction || true

# Dusk Chrome Driver
echo "Installing ChromeDriver..."
php artisan dusk:chrome-driver --no-interaction || true

# Passport Keys
echo "Generating Passport client keys..."
php artisan passport:client --password --no-interaction || true
php artisan passport:client --personal --no-interaction || true

# Execute CMD
echo "Starting PHP-FPM..."
exec php-fpm
