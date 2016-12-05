<?php

namespace Modules\Plugins\UserPlugins;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Template\Template;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

final class LoginForm {

	/** @var bool  */
	private $isLogged = false;

	/** @var array  */
	private $memberId;

	/** @var array  */
	private $groupVar;

	/** @var Functions  */
	private $functions;

	/** @var Db  */
	private $db;

	/** @var array  */
	private $config;

	/** @var array  */
	private $language;

	/** @var Template  */
	private $tpl;

	/**
	 * UserPanel constructor.
	 * @param $isLogged
	 * @param array $memberId
	 * @param array $groupVar
	 * @param Functions $functions
	 * @param Db $db
	 * @param Template $tpl
	 * @param array $config
	 * @param array $language
	 * @internal param $action
	 */
	function __construct ( $isLogged, array $memberId, array $groupVar, Functions $functions, Db $db, Template $tpl, array $config, array $language ) {
		$this->isLogged = $isLogged;
		$this->memberId = $memberId;
		$this->groupVar = $groupVar;

		$this->functions = $functions;
		$this->db = $db;

		$this->config = $config;
		$this->language = $language;

		$this->tpl = $tpl;

		$this->getForm();

	}

	private function authorize () {
		$url = 'https://oauth.vk.com/authorize';

		$authorizeUrl = [
			'client_id'     => $this->config['vk_app_id'],
			'redirect_uri'  => HTTP_HOME_URL . $this->config['vk_login'],
			'response_type' => 'code',
			'display' 		=> 'page',
			'scope' 		=> 'offline',
			'v' 			=> $this->config['vk_app_version'],

		];

		return $url . '?' . urldecode( http_build_query( $authorizeUrl ) );

	}

	private function getForm ()	{

		$this->tpl->loadTemplate( 'user/login_form.tpl' );

		switch ( $_POST['action'] ) {

			case 'registration' :

				$this->tpl->set( '{form_registration}', 'block' );
				$this->tpl->set( '{form_forget}', 'none' );
				$this->tpl->set( '{form_login}', 'none' );

				break;

			case 'forget' :

				$this->tpl->set( '{form_registration}', 'none' );
				$this->tpl->set( '{form_forget}', 'block' );
				$this->tpl->set( '{form_login}', 'none' );

				break;

			case 'login' :
			default :

				$this->tpl->set( '{form_registration}', 'none' );
				$this->tpl->set( '{form_forget}', 'none' );
				$this->tpl->set( '{form_login}', 'block' );

				break;

		}

		$this->tpl->set( '{vk_login_url}', $this->authorize() );

		$this->tpl->compile( 'content' );

		$this->tpl->clear();

	}


}
