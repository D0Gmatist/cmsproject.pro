<?php

namespace Modules\Functions;

class Functions implements FunctionsInterface {
	/** @var array  */
	private $config = [];

	/**
	 * Functions constructor.
	 * @param array $config
	 */
	function __construct ( array $config ) {
		$this->config = $config;

	}

	/**
	 * @param $url
	 * @return string
	 */
	public function cleanUrl ( $url ) {
		if( $url == '' ) {
			return false;

		}
		$url = str_replace( 'http://', '', strtolower( $url ) );
		$url = str_replace( 'https://', '', $url );

		if( substr( $url, 0, 2 ) == '//' ) {
			$url = str_replace( '//', '', $url );

		}

		if( substr( $url, 0, 4 ) == 'www.' ) {
			$url = substr( $url, 4 );

		}
		$url = explode( '/', $url );
		$url = reset( $url );
		$url = explode( ':', $url );
		$url = reset( $url );

		return $url;

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

	/**
	 * @param $name
	 * @param $value
	 * @param $expires
	 */
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
	public function getIp () {
		if ( filter_var( $_SERVER['REMOTE_ADDR'] , FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			return filter_var( $_SERVER['REMOTE_ADDR'] , FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );

		}

		if ( filter_var( $_SERVER['REMOTE_ADDR'] , FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
			return filter_var( $_SERVER['REMOTE_ADDR'] , FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 );

		}
		return 'localhost';

	}

	/**
	 * @param $ip_array
	 * @return bool
	 */
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

	/**
	 * @param $IP
	 * @param $CIDR
	 * @return bool
	 */
	public function maskMatch( $IP, $CIDR ) {
		list ( $net, $mask) = explode ( '/', $CIDR );
		return ( ip2long( $IP ) & ~( ( 1 << ( 32 - $mask ) ) - 1 ) ) == ip2long ( $net );

	}

	/**
	 * @param $value
	 * @param $charset
	 * @return int
	 */
	public function strLen( $value, $charset ) {
		if ( strtolower( $charset ) == 'utf-8' ) {
			if( function_exists( 'mb_strlen' ) ) {
				return mb_strlen( $value, 'utf-8' );

			} elseif( function_exists( 'iconv_strlen' ) ) {
				return iconv_strlen( $value, 'utf-8' );

			}

		}

		return strlen( $value );

	}

}