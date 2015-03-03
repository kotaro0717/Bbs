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
		'Comments.Comment',
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
				'contentEditable' => array('add', 'edit', 'delete', 'likes', 'unlikes'),
				'contentCreatable' => array('add', 'edit', 'delete', 'likes', 'unlikes'),
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
 * @param int $frameId frames.id
 * @param int $postId posts.id
 * @param int $currentPage currentPage
 * @param int $sortParams sortParameter
 * @param int $visibleCommentRow visibleCommentRow
 * @param int $narrowDownParams narrowDownParameter
 * @throws BadRequestException throw new
 * @return void
 */
	public function view($frameId, $postId = '', $currentPage = '', $sortParams = '',
							$visibleCommentRow = '', $narrowDownParams = '') {
		if (! $postId) {
			BadRequestException(__d('net_commons', 'Bad Request'));
		}

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		//コメント表示数/掲示板名等をセット
		$this->setBbs();

		//選択した記事をセット
		$this->__setPost($postId);

		//各パラメータをセット
		$this->initParams($currentPage, $sortParams, $narrowDownParams);

		//表示件数をセット
		$visibleCommentRow =
			($visibleCommentRow === '')? $this->viewVars['bbsSettings']['visible_comment_row'] : $visibleCommentRow;
		$this->set('currentVisibleRow', $visibleCommentRow);

		//Treeビヘイビアのlft,rghtカラムを利用して対象記事のコメントのみ取得
		$conditions['and']['lft >'] = $this->viewVars['bbsPosts']['lft'];
		$conditions['and']['rght <'] = $this->viewVars['bbsPosts']['rght'];
		//記事に関するコメントをセット
		$this->setComment($conditions);

		//Treeビヘイビアのlft,rghtカラムを利用して対象記事のコメントのみ取得
		$conditions['and']['lft >'] = $this->viewVars['bbsPosts']['lft'];
		$conditions['and']['rght <'] = $this->viewVars['bbsPosts']['rght'];
		//ページング情報取得
		$this->setPagination($conditions, $postId);

		//コメント数をセットする
		$this->setCommentNum(
				$this->viewVars['bbsPosts']['lft'],
				$this->viewVars['bbsPosts']['rght']
			);

		//コメント作成権限をセットする
		//$this->setCommentCreateAuth();
		if (((int)$this->viewVars['rolesRoomId'] !== 0 &&
				(int)$this->viewVars['rolesRoomId'] < 4) ||
				($this->viewVars['bbses']['comment_create_authority'] &&
				$this->viewVars['contentCreatable'])) {

			$this->set('commentCreatable', true);

		} else {
			$this->set('commentCreatable', false);

		}

		//既読情報を登録
		$this->__saveReadStatus($postId);
	}

