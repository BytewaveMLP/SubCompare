<?php

/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

$app->add(new \SubCompare\Middleware\ActiveRouteMiddleware($container));
$app->add(new \SubCompare\Middleware\OldInputMiddleware($container));
