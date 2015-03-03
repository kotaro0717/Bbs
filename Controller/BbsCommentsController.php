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
	public $useTable = array(
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
 * @param int $frameId frames.id
 * @param int $postId bbsPosts.id
 * @param int $commentId bbsPosts.id
 * @param int $currentPage currentPage
 * @param int $sortParams sortParameter
 * @param int $visibleCommentRow visibleCommentRow
 * @param int $narrowDownParams narrowDownParameter
 * @throws BadRequestException throw new
 * @return void
 */
	public function view($frameId, $postId = '', $commentId = '', $currentPage = '',
				$sortParams = '', $visibleCommentRow = '', $narrowDownParams = '') {
		if (! $postId || ! $commentId) {
			BadRequestException(__d('net_commons', 'Bad Request'));
		}

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		//コメント表示数/掲示板名等をセット
		$this->setBbs();

		//親記事情報をセット
		$this->__setPost($postId);

		//選択したコメントをセット
		$this->__setCurrentComment($commentId);

		//各パラメータをセット
		$this->initParams($currentPage, $sortParams, $narrowDownParams);

		//表示件数をセット
		$visibleCommentRow =
			($visibleCommentRow === '')? $this->viewVars['bbsSettings']['visible_comment_row'] : $visibleCommentRow;
		$this->set('currentVisibleRow', $visibleCommentRow);

		//Treeビヘイビアのlft,rghtカラムを利用して対象記事のコメントのみ取得
		$conditions['and']['lft >'] = $this->viewVars['bbsCurrentComments']['lft'];
		$conditions['and']['rght <'] = $this->viewVars['bbsCurrentComments']['rght'];
		//レスデータをセット
		$this->setComment($conditions);

		//Treeビヘイビアのlft,rghtカラムを利用して対象記事のコメントのみ取得
		$conditions['and']['lft >'] = $this->viewVars['bbsCurrentComments']['lft'];
		$conditions['and']['rght <'] = $this->viewVars['bbsCurrentComments']['rght'];
		//ページング情報取得
		$this->setPagination($conditions, $commentId);

		//コメント数をセットする
		$this->setCommentNum(
				$this->viewVars['bbsCurrentComments']['lft'],
				$this->viewVars['bbsCurrentComments']['rght']
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
	}

/**
 * view method
 *
 * @param int $frameId frames.id
 * @param int $parentId bbsPosts.id
 * @param int $postId bbsPosts.id
 * @return void
 */
	public function add($frameId, $parentId, $postId) {
		//引用フラグをURLパラメータからセット
		$this->set('quotFlag', $this->params->query['quotFlag']);

		$this->setBbs();

		$this->__setPost($postId);

		$this->__initComment();

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		if (! $this->request->isPost()) {
			return;
		}

		if (! $status = $this->parseStatus()) {
			return;
		}

		$data = $this->setAddSaveData($this->data, $status, $parentId);

		if (! $this->BbsPost->saveComment($data)) {
			debug('通過');
			if (!$this->handleValidationError($this->BbsPost->validationErrors)) {
				return;
			}
		}

		//親記事の更新処理(公開中のコメント数、コメント番号)
		if (! $this->__updateParentPosts($data['Bbs']['key'], $parentId)) {
			return;
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
		$this->setBbs();

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

		if (! $this->BbsPost->saveComment($data)) {
			if (! $this->handleValidationError($this->BbsPost->validationErrors)) {
				return;
			}
		}

		if (!$this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}

/**
 * delete method
 *
 * @param int $frameId frames.id
 * @param int $postId bbsPosts.id
 * @param int $parentId bbsPosts.id
 * @param int $commentId bbsPosts.id
 * @return void
 */
	public function delete($frameId, $postId, $parentId, $commentId = '') {
		if (! $this->request->isPost()) {
			return;
		}

		if ($this->BbsPost->delete(($commentId)? $commentId : $parentId)) {

			$backUrl = array(
				'controller' => ($commentId)? 'bbsComments' : 'bbsPosts',
				'action' => 'view',
				$frameId,
				$postId,
				($commentId)? $parentId : '',
			);

			//記事一覧orコメント一覧へリダイレクト
			$this->redirect($backUrl);
		}

		if (! $this->handleValidationError($this->BbsPost->validationErrors)) {
			return;
		}
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

		//取得した記事の作成者IDからユーザ情報を取得
		$user = $this->User->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $bbsPosts['BbsPost']['created_user'],
				)
			)
		);

		//いいね・よくないねを取得
		$likes = $this->BbsPostsUser->getLikes(
					$bbsPosts['BbsPost']['id'],
					$this->viewVars['userId']
				);

		$results = array(
			'bbsPosts' => $bbsPosts['BbsPost'],
			'contentStatus' => $bbsPosts['BbsPost']['status']
		);

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
 * __initPost method
 *
 * @return void
 */
	private function __initComment() {
		//新規記事データセット
		$comment = $this->BbsPost->create();

		//新規の記事名称
		$comment['BbsPost']['title'] = '新規コメント_' . date('YmdHis');

		$results = array(
				'bbsComments' => $comment['BbsPost'],
				'contentStatus' => null,
			);
		$this->set($results);
	}

/**
 * Set current comment method
 *
 * @param int $postId bbsPosts.id
 * @throws BadRequestException
 * @return void
 */
	private function __setCurrentComment($postId) {
		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];
		$conditions['id'] = $postId;

		if (! $currentPosts = $this->BbsPost->getOnePosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$conditions
		)) {
			throw new BadRequestException(__d('net_commons', 'Bad Request'));

		}

		$conditions = '';
		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];
		$conditions['id'] = $currentPosts['BbsPost']['parent_id'];
		//親記事の記事取得
		$parentPosts = $this->BbsPost->getOnePosts(
			$this->viewVars['userId'],
			$this->viewVars['contentEditable'],
			$this->viewVars['contentCreatable'],
			$conditions
		);

		//いいね・よくないねを取得
		$likes = $this->BbsPostsUser->getLikes(
					$currentPosts['BbsPost']['id'],
					$this->viewVars['userId']
				);

		//取得した記事の作成者IDからユーザ情報を取得
		$user = $this->User->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $currentPosts['BbsPost']['created_user'],
				)
			)
		);

		$results = array(
			'bbsCurrentComments' => $currentPosts['BbsPost'],
			'currentCommentStatus' => $currentPosts['BbsPost']['status']
		);
		//取得した情報を配列に追加
		$results['bbsCurrentComments']['parent_comment_index'] = $parentPosts['BbsPost']['comment_index'];
		$results['bbsCurrentComments']['username'] = $user['User']['username'];
		$results['bbsCurrentComments']['userId'] = $user['User']['id'];
		$results['bbsCurrentComments']['likesNum'] = $likes['likesNum'];
		$results['bbsCurrentComments']['unlikesNum'] = $likes['unlikesNum'];
		$results['bbsCurrentComments']['likesFlag'] = $likes['likesFlag'];
		$results['bbsCurrentComments']['unlikesFlag'] = $likes['unlikesFlag'];
		$this->set($results);
	}