/**
 * add method
 *
 * @param int $frameId frames.id
 * @return void
 */
	public function add($frameId) {
		//掲示板名を取得
		$this->setBbs();

		//記事初期データを取得
		$this->__initPost();

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		if (! $this->request->isPost()) {
			return;
		}

		if (! $status = $this->parseStatus()) {
			return;
		}

		$data = $this->setAddSaveData($this->data, $status);

		if (! $this->BbsPost->savePost($data)) {
			if (!$this->handleValidationError($this->BbsPost->validationErrors)) {
				return;
			}
		}

		if (! $this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}

/**
 * edit method
 *
 * @param int $frameId frames.id
 * @param int $postId bbsPosts.id
 * @return void
 */
	public function edit($frameId, $postId) {
		//掲示板名を取得
		$this->setBbs();

		//編集する記事を取得
		$this->__setPost($postId);

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		if (! $this->request->isPost()) {
			return;
		}

		if (! $data = $this->setEditSaveData($this->data, $postId)) {
			return;
		}

		if (! $this->BbsPost->savePost($data)) {
			if (! $this->handleValidationError($this->BbsPost->validationErrors)) {
				return;
			}
		}

		if (! $this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}

/**
 * delete method
 *
 * @param int $frameId frames.id
 * @param int $postId postId
 * @return void
 */
	public function delete($frameId, $postId) {
		if (! $this->request->isPost()) {
			return;
		}
		if (! $this->BbsPost->delete($postId)) {

			$backUrl = array(
					'controller' => 'bbses',
					'action' => 'view',
					$frameId,
				);

			//記事一覧へリダイレクト
			$this->redirect($backUrl);
		}

		if (!$this->handleValidationError($this->BbsPost->validationErrors)) {
			return;
		}
	}

/**
 * likes method
 *
 * @param int $frameId frames.id
 * @param int $postId bbsPosts.id
 * @param int $userId users.id
 * @param bool $likesFlag likes flag
 * @return void
 */
	public function likes($frameId, $postId, $userId, $likesFlag) {
		if (! $this->request->isPost()) {
			return;
		}

		CakeSession::write('backUrl', $this->request->referer());

		if (! $postsUsers = $this->BbsPostsUser->getPostsUsers(
				$postId,
				$userId
		)) {
			//データがなければ登録
			$default = $this->BbsPostsUser->create();
			$default['BbsPostsUser'] = array(
						'post_id' => $postId,
						'user_id' => $userId,
						'likes_flag' => (int)$likesFlag,
				);
			$this->BbsPostsUser->savePostsUsers($default);

		} else {
			$postsUsers['BbsPostsUser']['likes_flag'] = (int)$likesFlag;
			$this->BbsPostsUser->savePostsUsers($postsUsers);

		}
		$backUrl = CakeSession::read('backUrl');
		CakeSession::delete('backUrl');
		$this->redirect($backUrl);
	}

/**
 * unlikes method
 *
 * @param int $frameId frames.id
 * @param int $postId bbsPosts.id
 * @param int $userId users.id
 * @param bool $unlikesFlag unlikes flag
 * @return void
 */
	public function unlikes($frameId, $postId, $userId, $unlikesFlag) {
		if (! $this->request->isPost()) {
			return;
		}

		CakeSession::write('backUrl', $this->request->referer());

		if (! $postsUsers = $this->BbsPostsUser->getPostsUsers(
				$postId,
				$userId
		)) {
			//データがなければ登録
			$default = $this->BbsPostsUser->create();
			$default['BbsPostsUser'] = array(
						'post_id' => $postId,
						'user_id' => $userId,
						'unlikes_flag' => (int)$unlikesFlag,
				);
			$this->BbsPostsUser->savePostsUsers($default);

		} else {
			$postsUsers['BbsPostsUser']['unlikes_flag'] = (int)$unlikesFlag;
			$this->BbsPostsUser->savePostsUsers($postsUsers);

		}
		$backUrl = CakeSession::read('backUrl');
		CakeSession::delete('backUrl');
		$this->redirect($backUrl);
	}

/**
 * __initPost method
 *
 * @return void
 */
	private function __initPost() {
		//新規記事データセット
		$bbsPosts = $this->BbsPost->create();

		//新規の記事名称
		$bbsPosts['BbsPost']['title'] = '新規記事_' . date('YmdHis');

		$comments = $this->Comment->getComments(
			array(
				'plugin_key' => 'bbsPosts',
				'content_key' => isset($bbsPosts['BbsPost']['key']) ? $bbsPosts['BbsPost']['key'] : null,
			)
		);
		$results['comments'] = $comments;
		$results = $this->camelizeKeyRecursive($results);
		$results['bbsPosts'] = $bbsPosts['BbsPost'];
		$results['contentStatus'] = null;
		$this->set($results);
	}

/**
 * __setPost method
 *
 * @param int $postId bbsPosts.id
 * @throws BadRequestException
 * @return void
 */
	private function __setPost($postId) {
		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];
		$conditions['id'] = $postId;

		if (! $bbsPosts = $this->BbsPost->getOnePosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$conditions
		)) {
			throw new BadRequestException(__d('net_commons', 'Bad Request'));

		}

		//いいね・よくないねを取得
		$likes = $this->BbsPostsUser->getLikes(
					$bbsPosts['BbsPost']['id'],
					$this->viewVars['userId']
				);

		//取得した記事の作成者IDからユーザ情報を取得
		$user = $this->User->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $bbsPosts['BbsPost']['created_user'],
				)
			)
		);

		$comments = $this->Comment->getComments(
			array(
				'plugin_key' => 'bbsPosts',
				'content_key' => isset($bbsPosts['BbsPost']['key']) ? $bbsPosts['BbsPost']['key'] : null,
			)
		);
		$results['comments'] = $comments;
		$results = $this->camelizeKeyRecursive($results);
		$results['bbsPosts'] = $bbsPosts['BbsPost'];
		$results['contentStatus'] = $bbsPosts['BbsPost']['status'];

		//ユーザ名、ID、いいね、よくないねをセット
		$results['bbsPosts']['username'] = $user['User']['username'];
		$results['bbsPosts']['userId'] = $user['User']['id'];
		$results['bbsPosts']['likesNum'] = $likes['likesNum'];
		$results['bbsPosts']['unlikesNum'] = $likes['unlikesNum'];
		$results['bbsPosts']['likesFlag'] = $likes['likesFlag'];
		$results['bbsPosts']['unlikesFlag'] = $likes['unlikesFlag'];
		$this->set($results);
	}

/**
 * __saveReadStatus method
 *
 * @param int $postId bbsPosts.id
 * @return void
 */
	private function __saveReadStatus($postId) {
		//既読情報がなければデータ登録
		if (! $this->BbsPostsUser->getPostsUsers(
				$postId,
				$this->viewVars['userId']
		)) {
			$default = $this->BbsPostsUser->create();
			$default['BbsPostsUser'] = array(
						'post_id' => $postId,
						'user_id' => $this->viewVars['userId'],
						'likes_flag' => false,
						'unlikes_flag' => false,
				);
			$this->BbsPostsUser->savePostsUsers($default);
		}
	}
}
