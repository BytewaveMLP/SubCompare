<?php

/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace SubCompare\Twig\Extensions;

class BootstrapActiveExtension extends \Twig_Extension {
	protected $request;

	public function __construct($request) {
		$this->request = $request;
	}

	public function getFunctions() {
		return [
			new \Twig_Function('active_route', [$this, 'isActiveRoute']),
			new \Twig_Function('active_route_one_of', [$this, 'isActiveRouteOneOf']),
		];
	}

	public function isActiveRoute(string $name) {
		$route = $this->request->getAttribute('route');

		if (!$route) return;

		if ($route->getName() == $name) {
			return 'active';
		}
	}

	public function isActiveRouteOneOf(array $names) {
		foreach ($names as $name) {
			if ($this->isActiveRoute($name)) {
				return 'active';
			}
		}
	}
}
