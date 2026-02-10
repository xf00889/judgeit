#!/usr/bin/env php
<?php

/**
 * User Creation Script
 * 
 * Usage: php create_user.php
 * 
 * Interactive script to create user accounts for the judging platform.
 * Choose from predefined user types or create custom users.
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Colors for console output
class Console {
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const MAGENTA = "\033[35m";
    const CYAN = "\033[36m";
    const WHITE = "\033[37m";
    const RESET = "\033[0m";
    const BOLD = "\033[1m";
}

function colorize($text, $color) {
    return $color . $text . Console::RESET;
}

function prompt($message, $default = null) {
    if ($default) {
        echo $message . " [" . colorize($default, Console::CYAN) . "]: ";
    } else {
        echo $message . ": ";
    }
    
    $input = trim(fgets(STDIN));
    return empty($input) && $default ? $default : $input;
}

function promptPassword($message) {
    echo $message . ": ";
    
    // Hide password input on Unix systems
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        system('stty -echo');
        $password = trim(fgets(STDIN));
        system('stty echo');
        echo "\n";
    } else {
        // Windows fallback - password will be visible
        $password = trim(fgets(STDIN));
    }
    
    return $password;
}

function createUser($name, $email, $role, $password) {
    // Check if email already exists
    if (\App\Models\User::where('email', $email)->exists()) {
        echo colorize("Error: Email '$email' already exists in the system.\n", Console::RED);
        return false;
    }
    
    try {
        $user = \App\Models\User::create([
            'name' => $name,
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => $role,
            'email_verified_at' => now(),
        ]);
        
        echo colorize("✓ User created successfully!\n", Console::GREEN);
        echo "\n" . colorize("Login Details:", Console::BOLD) . "\n";
        echo "Name: " . colorize($name, Console::CYAN) . "\n";
        echo "Email: " . colorize($email, Console::CYAN) . "\n";
        echo "Role: " . colorize(ucfirst($role), Console::CYAN) . "\n";
        echo "User ID: " . colorize($user->id, Console::CYAN) . "\n";
        
        return true;
    } catch (\Exception $e) {
        echo colorize("Error creating user: " . $e->getMessage() . "\n", Console::RED);
        return false;
    }
}

function showMainMenu() {
    echo "\n" . colorize("=== User Creation Menu ===", Console::BOLD . Console::MAGENTA) . "\n";
    echo "Choose what type of user to create:\n\n";
    
    echo "1. " . colorize("Quick Setup Users", Console::YELLOW . Console::BOLD) . "\n";
    echo "   - Create standard test users with default credentials\n\n";
    
    echo "2. " . colorize("Admin User", Console::RED) . " - Full system access\n";
    echo "   - Manage categories, contestants, judges\n";
    echo "   - Configure overall rankings\n\n";
    
    echo "3. " . colorize("Judge User", Console::GREEN) . " - Score contestants\n";
    echo "   - Access judging interface\n";
    echo "   - Submit scores for assigned categories\n\n";
    
    echo "4. " . colorize("Tabulator User", Console::BLUE) . " - View results\n";
    echo "   - Monitor judging progress\n";
    echo "   - Generate rankings and awards\n\n";
    
    echo "5. " . colorize("Exit", Console::WHITE) . "\n";
}

function createQuickSetup() {
    echo "\n" . colorize("=== Quick Setup ===", Console::BOLD . Console::YELLOW) . "\n";
    echo "This will create standard test users with default passwords.\n";
    echo "You can change passwords after creation.\n\n";
    
    $users = [
        ['Admin User', 'admin@test.local', 'admin'],
        ['Judge One', 'judge1@test.local', 'judge'],
        ['Judge Two', 'judge2@test.local', 'judge'],
        ['Tabulator User', 'tabulator@test.local', 'tabulator'],
    ];
    
    $confirm = prompt("Create all 4 test users? (y/n)", "y");
    if (strtolower($confirm) !== 'y' && strtolower($confirm) !== 'yes') {
        echo colorize("Quick setup cancelled.\n", Console::YELLOW);
        return;
    }
    
    echo "\n" . colorize("Creating users...", Console::YELLOW) . "\n";
    
    $created = 0;
    foreach ($users as $userData) {
        list($name, $email, $role) = $userData;
        echo "Creating $name ($role)... ";
        
        if (createUser($name, $email, $role, 'password')) {
            $created++;
        }
    }
    
    echo "\n" . colorize("Quick setup completed! Created $created users.", Console::GREEN . Console::BOLD) . "\n";
    echo colorize("Default password for all users: password", Console::CYAN) . "\n";
}

function createCustomUser($role) {
    $roleNames = [
        'admin' => 'Admin',
        'judge' => 'Judge', 
        'tabulator' => 'Tabulator'
    ];
    
    echo "\n" . colorize("=== Create {$roleNames[$role]} User ===", Console::BOLD) . "\n";
    
    while (true) {
        $name = prompt("Full Name");
        if (!empty($name)) break;
        echo colorize("Name cannot be empty.\n", Console::RED);
    }
    
    while (true) {
        $email = prompt("Email Address");
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (!\App\Models\User::where('email', $email)->exists()) {
                break;
            } else {
                echo colorize("Email already exists. Please use a different email.\n", Console::RED);
            }
        } else {
            echo colorize("Invalid email format.\n", Console::RED);
        }
    }
    
    while (true) {
        $password = promptPassword("Password (min 8 characters)");
        if (strlen($password) < 8) {
            echo colorize("Password must be at least 8 characters long.\n", Console::RED);
            continue;
        }
        
        $confirmPassword = promptPassword("Confirm Password");
        if ($password !== $confirmPassword) {
            echo colorize("Passwords do not match. Please try again.\n", Console::RED);
            continue;
        }
        
        break; // Passwords match, exit the loop
    }
    
    // Confirmation
    echo "\n" . colorize("User Details:", Console::BOLD) . "\n";
    echo "Name: " . colorize($name, Console::CYAN) . "\n";
    echo "Email: " . colorize($email, Console::CYAN) . "\n";
    echo "Role: " . colorize($roleNames[$role], Console::CYAN) . "\n";
    
    $confirm = prompt("\nCreate this user? (y/n)", "y");
    if (strtolower($confirm) !== 'y' && strtolower($confirm) !== 'yes') {
        echo colorize("User creation cancelled.\n", Console::YELLOW);
        return;
    }
    
    createUser($name, $email, $role, $password);
}

// Main script
try {
    echo colorize("=== Judging Platform User Creation ===\n", Console::BOLD . Console::MAGENTA);
    
    while (true) {
        showMainMenu();
        
        $choice = prompt("\nSelect option (1-5)", "1");
        
        switch ($choice) {
            case '1':
                createQuickSetup();
                break;
            case '2':
                createCustomUser('admin');
                break;
            case '3':
                createCustomUser('judge');
                break;
            case '4':
                createCustomUser('tabulator');
                break;
            case '5':
                echo colorize("Goodbye!\n", Console::GREEN);
                exit(0);
            default:
                echo colorize("Invalid choice. Please select 1-5.\n", Console::RED);
                continue;
        }
        
        $continue = prompt("\nCreate another user? (y/n)", "n");
        if (strtolower($continue) !== 'y' && strtolower($continue) !== 'yes') {
            echo colorize("User creation completed!\n", Console::GREEN . Console::BOLD);
            break;
        }
    }
    
} catch (\Exception $e) {
    echo "\n" . colorize("Error: " . $e->getMessage(), Console::RED) . "\n";
    exit(1);
}