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
				'contentPublishable' => array('edit'),
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
 * edit method
 *
 * @return void
 */
	public function edit() {
		//掲示板の表示設定情報を取得
		$bbsSettings = $this->BbsFrameSetting->getBbsSetting(
										$this->viewVars['frameKey']);
		$results = array(
			'bbsSettings' => $bbsSettings['BbsFrameSetting'],
		);
		$this->set($results);

		if (! $this->request->isPost()) {
			return;
		}

		$data = $this->data;

		if (! $bbsSetting = $this->BbsFrameSetting->getBbsSetting(
			isset($data['Frame']['key']) ? $data['Frame']['key'] : null
		)) {
			//bbsFrameSettingテーブルデータ生成
			$bbsSetting = $this->BbsFrameSetting->create();
		}

		//作成時間,更新時間を再セット
		unset($bbsSetting['BbsFrameSetting']['created'], $bbsSetting['BbsFrameSetting']['modified']);
		$data = Hash::merge($bbsSetting, $data);

		if (! $bbsSetting = $this->BbsFrameSetting->saveBbsSetting($data)) {
			if (! $this->handleValidationError($this->BbsFrameSetting->validationErrors)) {
				return;
			}
		}

		$this->set('frameKey', $bbsSetting['BbsFrameSetting']['frame_key']);

		if (!$this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}
}
