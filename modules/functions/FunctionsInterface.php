<?php

namespace Modules\Functions;

interface FunctionsInterface {
	public function cleanUrl ( $url );
	public function domain ();
	public function Session ( $sId = false );
	public function setCookie ( $name, $value, $expires );
	public function getIp ();
	public function allowedIp ( $ip_array );
	public function maskMatch( $IP, $CIDR );
	public function strLen( $value, $charset );

}