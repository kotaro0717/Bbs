<?php
/**
 * Bbses Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BbsesAppController', 'Bbses.Controller');

/**
 * Bbses Controller
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Bbses\Controller
 */
class BbsesController extends BbsesAppController {

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Frames.Frame',
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
				'contentEditable' => array('edit')
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
 * index method
 *
 * @return void
 */
	public function index() {
		$this->__initBbs();
		$this->render('Bbses/index');
		//ToDo: bbsのframeKeyを元に、関連する記事のリストを取得する->モデルで
		//if ($this->viewVars['bbs']) {
//			$bbs_post_list = $this->Bbs->find('first', array(
//					'frame_key' => $this->viewVars['frameKey'],
//					'order' => 'Bbs.id DESC',
//				));
//			$this->set('bbs_post_list', $bbs_post_list);
//			$this->render('Bbs/index');
		//}
	}

/**
 * view method
 *
 * @return void
 */
	public function view() {
//		//BbsPostデータを取得
//		$bbs_post = $this->BbsPost->getBbs(
//				$this->viewVars['frameId'],
//				$this->viewVars['blockId'],
//				$this->viewVars['contentEditable']
//			);
//
//		//Bbsデータをviewにセット
//		$this->set('announcement', $announcement);
//		if (! $announcement) {
//			$this->autoRender = false;
//		}
		$this->render('Bbses/view');
	}

/**
 * view method
 *
 * @return void
 */
	public function commentView() {
		$this->render('Bbses/commentView');
	}
/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->render('Bbses/add');
	}

/**
 * __initBbs method
 *
 * @return void
 */
	private function __initBbs() {
		//掲示板フレーム設定を取得
		$bbs_settings = $this->BbsFrameSetting->getBbsSetting(
										$this->viewVars['frameKey']);
		$this->set($bbs_settings);

		//掲示板関連データを取得
		if (!$bbses = $this->Bbs->getBbs($this->viewVars['blockId'])
		) {
			$bbses = $this->Bbs->create();
		}
		$this->set($bbses);
		$this->set('bbs', $bbses);

		$bbs_posts = $this->BbsPost->getPosts(
				$this->viewVars['Bbs']['key'],
				$this->viewVars['BbsFrameSetting']['visible_post_row'],
				$this->viewVars['contentEditable']
			);
		$this->set('bbs_posts', $bbs_posts);

		//debug_info
		//var_dump($this->viewVars);

//		$results = array(
//			'bbses' => $bbses['Bbs'],
			//'bbsSettings' => $bbs_settings['BbsFrameSetting'],
			//'contentStatus' => $bbses['BbsPost']['status'],
//		);
		//$results = $this->camelizeKeyRecursive($results);
	}
}
