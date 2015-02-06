<?php
/**
 * BbsComments Controller
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
class BbsCommentsController extends BbsesAppController {

/**
 * use helpers
 *
 * @var array
 */
	public $useTable  = array(
		'bbs_posts'
	);

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
	public function view($frameId, $postId, $commentId) {
		$this->view = 'Bbses/viewForComment';

		$this->__setBbsSetting();
		if (!isset($this->viewVars['bbsSettings'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		$this->__setBbs();
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//親記事情報を取得
		$this->__setPost($postId);
		if (!isset($this->viewVars['bbsPosts'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//表示対象記事を取得
		$this->__setCurrentPost($commentId);
		if (!isset($this->viewVars['bbsCurrentPosts'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		$this->__setComment($commentId, $key = '', $params = '');
//		if (!isset($this->viewVars['bbsComments'])) {
//			throw new NotFoundException(__d('net_commons', 'Not Found'));
//		}
		//debug($this->viewVars);

//		$this->__setPostUser($postId);
//		if (!isset($this->viewVars['bbsPostUsers']) && !isset($this->viewVars['users'])) {
//			throw new NotFoundException(__d('net_commons', 'Not Found'));
//		}

	}

/**
 * view method
 *
 * @return void
 */
	public function add() {
		//
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
 * delete method
 *
 * @param string $postId postId
 * @throws NotFoundException
 * @return void
 */
	public function delete() {
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
			'currentVisibleCommentRow' => $bbsSettings['BbsFrameSetting']['visible_comment_row']
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
		$bbsPosts = $this->BbsPost->getPosts(
				$this->viewVars['bbses']['id'],
				1,
				$this->viewVars['contentCreatable'],
				$postId,
				false
			);
		//camelize
		$results = array(
			'bbsPosts' => $bbsPosts[0]['BbsPost'],
		);
		$this->set($this->camelizeKeyRecursive($results));
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setCurrentPost($postId) {
		$posts = $this->BbsPost->getCurrentPosts(
				$this->viewVars['bbses']['id'],
				$this->viewVars['contentCreatable'],
				$postId
			);
		//camelize
		$results = array(
			'bbsCurrentPosts' => $posts[0]['BbsPost'],
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

		$this->set($bbsCommnets);

		//camelize
//		foreach ($bbsCommnets as $bbsComment) {
//			$results = array(
//				'bbsComments' => $bbsComment['BbsPost'],
//			);
//			$comments[] = $this->camelizeKeyRecursive($results);
//		}
//
//		//配列の再構成 TODO:スリムじゃない
//		foreach ($comments as $comment) {
//			$result[] = $comment['bbsComments'];
//		}
//		$results['bbsComments'] = $result;
//
//		$this->set($results);
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
