<?php

declare(strict_types=1);

use Slim\Routing\RouteCollectorProxy;

use App\Controller\{
    User,
    Dashboard,
    MasterData,
    ManageUser,
    Task,
    Report
};

/* ------------------------------ General Routes ------------------------------ */

// Start Route
$app->get('/', 'App\Controller\Hello:getStatusAPI')->setName('main');

// Authentication Routes
$app->post('/auth/signin', 'App\Controller\Auth:signin')->setName('signin');

// Users Routes
$app->group('/user', function (RouteCollectorProxy $group) {
    $group->get('/info', [User::class, 'info']);
    $group->get('/profile', [User::class, 'profile']);
    $group->post('/profile/save', [User::class, 'save']);
    $group->post('/profile/update', [User::class, 'updateProfile']);
    $group->post('/profile/change_password', [User::class, 'change_password']);
    $group->post('/change-password', [User::class, 'changePassword']);
});

function routes($app, $url, $controller): void {
    $app->group("/$url", function (RouteCollectorProxy $group) use($controller) {
        $group->get('', [$controller, 'index']);
        $group->post('/save', [$controller, 'save']);
        $group->get('/{id}', [$controller, 'detail']);
        $group->delete('/bulk-delete', [$controller, 'bulkDelete']);
        $group->delete('[/{id}]', [$controller, 'delete']);
    });
}

// Diagnostic Data
$app->get('/diagnostic', 'App\Controller\Hello:getDiagnostic')->setName('diagnostic');

// Test Connect Database
$app->get('/testgetdata', 'App\Controller\Hello:testConnectFetchData');
$app->get('/test-notification', 'App\Controller\Hello:testNotification');
$app->get('/test-env', 'App\Controller\Hello:testEnv');

$app->group('/master-data', function (RouteCollectorProxy $group) {
    $group->get('/jabatan', [MasterData::class, 'listJabatan']);
    $group->get('/list-user', [MasterData::class, 'listUSer']);
});
/* --------------------------------------------------------------------------- */

/* ------------------------------ Super Admin Routes ------------------------------ */
$app->post('/manage-user/show-password', [ManageUser::class, 'showPassword']);
$app->post('/manage-user/change-password', [ManageUser::class, 'changePassword']);
routes($app, 'manage-user', ManageUser::class);

$app->get('/dashboard/statistic', [Dashboard::class, 'statistic']);

$app->get('/task/statistic', [Task::class, 'statistic']);
$app->get('/task/discussion', [Task::class, 'discussion']);
$app->post('/task/save-discussion', [Task::class, 'saveDiscussion']);
$app->post('/task/update-status', [Task::class, 'updateStatus']);
routes($app, 'task', Task::class);

$app->get('/report', [Report::class, 'index']);
$app->get('/report/download', [Report::class, 'download']);