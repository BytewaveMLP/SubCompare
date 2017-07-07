<?php

/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace SubCompare\Twig\Extensions;

class HtmlEntitiesExtension extends \Twig_Extension {
	public function getFilters() {
		return [
			new \Twig_Filter('unescape', [$this, 'unescape']),
		];
	}

	public function unescape($text) {
		return html_entity_decode($text, ENT_HTML5);
	}
}
