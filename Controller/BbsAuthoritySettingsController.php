<?php
/**
 * BbsAuthoritySettings Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BbsesAppController', 'Bbses.Controller');

/**
 * BbsAuthoritySettings Controller
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Bbses\Controller
 */
class BbsAuthoritySettingsController extends BbsesAppController {

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
		$this->setBbs();

		if (! $this->request->isPost()) {
			return;
		}

		$blockId = isset($this->data['Block']['id']) ?
					(int)$this->data['Block']['id'] : null;

		$data = $this->__setEditSaveData($this->data, $blockId);

		if (! $this->Bbs->saveBbs($data)) {
			if (! $this->handleValidationError($this->Bbs->validationErrors)) {
				return;
			}
		}

		if (! $this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}

/**
 * setEditSaveData
 *
 * @param array $postData post data
 * @param int $blockId blocks.id
 * @return array
 */
	private function __setEditSaveData($postData, $blockId) {
		if (! $bbs = $this->Bbs->getBbs($blockId)) {
			//bbsテーブルデータ作成とkey格納
			$bbs = $this->initBbs();

		}

		$bbs = $this->__convertStringToBoolean($postData, $bbs);

		$results = Hash::merge($postData, $bbs);

		//IDリセット
		unset($results['Bbs']['id']);

		return $results;
	}

/**
 * convertStringToBoolean
 *
 * @param array $postData post data
 * @param array $bbs bbses
 * @return array
 */
	private function __convertStringToBoolean($postData, $bbs) {
		//boolean値が文字列になっているため個別で格納し直し
		return $bbs['Bbs'] = array(
			'post_create_authority' => ($postData['Bbs']['post_create_authority'] === '1') ? true : false,
			'editor_publish_authority' => ($postData['Bbs']['editor_publish_authority'] === '1') ? true : false,
			'general_publish_authority' => ($postData['Bbs']['general_publish_authority'] === '1') ? true : false,
			'comment_create_authority' => ($postData['Bbs']['comment_create_authority'] === '1') ? true : false,
		);
	}
}
