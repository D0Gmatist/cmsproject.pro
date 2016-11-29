<?php

namespace Modules\Plugins\Registration;

use Modules\Functions\Functions;

final class Registration {
	public $functions;

	function __construct ( Functions $functions ) {
		$this->functions = $functions;

	}
}