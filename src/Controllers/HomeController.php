<?php

/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace SubCompare\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class HomeController extends Controller {
	public function index(Request $request, Response $response) {
		return $this->container->view->render($response, 'pages/home.twig');
	}

	public function about(Request $request, Response $response) {
		return $this->container->view->render($response, 'pages/about.twig');
	}
}
