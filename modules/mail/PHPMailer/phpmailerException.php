<?php

namespace Modules\Mail\PHPMailer;

use Exception;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

/**
 * PHPMailer exception handler
 * @package PHPMailer
 */

class phpmailerException extends Exception
{
	/**
	 * Prettify error message output
	 * @return string
	 */
	public function errorMessage()
	{
		$errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
		return $errorMsg;
	}
}
