<?php

namespace Modules\Plugins\UserPlugins;

use Modules\Functions\Functions;
use Modules\Mail\Mail;
use Modules\Mysql\Db\Db;
use Modules\Plugins\MsgBox\MsgBox;
use Modules\Template\Template;

final class RegistrationVk {

	/** @var bool  */
	private $isLogged = false;

	/** @var array  */
	private $memberId;

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

	/** @var MsgBox  */
	private $msgBox;

	/** @var Mail  */
	private $mail;

	/** @var bool  */
	private $registration = false;

	/** @var int  */
	private $step = 0;



	/**
	 * Registration constructor.
	 * @param $isLogged
	 * @param array $memberId
	 * @param $action
	 * @param Functions $functions
	 * @param Db $db
	 * @param Template $tpl
	 * @param MsgBox $msgBox
	 * @param Mail $mail
	 * @param array $config
	 * @param array $language
	 */
	function __construct ( $isLogged, array $memberId, $action, Functions $functions, Db $db, Template $tpl, MsgBox $msgBox, Mail $mail, array $config, array $language ) {
		$this->isLogged = $isLogged;
		$this->memberId = $memberId;

		$this->functions = $functions;
		$this->db = $db;

		$this->config = $config;
		$this->language = $language;

		$this->tpl = $tpl;
		$this->msgBox = $msgBox;
		$this->mail = $mail;

		if ( $_POST[ 'action' ] == 'registration' ) {
			$this->step = 1;
			$this->stepOne();

		}

		$this->getContent();

	}

	/**
	 * @return bool|mixed|string
	 */
	private function checkLogin () {

	}

	/**
	 * @return bool
	 */
	private function checkEmail () {


	}

	/**
	 * @return bool
	 */
	private function checkPassword () {


	}

	private function stepOne () {
		$this->registration = true;
		$this->complete ();

	}

	private function complete () {

	}

	private function urlVkForm () {
		$url = 'https://oauth.vk.com/authorize';

		$params_user = array(
			'client_id'     => $this->config['vk_app_id'],
			'redirect_uri'  => 'https://d0gmatist.pro/licens/xxx.php',
			//'redirect_uri'  => $this->config['save_home_url'] . $this->config['vk_app_redirect'] ,
			'response_type' => 'code',
			'display' 		=> 'page',
			'scope' 		=> 'offline',
			'v' 			=> $this->config['vk_app_version']

		);

		return $url . '?' . urldecode( http_build_query( $params_user ) );

	}
	private function getContent () {
		$urlVkForm = $this->urlVkForm();
		$this->tpl->loadTemplate( 'user/registration_vk.tpl' );

		switch ( $this->step ) {

			case '1' :

				$this->tpl->setBlock( "'\\[form_registration_vk\\](.*?)\\[/form_registration_vk\\]'si", "" );

				break;

			case '0' :
			default :

				$this->tpl->set( '[form_registration_vk]', "" );
				$this->tpl->set( '[/form_registration_vk]', "" );

				break;

		}

		$this->tpl->set( '{url_vk_form}', $urlVkForm );


		$this->tpl->compile( 'content' );

		$this->tpl->clear();

	}

}