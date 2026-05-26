<?php

// Redirect user
function redirect(string $url, string $flashType = '', string $flashMessage = '')
{
    if ($flashType && $flashMessage) {
        setFlash($flashType, $flashMessage);
    }
    header("Location: {$url}");
    exit;
}

// Helper functions for checking login and admin role

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('../pages/login.php');
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        redirect('../pages/dashboard.php');
    }
}

// Create flash messages for displaying errors or success
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

// Render the flash message with correct styling
function renderFlash(string $classes = ''): void
{
    if (!isset($_SESSION['flash'])) return;

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    $styles = [
        'success' => '!bg-custom-teal',
        'error' => '!bg-custom-red',
        'info' => '!bg-custom-bg',
    ];

    $icons = [
        'success' => 'fa-circle-check',
        'error' => 'fa-triangle-exclamation',
        'info' => 'fa-circle-info',
    ];

    $style = $styles[$flash['type']] ?? $styles['info'];
    $icon = $icons[$flash['type']] ?? $icons['info'];
    $message = htmlspecialchars($flash['message']);

    echo "<div class='{$style} {$classes} custom-card !px-4 !py-2.5 font-mono text-sm font-bold mb-4 flex items-center justify-between gap-4'>
            <div class='flex items-center'>
                <i class='fa-solid {$icon} mr-2'></i>
                <span>{$message}</span>
            </div>
            <button onclick='this.parentElement.remove()' class='w-8 h-8 !p-0.5 custom-btn'>
                <i class='fa-solid fa-xmark'></i>
            </button>
        </div>";
}

// ----- Handle translations -----

// Set the language
if (isset($_GET['lang'])) {
    $selected_lang = trim($_GET['lang']);
    if (in_array($selected_lang, ['en', 'lv'])) {
        $_SESSION['lang'] = $selected_lang;
    }
}

// Get language from session
$current_lang = $_SESSION['lang'] ?? 'en';

// Load the correct language file
$translation = [];
$lang_file_location = __DIR__ . "/../lang/{$current_lang}.php";
if (file_exists($lang_file_location)) {
    $translation = require $lang_file_location;
}

// Load correct translation
function __(string $key): string
{
    global $translation;
    return $translation[$key] ?? $key;
}

// Translate database status strings
function translateStatus(string $status): string
{
    $map = [
        'waitlist' => __('status_waitlist'),
        'playing' => __('status_playing'),
        'finished' => __('status_finished'),
        'quit' => __('status_quit'),
    ];

    return $map[$status] ?? $status;
}
