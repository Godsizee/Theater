<?php
// config/routes.php

// Controller-Klassen importieren
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaController;

return [
    // --- Controller-basierte Routen ---
    '#^$#' => [HomeController::class, 'show', 'GET'],
    '#^home$#' => [HomeController::class, 'show', 'GET'],

    '#^select$#' => [MediaController::class, 'showMovies', 'GET'],
    '#^series$#' => [MediaController::class, 'showSeries', 'GET'],
    '#^genres$#' => [MediaController::class, 'showGenres', 'GET'],
    '#^random$#' => [MediaController::class, 'showRandom', 'GET'],

    '#^admin/settings$#' => [SettingsController::class, 'show', 'GET'],
    '#^admin/settings/update$#' => [SettingsController::class, 'update', 'POST'],

    '#^admin/users$#' => [UserController::class, 'index', 'GET'],
    '#^admin/create_user$#' => [UserController::class, 'create', 'GET'],
    '#^admin/create_user/store$#' => [UserController::class, 'store', 'POST'],
    '#^admin/edit_user/(\d+)$#' => [UserController::class, 'edit', 'GET'],
    '#^admin/edit_user/(\d+)/update$#' => [UserController::class, 'update', 'POST'],

    '#^admin/insert$#' => [AdminMediaController::class, 'create', 'GET'],
    '#^admin/insert/store$#' => [AdminMediaController::class, 'store', 'POST'],
    '#^admin/media_overview$#' => [AdminMediaController::class, 'index', 'GET'],
    '#^admin/update/(movie|series)/(\d+)$#' => [AdminMediaController::class, 'edit', 'GET'],
    '#^admin/update/(movie|series)/(\d+)/update$#' => [AdminMediaController::class, 'update', 'POST'],
    '#^admin/delete/(movie|series)/(\d+)$#' => [AdminMediaController::class, 'confirmDelete', 'GET'],
    '#^admin/delete/(movie|series)/(\d+)/destroy$#' => [AdminMediaController::class, 'destroy', 'POST'],
    
    '#^profil$#' => [ProfileController::class, 'showProfile', 'GET'],
    '#^bestellungen$#' => [ProfileController::class, 'showOrders', 'GET'],
    '#^bestellung/(\d+)$#' => [ProfileController::class, 'showOrderDetails', 'GET'],
    '#^rechnungen$#' => [ProfileController::class, 'showInvoices', 'GET'],
    '#^profil_daten$#' => [ProfileController::class, 'showProfileData', 'GET'],
    '#^profil_daten/update$#' => [ProfileController::class, 'handleProfileDataUpdate', 'POST'],

    '#^login$#' => [AuthController::class, 'showLogin', 'GET'],
    '#^login/process$#' => [AuthController::class, 'handleLogin', 'POST'],
    '#^registry$#' => [AuthController::class, 'showRegistry', 'GET'],
    '#^registry/process$#' => [AuthController::class, 'handleRegistry', 'POST'],

    '#^support$#' => [SupportController::class, 'index', 'GET'],
    '#^support/kunden$#' => [SupportController::class, 'listCustomers', 'GET'],
    '#^support/kundendetails/(\d+)$#' => [SupportController::class, 'showCustomerDetails', 'GET'],
    '#^support/kundendaten_bearbeiten/(\d+)$#' => [SupportController::class, 'editCustomerData', 'GET'],
    '#^support/kundendaten_bearbeiten/(\d+)/update$#' => [SupportController::class, 'updateCustomerData', 'POST'],
    '#^support/kundenbestellungen/(\d+)$#' => [SupportController::class, 'showCustomerOrders', 'GET'],
    '#^support/bestellungsdetails_ansicht/(\d+)$#' => [SupportController::class, 'showOrderDetails', 'GET'],
    '#^support/bestellung_bearbeiten/(\d+)$#' => [SupportController::class, 'editOrder', 'GET'],
    '#^support/bestellung_bearbeiten/(\d+)/update$#' => [SupportController::class, 'updateOrder', 'POST'],
    '#^support/stornieren\.php$#' => 'pages/support/stornieren.php',

    '#^cart$#' => [ShopController::class, 'showCart', 'GET'],
    '#^checkout$#' => [ShopController::class, 'showCheckout', 'GET'],
    '#^merchandise$#' => [ShopController::class, 'showMerchandise', 'GET'],

    '#^kontakt$#' => [StaticPageController::class, 'showKontakt', 'GET'],
    '#^kontakt/send$#' => [StaticPageController::class, 'handleKontakt', 'POST'],
    '#^(agb|impressum|datenschutz|hilfe|ueber-uns)$#' => [StaticPageController::class, 'show', 'GET'],

    // --- Verbleibende Datei-basierte Routen ---
    '#^movie/([a-z0-9-]+)$#' => 'pages/media/movie_details.php',
    '#^series/([a-z0-9-]+)$#' => 'pages/media/series_details.php',

    '#^rechnung_pdf.php$#' => 'pages/profile/rechnung_pdf.php',

    '#^admin$#' => 'pages/admin/index.php',
    '#^admin/logs$#' => 'pages/admin/logs.php',
    '#^admin/logs/export$#' => 'pages/admin/log_export.php',
];
