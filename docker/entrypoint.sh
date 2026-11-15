#!/bin/bash
# ==============================================================================
# DeadlineFlow - Docker Entrypoint Script
# ==============================================================================
# This script runs before the main container command (e.g., php-fpm).
# It handles two critical tasks:
#   1. Reading Docker Secrets from files (for production environments).
#   2. Fixing file permissions for the non-root user.
# ==============================================================================

# Exit immediately if any command fails
set -e

# ==========================================
# 1. Helper Function: File Environment Loader
# ==========================================
# This function checks if a variable has a '_FILE' suffix (e.g., DB_PASSWORD_FILE).
# If it exists, it reads the file content and exports it as a normal variable (DB_PASSWORD).
# This is the standard way to use Docker Secrets in PHP applications.
file_env() {
    local var="$1"
    local fileVar="${var}_FILE"
    local def="${2:-}"

    # If the _FILE variable exists and points to a valid file, read it.    
    if [ -f "${!fileVar:-}" ]; then
        export "$var"="$(cat "${!fileVar}")"
    # Otherwise, if the direct variable exists, use it (for local development).
    elif [ -n "${!var:-}" ]; then
        export "$var"="${!var}"
    # Otherwise, use the default value if provided.
    elif [ -n "$def" ]; then
        export "$var"="$def"
    fi

    # Remove the _FILE variable to prevent leaking secret paths.
    unset "$fileVar"
}

# ==========================================
# 2. Load Secrets into Environment Variables
# ==========================================
# These calls read the secrets and make them available to Laravel via env().
file_env 'DB_PASSWORD'
file_env 'REDIS_PASSWORD'
file_env 'RABBITMQ_PASSWORD'
file_env 'HORIZON_PASSWORD'
file_env 'APP_KEY'

# ==========================================
# 3. Fix File Permissions
# ==========================================
# Because we run as a non-root user (appuser), we need to ensure it has
# write access to the storage and cache directories (mounted from the host).
if [ -d "/var/www/html/storage" ]; then
    chown -R appuser:appuser /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public 2>/dev/null || true
fi

# ==========================================
# 4. Execute the Main Command
# ==========================================
# Replace the current shell with the main container command (e.g., php-fpm).
# Using 'exec' ensures proper signal handling (e.g., graceful shutdown).
exec "$@"