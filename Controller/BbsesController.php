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
		$this->view = 'Bbses/index';

		$this->__initBbs();
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		$this->__setBbsSetting();
		if (!isset($this->viewVars['bbsSettings'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//引数0:親記事取得
		$this->__setPost($postId = 0);
		if (!isset($this->viewVars['bbsPosts'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

//		if ($this->request->is('ajax')) {
//			$tokenFields = Hash::flatten($this->request->data);
//			$hiddenFields = array(
//				'Announcement.block_id',
//				'Announcement.key'
//			);
//			$this->set('tokenFields', $tokenFields);
//			$this->set('hiddenFields', $hiddenFields);
//		}

		if ($this->viewVars['contentEditable']) {
			$this->view = 'Bbses/indexForEditor';
		}
		if (! $this->viewVars['bbses']) {
			$this->autoRender = false;
		}
	}

/**
 * view method
 *
 * @return void
 */
	public function view($frameId, $postId) {
		$this->view = 'Bbses/view';
		$this->__initBbs();
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		$this->__setBbsSetting();
		if (!isset($this->viewVars['bbsSettings'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		$this->__setPost($postId);
		if (!isset($this->viewVars['bbsPosts'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}
		//debug($this->viewVars);
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
		$this->view = 'Bbses/add';
		$this->__initBbs();
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}
		//記事追加の場合、ステータスを別途セットする（とりあえず）
		$this->set($this->camelizeKeyRecursive(array('contentStatus' => '0')));

	}

/**
 * edit method
 *
 * @return void
 */
	public function edit() {
		//
	}

/**
 * __initBbs method
 *
 * @return void
 */
	private function __initBbs() {
		//掲示板データを取得
		if (!$bbses = $this->Bbs->getBbs($this->viewVars['blockId'])
		) {
			$bbses = $this->Bbs->create();
		}

		//camelize
		$results = array(
			'bbses' => $bbses['Bbs'],
			'bbsPostNum' => count($bbses['BbsPost'])
		);
		$this->set($this->camelizeKeyRecursive($results));
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
		);
		$this->set($this->camelizeKeyRecursive($results));
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setPost($postId) {
		$bbsPosts = $this->BbsPost->getPosts(
				$this->viewVars['bbses']['id'],
				$this->viewVars['bbsSettings']['visiblePostRow'],
				$this->viewVars['contentEditable'],
				$postId
			);

		//camelize
		foreach ($bbsPosts as $bbsPost) {
			$key = array(
				'bbsPosts' => $bbsPost['BbsPost'],
			);
			$posts[] = $this->camelizeKeyRecursive($key);
		}

		//配列の再構成 TODO:スリムじゃない
		foreach ($posts as $post) {
			$result[] = $post['bbsPosts'];
		}
		$results['bbsPosts'] = $result;
		$this->set($results);
	}
}
