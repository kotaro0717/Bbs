<?php
/**
 * BbsPosts Controller
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
class BbsPostsController extends BbsesAppController {

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
				'contentEditable' => array('add', 'edit', 'delete'),
				'contentCreatable' => array('add', 'edit', 'delete'),
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
	public function view($frameId, $postId) {
		$this->view = 'Bbses/view';

		$this->__setBbs();
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

		$this->__setComment($postId, $key = '', $params = '');
//		if (!isset($this->viewVars['bbsComments'])) {
//			throw new NotFoundException(__d('net_commons', 'Not Found'));
//		}

	}

/**
 * view method
 *
 * @return void
 */
	public function add() {
		$this->view = 'Bbses/add';
		$this->__setBbs();
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}
		//記事追加の場合、ステータスを別途セットする（とりあえず）
		$this->set(array('contentStatus' => '0'));

//		if ($this->request->is('ajax')) {
//			$tokenFields = Hash::flatten($this->request->data);
//			$hiddenFields = array(
//				'BbsPost.bbs_id',
//				'BbsPost.parent_id',
//				'BbsPost.key'
//			);
//			$this->set('tokenFields', $tokenFields);
//			$this->set('hiddenFields', $hiddenFields);
//		}
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit($frameId, $postId) {
		$this->view = 'Bbses/add';
		$this->__setBbs();
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		$this->__setPost($postId);
		if (!isset($this->viewVars['bbsPosts'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//記事追加の場合、ステータスを別途セットする（とりあえず）
		$this->set(array('contentStatus' => '0'));

		//登録処理
//		if ($this->request->isPost()) {
//			if ($matches = preg_grep('/^save_\d/', array_keys($this->data))) {
//				list(, $status) = explode('_', array_shift($matches));
//			}
//			$data = array_merge_recursive(
//				$this->data,
//				['BbsPost' => ['status' => $status]]
//			);
//
//			$bbsPost = $this->BbsPost->savePost($data);
//			$this->redirect(isset($this->request->query['back_url']) ? $this->request->query['back_url'] : null);
//			return;
//		}

		//最新データ取得
//		$this->__setBbsSetting();
//		$this->__setBbs();
//		$this->__setPost();

		//$this->set('backUrl', isset($this->request->query['back_url']) ? $this->request->query['back_url'] : null);
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
		}
		//camelize
		$results = array(
			'bbses' => $bbses['Bbs'],
		);
		$results = $this->camelizeKeyRecursive($results);
		$this->set($results);
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setPost($postId) {
		$bbsPost = $this->BbsPost->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $postId,
				),
			)
		);
		//camelize
		$results = array(
			'bbsPosts' => $bbsPost['BbsPost'],
		);
		$this->set($this->camelizeKeyRecursive($results));
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setComment($postId, $key, $params) {
		//TODO:Modelとの切り分け考える
		//初期設定
		$visibleCommentRow = $this->viewVars['bbsSettings']['visibleCommentRow'];
		$sortOrder = $this->__setSortOrder($params);
		//key(2):表示件数
		if ($key === '2') {
			$visibleCommentRow = $params;
			$results = array(
				'currentVisibleCommentRow' => $visibleCommentRow

			);
			$this->set($results);
		}

		$bbsCommnets = $this->BbsPost->getPosts(
				$this->viewVars['bbses']['id'],
				$visibleCommentRow,
				$this->viewVars['contentCreatable'],
				$postId,
				$sortOrder
			);

		//camelize
		foreach ($bbsCommnets as $bbsComment) {
			$results = array(
				'bbsComments' => $bbsComment['BbsPost'],
			);
			$comments[] = $this->camelizeKeyRecursive($results);
		}

		//配列の再構成 TODO:スリムじゃない
		foreach ($comments as $comment) {
			$result[] = $comment['bbsComments'];
		}
		$results['bbsComments'] = $result;

		$this->set($results);

		}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setSortOrder($params) {
		switch ($params) {
		case '1':
		case '':
		default :
			//最新の投稿順
			$sortStr = __d('bbses', 'Latest comment order');
			$this->set('currentCommentSortOrder', $sortStr);
			return 'BbsPost.created DESC';
		case '2':
			//古い投稿順
			$sortStr = __d('bbses', 'Older comment order');
			$this->set('currentCommentSortOrder', $sortStr);
			return 'BbsPost.created ASC';
		case '3':
			//ステータス順
			$sortStr = __d('bbses', 'Status order');
			$this->set('currentCommentSortOrder', $sortStr);
			return 'BbsPost.status DESC';
		}
	}


}
