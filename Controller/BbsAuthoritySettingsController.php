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
//		$authSettingOptions = array(
//				__d('bbses', 'Room administrator'),
//				__d('bbses', 'Cheif editor'),
//				__d('bbses', 'Editor'),
//			);
//		$this->set('authSettingOptions', $authSettingOptions);

		$this->__setBbs();
		//debug用
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		if ($this->request->isPost()) {
			$data = $this->data;
			//$blockId, $userId, $contentCreatable, $contentEditable, $is_post_list
			if (!$bbs = $this->Bbs->getBbs(
				isset($this->data['Block']['id']) ? (int)$this->data['Block']['id'] : null,
				false,
				false,
				false,
				false
			)) {
				//bbsテーブルデータ作成とkey格納
				$bbs = $this->Bbs->create(['key' => Security::hash('bbs' . mt_rand() . microtime(), 'md5')]);
			}

			//boolean値が文字列になっているため個別で格納し直している
			$bbs['Bbs']['post_create_authority'] = ($data['Bbs']['post_create_authority'] === '1') ? true : false;
			$bbs['Bbs']['post_publish_authority'] = ($data['Bbs']['post_publish_authority'] === '1') ? true : false;
			$bbs['Bbs']['comment_create_authority'] = ($data['Bbs']['comment_create_authority'] === '1') ? true : false;
			//IDリセット
			unset($data['Bbs']['id']);
			$data = Hash::merge($data, $bbs);

			if (!$bbs = $this->Bbs->saveBbs($data)) {
				if (!$this->__handleValidationError($this->Bbs->validationErrors)) {
					return;
				}
			}

			$this->set('blockId', $bbs['Bbs']['block_id']);
			if (!$this->request->is('ajax')) {
				$backUrl = CakeSession::read('backUrl');
				CakeSession::delete('backUrl');
				$this->redirect($backUrl);
			}
			return;
		}
	}

/**
 * __initBbs method
 *
 * @return void
 */
	private function __setBbs() {
		//ユーザIDを取得し、Viewにセット
		$this->set('userId', $this->Session->read('Auth.User.id'));

		//掲示板データを取得
		if (!$bbses = $this->Bbs->getBbs(
				$this->viewVars['blockId'],
				$this->viewVars['userId'],
				$this->viewVars['contentCreatable'],
				$this->viewVars['contentEditable'],
				$is_post_list = false
			)
		) {
			$bbses = $this->Bbs->create();
			$bbses['Bbs']['post_create_authority'] = ($bbses['Bbs']['post_create_authority'] === '1') ? true : false;
			$bbses['Bbs']['post_publish_authority'] = ($bbses['Bbs']['post_publish_authority'] === '1') ? true : false;
			$bbses['Bbs']['comment_create_authority'] = ($bbses['Bbs']['comment_create_authority'] === '1') ? true : false;
		}
		$results = array(
			'bbses' => $bbses['Bbs'],
		);
		$this->set($results);
	}

}
