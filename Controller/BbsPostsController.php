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
		'Users.User',
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

		$this->__setBbsSetting();
		if (!isset($this->viewVars['bbsSettings'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		$this->__setBbs();
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//記事を取る時にその人のユーザ名も含めて返すべき
		$this->__setPost($postId);
		if (!isset($this->viewVars['bbsPosts'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//コメントを取る時にその人のユーザ名も含めて返すべき
		$this->__setComment($postId, $key = '', $params = '');
		if (!isset($this->viewVars['bbsComments'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		$this->__setPostUser($postId);
//		if (!isset($this->viewVars['bbsPostUsers']) && !isset($this->viewVars['users'])) {
//			throw new NotFoundException(__d('net_commons', 'Not Found'));
//		}

	}

/**
 * view method
 *
 * @return void
 */
	public function add($frameId, $postId = '', $postFlag = '') {
		$this->view = 'Bbses/add';
		$this->set(array('addStrings' => __d('bbses', 'Create post')));
		$this->__setBbs();
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//コメント返信の場合
		if ($postFlag === '2') {
			$this->set(array('addStrings' => __d('bbses', 'Create comment')));
			$this->view = 'Bbses/comment';
//			debug($postId);
			$this->__setPost($postId);
			if (!isset($this->viewVars['bbsPosts'])) {
				throw new NotFoundException(__d('net_commons', 'Not Found'));
			}
		}

		//記事追加の場合、ステータスを別途セットする（とりあえず）
		$this->set(array('contentStatus' => '0'));
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit($frameId, $postId) {
		$this->view = 'Bbses/add';
		$this->set(array('addStrings' => __d('bbses', 'Edit')));
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
 * delete method
 *
 * @param string $postId postId
 * @throws NotFoundException
 * @return void
 */
	public function delete($postId = null) {
		$this->BbsPost->id = $postId;
		if (!$this->BbsPost->exists()) {
			throw new NotFoundException(__('Invalid post'));
		}

		$this->request->onlyAllow('delete');
		if ($this->BbsPost->deleteFrame()) {
			return $this->flash(__('The post has been deleted.'), array('controller' => 'bbses', 'action' => 'index'));
		} else {
			return $this->flash(__('The post could not be deleted. Please, try again.'), array('controller' => 'bbses', 'action' => 'index'));
		}
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
		//BbsPostモデルで対象記事一件取得
		$bbsPosts = $this->BbsPost->getPosts(
				$this->viewVars['bbses']['id'],
				1,
				$this->viewVars['contentCreatable'],
				$postId,
				false
			);

		//取得した記事の作成者IDからユーザ情報を取得
		$user = $this->User->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $bbsPosts[0]['BbsPost']['created_user'],
				)
			)
		);

		//camelize用の配列へ格納
		$results = array(
			'bbsPosts' => $bbsPosts[0]['BbsPost'],
		);

		//取得した記事の配列にユーザ名を追加
		$results['bbsPosts']['username'] = $user['User']['username'];

		//camelize
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
//		if ($key === '2') {
//			$visibleCommentRow = $params;
//			$results = array(
//				'currentVisibleCommentRow' => $visibleCommentRow
//
//			);
//			$this->set($results);
//		}

		$bbsCommnets = $this->BbsPost->getComments(
				$this->viewVars['bbses']['id'],
				$visibleCommentRow,
				$this->viewVars['contentCreatable'],
				$postId,
				$sortOrder
			);

		//記事群をcamelizeするためのforeach
		foreach ($bbsCommnets as $bbsComment) {

			//取得した記事の作成者IDからユーザ情報を取得
			$user = $this->User->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $bbsComment['BbsPost']['created_user'],
					)
				)
			);

			$results = array(
				'bbsComments' => $bbsComment['BbsPost'],
			);

			//取得した記事の配列にユーザ名を追加
			$results['bbsComments']['username'] = $user['User']['username'];

			//camelize
			$comments[] = $this->camelizeKeyRecursive($results);
		}

		//配列の再構成 TODO:スリムじゃない
		foreach ($comments as $comment) {
			$result[] = $comment['bbsComments'];
		}
		$results['bbsComments'] = $result;

		$this->set($results);
//		$this->set($bbsCommnets);
		}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setPostUser($postId) {
		$user = $this->User->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $this->viewVars['bbsPosts']['createdUser'],
				)
			)
		);

		$bbsPostUser = $this->BbsPostsUser->getUsers(
				$user['User']['id'],
				$postId
			);

		$results = array(
			'bbsPostUsers' => $bbsPostUser['BbsPostsUser'],
			'users' => $user['User'],
		);
		return $this->set($this->camelizeKeyRecursive($results));
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