<?php
/**
 * Database Initialization Script
 * Run this once to set up the database tables
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Admin.php';

try {
    echo "Initializing database...\n";
    
    $admin = new Admin();
    $admin->initializeTables();
    $admin->createInitialAdmin();
    
    echo "Database initialization completed successfully!\n";
    echo "Default admin user created: admin / admin123\n";
    
} catch (Exception $e) {
    echo "Error initializing database: " . $e->getMessage() . "\n";
}
?>