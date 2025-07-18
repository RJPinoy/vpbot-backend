#!/bin/bash
set -e

# Wait for the database to be ready
echo "Waiting for database to be ready..."
until php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; do
  sleep 2
done

# Run database migrations
echo "Running Doctrine migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Create superadmin user if it doesn't exist
echo "Creating superadmin (if not already exists)..."
php bin/console app:create-superadmin "$ADMIN_EMAIL" "$ADMIN_PASSWORD"

# Start Apache
echo "Starting Apache..."
exec apache2-foreground