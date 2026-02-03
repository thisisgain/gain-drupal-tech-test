#!/bin/bash

# GAIN Drupal Tech Test - Quick Setup Script
# This script helps you get the Drupal site running quickly

echo "🚀 Setting up GAIN Drupal Tech Test..."

# Check if DDEV is available
if command -v ddev &> /dev/null; then
    echo "📦 DDEV detected - using DDEV setup"
    
    # Start DDEV
    ddev start
    
    # Install SSL certificate
    mkcert -install 2>/dev/null || true
    
    # Install Composer dependencies
    ddev composer install
    
    # Install Drupal
    ddev drush site:install standard --db-url=mysql://db:db@db/db --site-name="GAIN Drupal Tech Test" --account-name=admin --account-pass=admin -y
    
    # Enable the custom module
    ddev drush en gain_drupal_tech_test -y
    
    # Clear cache
    ddev drush cr
    
    echo "✅ Setup complete!"
    echo "🌐 Site URL: https://gain-drupal-tech-test.ddev.site:8443"
    echo "👤 Admin login: admin / admin"
    
elif command -v docker-compose &> /dev/null; then
    echo "🐳 Docker Compose detected - using Docker setup"
    
    # Install Composer dependencies locally first
    composer install
    
    # Start Docker containers
    docker-compose up -d
    
    echo "⏳ Waiting for database to be ready..."
    sleep 30
    
    # Install Drupal (you'll need to do this manually via browser)
    echo "✅ Docker containers started!"
    echo "🌐 Site URL: http://localhost:8080"
    echo "📝 Complete Drupal installation via the web interface"
    echo "   Database: drupal / drupal @ db:3306"
    
else
    echo "⚠️  Neither DDEV nor Docker Compose found"
    echo "📋 Manual setup required:"
    echo "   1. composer install"
    echo "   2. Set up web server pointing to web/"
    echo "   3. Create database and configure settings.php"
    echo "   4. Run Drupal installation"
    echo "   5. Enable gain_drupal_tech_test module"
fi

echo ""
echo "📚 Next steps:"
echo "   1. Check that the site loads"
echo "   2. Look for the Recent Articles block (it has bugs!)"
echo "   3. Start working on the tasks in README.md"
echo ""
echo "🎯 Good luck with the tech test!"