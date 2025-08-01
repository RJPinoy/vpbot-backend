#!/bin/bash
set -e

# Decode JWT keys if needed
echo "🔐 Decoding JWT keys..."

# Ensure directory exists
mkdir -p config/jwt

# Handle edge case: file is wrongly a directory
[ -d config/jwt/private.pem ] && echo "❌ 'private.pem' is a directory. Removing it..." && rm -rf config/jwt/private.pem
[ -d config/jwt/public.pem ] && echo "❌ 'public.pem' is a directory. Removing it..." && rm -rf config/jwt/public.pem

# Decode keys only if env vars are provided
if [ -n "$JWT_PRIVATE_KEY_B64" ]; then
  echo "$JWT_PRIVATE_KEY_B64" | base64 -d > config/jwt/private.pem && echo "✅ Private key decoded."
else
  echo "⚠️ JWT_PRIVATE_KEY_B64 not set. Skipping private key."
fi

if [ -n "$JWT_PUBLIC_KEY_B64" ]; then
  echo "$JWT_PUBLIC_KEY_B64" | base64 -d > config/jwt/public.pem && echo "✅ Public key decoded."
else
  echo "⚠️ JWT_PUBLIC_KEY_B64 not set. Skipping public key."
fi

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

# Create public chatbot if it doesn't exist
echo "Creating public chatbot (if not already exists)..."
php bin/console app:create-public_chatbot

# Create fixtures if LOAD_FIXTURES in CI pipeline or .env is true
echo "🔍 Checking if fixtures should be loaded..."
echo "🧪 APP_ENV: ${APP_ENV:-undefined}"
echo "📦 LOAD_FIXTURES: ${LOAD_FIXTURES:-undefined}"

if [[ "$APP_ENV" == "dev" || "$LOAD_FIXTURES" == "true" ]]; then
  echo "✅ Conditions met. Loading fixtures..."
  php bin/console doctrine:fixtures:load --append --no-interaction
else
  echo "⏩ Skipping fixture loading (conditions not met)."
fi

# Start Apache
echo "Starting Apache..."
exec apache2-foreground