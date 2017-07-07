<?php

/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace SubCompare\Middleware;

class ActiveRouteMiddleware extends Middleware {
	public function __invoke($request, $response, $next) {
		$this->container->view->addExtension(new \SubCompare\Twig\Extensions\BootstrapActiveExtension($request));
		
		$response = $next($request, $response);
		return $response;
	}
}
