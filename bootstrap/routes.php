<?php

/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

$app->get('/', 'HomeController:index')->setName('home');
$app->get('/about', 'HomeController:about')->setName('about');
$app->get('/compare', 'CompareController:compare')->setName('compare');
