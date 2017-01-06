<?php

namespace Modules\Plugins\Vk;

use Modules\Functions\Functions;
use Modules\Mysql\Db\Db;
use Modules\Template\Template;

if ( ! defined ( 'ENGINE' ) ) {
	die ( 'Get out of here!' );

}

final class VkSearchGroupForm {

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

	/** @var array  */
	private $data;

	/** @var Template  */
	private $tpl;

	/** @var int  */
	private $stepCount = 1000;

	/** @var int  */
	private $offset = 0;

	/** @var array  */
	private $result = [];

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

		/** @var VkApi $vkApi */
		$this->vkApi = new VkApi( $this->memberId = $memberId, $this->config );

		if ( $_POST['method'] == 'ajax' OR $_GET['method'] == 'ajax' ) {
			$this->searchForm();

		} else {
			$this->getForm();

		}

	}

	private function getForm ()	{
		$this->tpl->loadTemplate( 'vk_group_search_form.tpl' );

		$this->tpl->compile( 'content' );

		$this->tpl->clear();

	}

	private function searchForm () {
		$this->data = $_GET['data'];

		$vk_get = $this->vkApi->getApiGroupsSearch(
			(string)$this->data['q'],
			(string)$this->data['type'],
			(int)$this->data['countries'],
			(int)$this->data['cities'],
			(int)$this->data['sort'],
			(int)$this->offset,
			(int)$this->stepCount
		);

		if ( is_array( $vk_get[ 'response' ] ) AND count( $vk_get[ 'response' ] ) > 0 ) {
			$this->result = $vk_get[ 'response' ];

		}

	}

	public function getResult () {
		if ( count( $this->result['count'] ) > 0 ) {
			$n = 0;
			foreach ( $this->result['items'] AS $item ) {
				$n++;

				$this->tpl->loadTemplate ( 'vk_group_search_result.tpl' );

				$this->tpl->set ( '{id}', $item[ 'id' ] );
				$this->tpl->set ( '{name}', $item[ 'name' ] );
				$this->tpl->set ( '{screen_name}', $item[ 'screen_name' ] );

				switch ( $item[ 'is_closed' ] ) {
					case 0:
						$this->tpl->set ( '{is_closed}', 'Сообщество открытое' );
						break;

					case 1:
						$this->tpl->set ( '{is_closed}', 'Сообщество закрытое' );
						break;

					case 2:
						$this->tpl->set ( '{is_closed}', 'Сообщество частное' );
						break;

				}

				if ( trim ( $item[ 'photo_50' ] ) == '' ) {
					$this->tpl->set ( '{photo_50}', $this->config[ 'http_home_url' ].'templates/'.$this->config[ 'skin' ].'/img/group_avatar_50.png' );

				} else {
					$this->tpl->set ( '{photo_50}', $item[ 'photo_50' ] );

				}

				if ( trim ( $item[ 'photo_100' ] ) == '' ) {
					$this->tpl->set ( '{photo_100}', $this->config[ 'http_home_url' ].'templates/'.$this->config[ 'skin' ].'/img/group_avatar_100.png' );

				} else {
					$this->tpl->set ( '{photo_100}', $item[ 'photo_100' ] );

				}

				if ( trim ( $item[ 'photo_200' ] ) == '' ) {
					$this->tpl->set ( '{photo_200}', $this->config[ 'http_home_url' ].'templates/'.$this->config[ 'skin' ].'/img/group_avatar_200.png' );

				} else {
					$this->tpl->set ( '{photo_200}', $item[ 'photo_200' ] );

				}

				switch ( $item[ 'type' ] ) {
					case 'group':
						$this->tpl->set ( '{type}', 'Группа' );
						break;

					case 'page':
						$this->tpl->set ( '{type}', 'Публичная страница' );
						break;

					case 'event':
						$this->tpl->set ( '{type}', 'Мероприятие' );
						break;

				}

				$this->tpl->compile ( 'vk_group_search_result' );

			}

			if ( $this->tpl->result[ 'vk_group_search_result' ] ) {
				$this->tpl->loadTemplate ( 'vk_group_search_result_block.tpl' );

				$this->tpl->set ( '{vk_group_search_result}', $this->tpl->result[ 'vk_group_search_result' ] );
				$this->tpl->set ( '{count}', $n );

				$this->tpl->compile ( 'vk_group_search_result_block' );

				return $this->tpl->result[ 'vk_group_search_result_block' ];

			}

			return false;

		}

		return false;

	}

}