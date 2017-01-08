<?php

namespace Modules\Functions;

use DateTime;
use DateTimeZone;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

class Functions {
	/** @var array  */
	private $config = [];

	/** @var array  */
	private $language = [];

	/**
	 * Functions constructor.
	 * @param array $config
	 */
	function __construct ( array $config, array $language ) {
		$this->config = $config;
		$this->language = $language;

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

	public function toTranslate( $var, $langTranslate, $lower = true, $point = true ) {

		if ( is_array( $var ) ) {
			return '';

		}
		$var = str_replace( chr( 0 ), '', $var );

		if ( ! is_array ( $langTranslate ) OR ! count( $langTranslate ) ) {
			$var = trim( strip_tags( $var ) );

			if ( $point ) {
				$var = preg_replace( "/[^a-z0-9\_\-.]+/mi", '', $var );

			} else {
				$var = preg_replace( "/[^a-z0-9\_\-]+/mi", '', $var );

			}
			$var = preg_replace( '#[.]+#i', '.', $var );
			$var = str_ireplace( '.php', '.ppp', $var );

			if ( $lower ) {
				$var = strtolower( $var );

			}

			return $var;

		}
		$var = trim( strip_tags( $var ) );
		$var = preg_replace( "/\s+/ms", "-", $var );
		$var = str_replace( "/", "-", $var );
		$var = strtr( $var, $langTranslate );

		if ( $point ) {
			$var = preg_replace( "/[^a-z0-9\_\-.]+/mi", '', $var );

		} else {
			$var = preg_replace( "/[^a-z0-9\_\-]+/mi", '', $var );

		}
		$var = preg_replace( '#[\-]+#i', '-', $var );
		$var = preg_replace( '#[.]+#i', '.', $var );

		if ( $lower ) {
			$var = strtolower( $var );

		}
		$var = str_ireplace( '.php', '', $var );
		$var = str_ireplace( '.php', '.ppp', $var );

		if ( strlen( $var ) > 200 ) {
			$var = substr( $var, 0, 200 );

			if ( ( $temp_max = strrpos( $var, '-' ) ) ) {
				$var = substr( $var, 0, $temp_max );

			}

		}

		return $var;

	}

	public function langDate( $format, $stamp ) {
		$timezones = [
			'Pacific/Midway', 'US/Samoa', 'US/Hawaii', 'US/Alaska', 'US/Pacific', 'America/Tijuana', 'US/Arizona',
			'US/Mountain', 'America/Chihuahua', 'America/Mazatlan', 'America/Mexico_City', 'America/Monterrey',
			'US/Central', 'US/Eastern', 'US/East-Indiana', 'America/Lima', 'America/Caracas', 'Canada/Atlantic',
			'America/La_Paz', 'America/Santiago', 'Canada/Newfoundland', 'America/Buenos_Aires', 'Greenland',
			'Atlantic/Stanley', 'Atlantic/Azores', 'Africa/Casablanca', 'Europe/Dublin', 'Europe/Lisbon',
			'Europe/London', 'Europe/Amsterdam', 'Europe/Belgrade', 'Europe/Berlin', 'Europe/Bratislava',
			'Europe/Brussels', 'Europe/Budapest', 'Europe/Copenhagen', 'Europe/Madrid', 'Europe/Paris', 'Europe/Prague',
			'Europe/Rome', 'Europe/Sarajevo', 'Europe/Stockholm', 'Europe/Vienna', 'Europe/Warsaw', 'Europe/Zagreb',
			'Europe/Athens', 'Europe/Bucharest', 'Europe/Helsinki', 'Europe/Istanbul', 'Asia/Jerusalem', 'Europe/Kiev',
			'Europe/Minsk', 'Europe/Riga', 'Europe/Sofia', 'Europe/Tallinn', 'Europe/Vilnius', 'Asia/Baghdad',
			'Asia/Kuwait', 'Africa/Nairobi', 'Asia/Tehran', 'Europe/Kaliningrad', 'Europe/Moscow', 'Europe/Volgograd',
			'Europe/Samara', 'Asia/Baku', 'Asia/Muscat', 'Asia/Tbilisi', 'Asia/Yerevan', 'Asia/Kabul',
			'Asia/Yekaterinburg', 'Asia/Tashkent', 'Asia/Kolkata', 'Asia/Kathmandu', 'Asia/Almaty', 'Asia/Novosibirsk',
			'Asia/Jakarta', 'Asia/Krasnoyarsk', 'Asia/Hong_Kong', 'Asia/Kuala_Lumpur', 'Asia/Singapore', 'Asia/Taipei',
			'Asia/Ulaanbaatar', 'Asia/Urumqi', 'Asia/Irkutsk', 'Asia/Seoul', 'Asia/Tokyo', 'Australia/Adelaide',
			'Australia/Darwin', 'Asia/Yakutsk', 'Australia/Brisbane', 'Pacific/Port_Moresby', 'Australia/Sydney',
			'Asia/Vladivostok', 'Asia/Sakhalin', 'Asia/Magadan', 'Pacific/Auckland', 'Pacific/Fiji'
		];

		if ( ! $stamp ) {
			$stamp = time();

		}

		$local = new DateTime( '@' . $stamp );

		$localzone = date_default_timezone_get();

		if ( ! in_array( $localzone, $timezones ) ) {
			$localzone = 'Europe/Moscow';

		}

		$local->setTimeZone( new DateTimeZone( $localzone ) );

		return strtr( $local->format( $format ), $this->language['date'] );

	}

	public function formatDate( $matches, $newsFormatDate ) {
		return $this->langDate( $matches, $newsFormatDate );

	}

}