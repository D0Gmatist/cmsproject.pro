<?php

namespace Modules\Functions;

class Functions {
	public $url = '';

	/**
	 * @param $url
	 * @return string
	 */
	public function cleanUrl ( $url ) {
		$this->url = $url;
		if( $this->url == '' ) {
			return false;

		}
		$this->url = str_replace( 'http://', '', strtolower( $this->url ) );
		$this->url = str_replace( 'https://', '', $this->url );

		if( substr( $this->url, 0, 2 ) == '//' ) {
			$this->url = str_replace( '//', '', $this->url );

		}

		if( substr( $this->url, 0, 4 ) == 'www.' ) {
			$this->url = substr( $this->url, 4 );

		}
		$this->url = explode( '/', $this->url );
		$this->url = reset( $this->url );
		$this->url = explode( ':', $this->url );
		$this->url = reset( $this->url );

		return $this->url;

	}

	public function domain () {
		$domain_cookie = explode ( '.', $this->cleanUrl( $_SERVER['HTTP_HOST'] ) );
		$domain_cookie_count = count( $domain_cookie );
		$domain_allow_count = -2;

		if ( $domain_cookie_count > 2 ) {
			if ( in_array( $domain_cookie[$domain_cookie_count-2], array( 'com', 'net', 'org' ) ) ) {
				$domain_allow_count = -3;

			}
			if ( $domain_cookie[$domain_cookie_count-1] == 'ua' ) {
				$domain_allow_count = -3;

			}
			$domain_cookie = array_slice( $domain_cookie, $domain_allow_count );

		}
		$domain_cookie = '.' . implode( '.', $domain_cookie );

		if ( ( ip2long( $_SERVER['HTTP_HOST'] ) == -1 OR ip2long( $_SERVER['HTTP_HOST'] ) === false ) AND strtoupper( substr( PHP_OS, 0, 3 ) ) !== 'WIN' ) {
			define( 'DOMAIN', $domain_cookie );

		} else {
			define( 'DOMAIN', null );

		}

	}

	/**
	 * @param $sId
	 */
	public function Session ( $sId = false ) {
		$params = session_get_cookie_params();

		if ( DOMAIN ) {
			$params['domain'] = DOMAIN;

		}

		if( version_compare( PHP_VERSION, '5.2', '<' ) ) {
			session_set_cookie_params( $params['lifetime'], '/', $params['domain'].'; HttpOnly', $params['secure'] );

		} else {
			session_set_cookie_params( $params['lifetime'], '/', $params['domain'], $params['secure'], true );

		}

		if ( $sId ) {
			@session_id( $sId );

		}
		@session_start();

	}

	public function setCookie ( $name, $value, $expires ) {
		if ( $expires ) {
			$expires = time() + ( $expires * 86400 );

		} else {
			$expires = FALSE;

		}

		if ( PHP_VERSION < 5.2 ) {
			if ( DOMAIN ) {
				setcookie( $name, $value, $expires, '/', '; HttpOnly' );

			} else {
				setcookie( $name, $value, $expires, '/', DOMAIN . '; HttpOnly' );

			}

		} else {
			setcookie( $name, $value, $expires, "/", DOMAIN, null, true );

		}

	}

	/**
	 * @return string
	 */
	function getIp () {
		if ( filter_var( $_SERVER['REMOTE_ADDR'] , FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			return filter_var( $_SERVER['REMOTE_ADDR'] , FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );

		}

		if ( filter_var( $_SERVER['REMOTE_ADDR'] , FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
			return filter_var( $_SERVER['REMOTE_ADDR'] , FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 );

		}
		return 'localhost';

	}

	public function allowedIp ( $ip_array ) {
		$ip_array = trim( $ip_array );
		$_IP = $this->getIp();

		if( $ip_array == '' ) {
			return true;

		}
		$ip_array = explode( '|', $ip_array );
		$db_ip_split = explode( '.', $_IP );

		foreach ( $ip_array as $ip ) {
			$ip = trim( $ip );

			if ( $ip == $_IP ) {
				return true;

			}

			if ( count( explode ( '/', $ip ) ) == 2 ) {
				if ( $this->maskMatch( $_IP, $ip ) ) {
					return true;

				}

			} else {
				$ip_check_matches = 0;
				$this_ip_split = explode( '.', $ip );

				for ( $i_i = 0; $i_i < 4; $i_i++ ) {
					if ( $this_ip_split[$i_i] == $db_ip_split[$i_i] OR $this_ip_split[$i_i] == '*' ) {
						$ip_check_matches += 1;

					}

				}

				if ( $ip_check_matches == 4 ) {
					return true;

				}

			}

		}
		return false;

	}

	function  maskMatch( $IP, $CIDR ) {
		list ( $net, $mask) = explode ( '/', $CIDR );
		return ( ip2long( $IP ) & ~( ( 1 << ( 32 - $mask ) ) - 1 ) ) == ip2long ( $net );

	}

}