/**
 * updateParentPosts
 *
 * @param string $bbsKey bbses.key
 * @param int $parentId bbsPosts.id
 * @return true is save successful, or false is failure
 */
	private function __updateParentPosts($bbsKey, $parentId) {
		//親記事(lft,rghtカラム)取得
		$conditions['bbs_key'] = $bbsKey;
		$conditions['id'] = $parentId;
		$parentPosts = $this->BbsPost->getOnePosts(
				false,
				false,
				false,
				$conditions
			);

		//条件初期化
		$conditions = null;

		//コメント一覧取得
		$conditions['bbs_key'] = $bbsKey;
		$conditions['and']['lft >'] = $parentPosts['BbsPost']['lft'];
		$conditions['and']['rght <'] = $parentPosts['BbsPost']['rght'];
		//公開されているコメントを取得
		$bbsComments = $this->BbsPost->getPosts(
				false,
				false,
				false,
				false,
				false,
				false,
				$conditions
			);

		$parentPosts['BbsPost']['comment_num'] = count($bbsComments);
		$parentPosts['BbsPost']['comment_index'] = ++$parentPosts['BbsPost']['comment_index'];
		$parentPosts['Bbs']['key'] = $bbsKey;
		$parentPosts['Comment']['comment'] = '';

		if (! $this->BbsPost->savePost($parentPosts)) {
			if (! $this->handleValidationError($this->BbsPost->validationErrors)) {
				return false;
			}
		}

		return true;
	}
}
