<?php

/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

$container['view'] = function($container) {
	$view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
		'cache' => getenv('VIEW_CACHE_DIR'),
		'debug' => getenv('DEBUG') == 'true',
	]);

	$twig = $view->getEnvironment();

	$view->addExtension(new Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
	));

	$view->addExtension(new Twig_Extension_Debug());

	$view->addExtension(new SubCompare\Twig\Extensions\HtmlEntitiesExtension());

	$view->addExtension(new Twig_Extensions_Extension_Text());

	$twig->addGlobal('site', [
		'theme' => getenv('BOOTSTRAP_THEME')
	]);

	$view->getEnvironment()->addGlobal('flash', $container->flash);
	
	return $view;
};

$container['cache'] = function($container) {
	$filesystemAdapter = new \League\Flysystem\Adapter\Local(getenv('DATA_CACHE_DIR'));
	$filesystem        = new \League\Flysystem\Filesystem($filesystemAdapter);

	return new Cache\Adapter\Filesystem\FilesystemCachePool($filesystem);
};

$container['csrf'] = function ($container) {
	return new \Slim\Csrf\Guard;
};

$container['flash'] = function ($container) {
	return new \Slim\Flash\Messages;
};
