<?php
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
        header('Location: ../pages/login.php');
        exit;
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: ../pages/dashboard.php');
        exit;
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
