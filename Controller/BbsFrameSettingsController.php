<?php
/**
 * BbsFrameSettings Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BbsesAppController', 'Bbses.Controller');

/**
 * BbsFrameSettings Controller
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Bbses\Controller
 */
class BbsFrameSettingsController extends BbsesAppController {

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Bbses.Bbs',
		'Bbses.BbsFrameSetting',
		'Bbses.BbsPost',
		'Bbses.BbsPostsUser',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommonsBlock',
		'NetCommons.NetCommonsFrame',
		'NetCommons.NetCommonsRoomRole' => array(
			//コンテンツの権限設定
			'allowedActions' => array(
				//'contentEditable' => array('add', 'edit', 'delete'),
				//'contentCreatable' => array('add', 'edit', 'delete'),
			),
		),
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Token'
	);

/**
 * view method
 *
 * @return void
 */
	public function view() {
		$this->view = 'Bbses/displayChange';

		$this->__setBbsSetting();
		if (!isset($this->viewVars['bbsSettings'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

	}

/**
 * edit method
 *
 * @return void
 */
	public function edit($frameId, $postId) {
		//
	}

/**
 * __initBbs method
 *
 * @return void
 */
	private function __setBbsSetting() {
		//掲示板の表示設定情報を取得
		$bbsSettings = $this->BbsFrameSetting->getBbsSetting(
										$this->viewVars['frameKey']);

		//camelize
		$results = array(
			'bbsSettings' => $bbsSettings['BbsFrameSetting'],
			'currentVisiblePostRow' => $bbsSettings['BbsFrameSetting']['visible_post_row']
		);
		$this->set($this->camelizeKeyRecursive($results));

	}

}
