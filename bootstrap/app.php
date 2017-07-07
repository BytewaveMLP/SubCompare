<?php

/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

$app = new \Slim\App([
	'settings' => [
		'displayErrorDetails'               => getenv('DEBUG') == 'true',
		'debug'                             => getenv('DEBUG') == 'true',
		'routerCacheFile'                   => getenv('ROUTER_CACHE_FILE'),
		'determineRouteBeforeAppMiddleware' => true,
		'whoops.editor'                     => 'sublime',
	],
]);

$container = $app->getContainer();

require __DIR__ . '/errorhandlers.php';
require __DIR__ . '/container.php';
require __DIR__ . '/controllers.php';
require __DIR__ . '/middleware.php';

require __DIR__ . '/routes.php';
