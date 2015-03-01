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
 * @param int $visibleRow visibleRow
 * @param int $narrowDownParams narrowDownParameter
 * @throws BadRequestException throw new
 * @return void
 */
	public function view($frameId, $postId = '', $commentId = '', $currentPage = '',
				$sortParams = '', $visibleRow = '', $narrowDownParams = '') {
		if (! $postId || ! $commentId) {
			BadRequestException(__d('net_commons', 'Bad Request'));
		}

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		//コメント表示数をセット
		$this->setBbsSetting();

		//各パラメータをセット
		$this->initParams();

		//掲示板名等をセット
		$this->setBbs();

		//親記事情報をセット
		$this->__setPost($postId);

		//選択したコメントをセット
		$this->__setCurrentComment($commentId);

		//Treeビヘイビアのlft,rghtカラムを利用して対象記事のコメントのみ取得
		$conditions['and']['lft >'] = $this->viewVars['bbsCurrentComments']['lft'];
		$conditions['and']['rght <'] = $this->viewVars['bbsCurrentComments']['rght'];
		//レスデータをセット
		$this->setComment($commentId, $currentPage, $sortParams, $visibleRow, $narrowDownParams, $conditions);

		//コメント数をセットする
		$this->setCommentNum(
				$this->viewVars['bbsCurrentComments']['lft'],
				$this->viewVars['bbsCurrentComments']['rght']
			);

		//コメント作成権限をセットする
		$this->setCommentCreateAuth();
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

		//掲示板名等をセット
		$this->setBbs();

		//親記事情報をセット
		$this->__setPost($postId);

		//記事情報をセット
		$this->__initComment();

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		if ($this->request->isPost()) {
			if (!$status = $this->parseStatus()) {
				return;
			}

			$data = Hash::merge(
				$this->data,
				['BbsPost' => ['status' => $status]]
			);

			//新規登録のため、データ生成
			$comment = $this->BbsPost->create(['key' => Security::hash('bbsPost' . mt_rand() . microtime(), 'md5')]);
			//初期化
			$comment['BbsPost']['bbs_key'] = $data['Bbs']['key'];
			$data = Hash::merge($comment, $data);

			if (!$comment = $this->BbsPost->saveComment($data)) {
				if (!$this->handleValidationError($this->BbsPost->validationErrors)) {
					return;
				}
			}

			//親記事のコメント数の更新処理(公開中のコメント数のみカウント)
			//親記事(lft,rghtカラム)取得
			$conditions['bbs_key'] = $data['Bbs']['key'];
			$conditions['id'] = $parentId;
			$parentPosts = $this->BbsPost->getOnePosts(
					false,
					false,
					false,
					$conditions
				);

			//条件初期化
			$conditions = null;
			//コメント一覧取得（page,limit,order等の指定しない）
			$conditions['bbs_key'] = $parentPosts['BbsPost']['bbs_key'];
			$conditions['and']['lft >'] = $parentPosts['BbsPost']['lft'];
			$conditions['and']['rght <'] = $parentPosts['BbsPost']['rght'];
			$bbsComments = $this->BbsPost->getPosts(
					$this->viewVars['userId'],
					false,
					false,
					false,
					false,
					false,
					$conditions
				);

			$parentPosts['BbsPost']['comment_num'] = count($bbsComments);
			$parentPosts['Bbs']['key'] = $data['Bbs']['key'];
			$parentPosts['Comment']['comment'] = '';
			if (! $bbsComment = $this->BbsPost->savePost($parentPosts)) {
				if (!$this->handleValidationError($this->BbsPost->validationErrors)) {
					return;
				}
			}

			if (!$this->request->is('ajax')) {
				$backUrl = CakeSession::read('backUrl');
				CakeSession::delete('backUrl');
				$this->redirect($backUrl);
			}
			return;
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

		if ($this->request->isPost()) {
			if (!$status = $this->parseStatus()) {
				return;
			}

			$data = Hash::merge(
				$this->data,
				['BbsPost' => ['status' => $status]]
			);

			//編集データ取得
			$conditions['bbs_key'] = $data['Bbs']['key'];
			$conditions['id'] = $postId;
			$comments = $this->BbsPost->getOnePosts(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$conditions
				);

			//更新時間をセット
			$comments['BbsPost']['modified'] = date('Y-m-d H:i:s');
			$data = Hash::merge($comments, $data);

			//UPDATE
			$data['BbsPost']['id'] = $comments['BbsPost']['id'];

			if (!$comments = $this->BbsPost->saveComment($data)) {
				if (!$this->handleValidationError($this->BbsPost->validationErrors)) {
					return;
				}
			}

			if (!$this->request->is('ajax')) {
				$backUrl = CakeSession::read('backUrl');
				CakeSession::delete('backUrl');
				$this->redirect($backUrl);
			}
			return;
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

		if (!$bbsPost = $this->BbsPost->delete(
				($commentId)? $commentId : $parentId
		)) {
			if (!$this->handleValidationError($this->BbsPost->validationErrors)) {
				return;
			}
		}

		$backUrl = array(
				'controller' => ($commentId)? 'bbsComments' : 'bbsPosts',
				'action' => 'view',
				$frameId,
				$postId,
				($commentId)? $parentId : '',
			);

		$this->redirect($backUrl);
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

		$results = array(
			'bbsPosts' => $bbsPosts['BbsPost'],
			'contentStatus' => $bbsPosts['BbsPost']['status']
		);

		//取得した記事の配列にユーザ名を追加
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

		if (! $posts = $this->BbsPost->getOnePosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$conditions
		)) {
			throw new BadRequestException(__d('net_commons', 'Bad Request'));

		}

		//いいね・よくないねを取得
		$likes = $this->BbsPostsUser->getLikes(
					$posts['BbsPost']['id'],
					$this->viewVars['userId']
				);

		//取得した記事の作成者IDからユーザ情報を取得
		$user = $this->User->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $posts['BbsPost']['created_user'],
				)
			)
		);

		$results = array(
			'bbsCurrentComments' => $posts['BbsPost'],
			'currentCommentStatus' => $posts['BbsPost']['status']
		);
		$results['bbsCurrentComments']['username'] = $user['User']['username'];
		$results['bbsCurrentComments']['userId'] = $user['User']['id'];
		$results['bbsCurrentComments']['likesNum'] = $likes['likesNum'];
		$results['bbsCurrentComments']['unlikesNum'] = $likes['unlikesNum'];
		$results['bbsCurrentComments']['likesFlag'] = $likes['likesFlag'];
		$results['bbsCurrentComments']['unlikesFlag'] = $likes['unlikesFlag'];
		$this->set($results);
	}

}
