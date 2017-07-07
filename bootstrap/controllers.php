<?php

/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

$container['HomeController'] = function($container) {
	return new SubCompare\Controllers\HomeController($container);
};

$container['CompareController'] = function($container) {
	return new SubCompare\Controllers\CompareController($container);
};
