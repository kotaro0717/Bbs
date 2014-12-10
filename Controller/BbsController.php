<?php
/**
 * Bbs Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BbsAppController', 'Bbs.Controller');

/**
 * Bbs Controller
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Bbs\Controller
 */
class BbsController extends BbsAppController {

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Bbs.Bbs',
		'Bbs.BbsFrameSettings',
		'Bbs.BbsPosts',
		'Bbs.BbsPostContents',
		'Bbs.BbsPostsUsers',
		'Bbs.BbsTopics',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommonsBlock', //Use Announcement model
		'NetCommons.NetCommonsFrame',
//		'NetCommons.NetCommonsRoomRole' => array(
//			//コンテンツの権限設定
//			'allowedActions' => array(
//				'contentEditable' => array('setting', 'token', 'edit')
//			),
//			//コンテンツのワークフロー設定(公開権限チェック)
//			'workflowActions' => array('edit'),
//			'workflowModelName' => 'Bbs',
//		),
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsForm'
	);

/**
 * index method
 *
 * @return void
 */
	public function index() {
		//ToDo: bbsのframeKeyを元に、関連する記事のリストを取得する->モデルで
		//if ($this->viewVars['bbs']) {
			$bbs_post_list = $this->Bbs->find('first', array(
					'frame_key' => $this->viewVars['frameKey'],
					'order' => 'Bbs.id DESC',
				));
			$this->set('bbs_post_list', $bbs_post_list);
			$this->render('Bbs/index');
		//}
	}

/**
 * view method
 *
 * @return void
 */
//	public function view() {
//		//BbsPostデータを取得
//		$bbs_post = $this->BbsPost->getAnnouncement(
//				$this->viewVars['frameId'],
//				$this->viewVars['blockId'],
//				$this->viewVars['contentEditable']
//			);
//
//		//Announcementデータをviewにセット
//		$this->set('announcement', $announcement);
//		if (! $announcement) {
//			$this->autoRender = false;
//		}
//	}

/**
 * setting method
 *
 * @return void
 */
	public function setting() {
		$this->layout = 'NetCommons.modal';
		$this->view();
	}

/**
 * token method
 *
 * @return void
 */
	public function token() {
		$this->view();
		$this->render('Bbs/token', false);
	}

}
