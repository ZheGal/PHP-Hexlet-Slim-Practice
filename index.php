<?php

// Подключение автозагрузки через composer
require __DIR__ . '/vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;

$users = ['mike', 'mishel', 'adel', 'keks', 'kamila'];

$container = new Container();
$container->set('renderer', function () {
    // Параметром передается базовая директория в которой будут храниться шаблоны
    return new \Slim\Views\PhpRenderer(__DIR__ . '/templates');
});
$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$app->get('/', function ($request, $response) {
    return $response->write('Welcome to Slim!');
});
$app->get('/users', function ($request, $response) use ($users) {
    $search = $request->getQueryParam('search');
    $params = ['users' => $users];
    if ($search) {
        $find = [];
        foreach ($users as $user) {
            if (strpos($user, $search) !== false) {
                $find[] = htmlspecialchars($user);
            }
        }
        $params = ['users' => $find];
    }
    return $this->get('renderer')->render($response, 'users/index.phtml', $params);
});
$app->get('/users/{id}', function ($request, $response, $args) {
    $params = ['id' => $args['id'], 'nickname' => 'user-' . $args['id']];
    return $this->get('renderer')->render($response, 'users/show.phtml', $params);
});
$app->post('/users', function ($request, $response) {
    return $response->withStatus(302);
});
$app->get('/courses/{id}', function ($request, $response, array $args) {
    $id = $args['id'];
    return $response->write("Course id: {$id}");
});

$app->run();