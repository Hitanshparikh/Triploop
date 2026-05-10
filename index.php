<?php
/**
 * JourneyOS AI — Entry Point
 */
require_once __DIR__ . '/includes/functions.php';

// If logged in, go to dashboard; otherwise show landing
if (isLoggedIn()) {
    redirect('/pages/dashboard.php');
} else {
    redirect('/pages/landing.php');
}
