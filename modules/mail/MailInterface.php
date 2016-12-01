<?php

namespace Modules\Mail;

interface MailInterface {
	public function doSend ( $config, $is_html = false );
	public function send( $to, $subject, $message );
}