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
 * @param int $visibleRow visibleRow
 * @param int $narrowDownParams narrowDownParameter
 * @return void
 */
	public function view($frameId, $postId, $currentPage = '', $sortParams = '',
							$visibleRow = '', $narrowDownParams = '') {
		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		//コメント表示数をセット
		$this->setBbsSetting();

		//各パラメータをセット
		$this->initParams();

		//掲示板名等をセット
		$this->setBbs();

		//選択した記事をセット
		$this->__setPost($postId);

		//Treeビヘイビアのlft,rghtカラムを利用して対象記事のコメントのみ取得
		$conditions['and']['lft >'] = $this->viewVars['bbsPosts']['lft'];
		$conditions['and']['rght <'] = $this->viewVars['bbsPosts']['rght'];
		//記事に関するコメントをセット
		$this->setComment($postId, $currentPage, $sortParams, $visibleRow, $narrowDownParams, $conditions);

		//コメント数をセットする
		$this->setCommentNum(
				$this->viewVars['bbsPosts']['lft'],
				$this->viewVars['bbsPosts']['rght']
			);

		//コメント作成権限をセットする
		$this->setCommentCreateAuth();

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
			$referer = $this->request->referer();
			if (! strstr($referer, '/bbses')) {
				CakeSession::write('backUrl', $this->request->referer());
			}
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
			$bbsPost = $this->BbsPost->create(['key' => Security::hash('bbsPost' . mt_rand() . microtime(), 'md5')]);
			$bbsPost['BbsPost']['bbs_key'] = $data['Bbs']['key'];
			$data = Hash::merge($bbsPost, $data);

			if (! $bbsPost = $this->BbsPost->savePost($data)) {
				if (!$this->handleValidationError($this->BbsPost->validationErrors)) {
					return;
				}
			}

			if (! $this->request->is('ajax')) {
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
			$bbsPosts = $this->BbsPost->getOnePosts(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$conditions
				);

			//更新時間をセット
			$bbsPosts['BbsPost']['modified'] = date('Y-m-d H:i:s');
			$data = Hash::merge($bbsPosts, $data);

			//UPDATE
			$data['BbsPost']['id'] = $bbsPosts['BbsPost']['id'];

			if (!$bbsPosts = $this->BbsPost->savePost($data)) {
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
 * @param int $postId postId
 * @return void
 */
	public function delete($frameId, $postId) {
		if (! $this->request->isPost()) {
			return;
		}
		if (!$bbsPost = $this->BbsPost->delete($postId)) {
			if (!$this->handleValidationError($this->BbsPost->validationErrors)) {
				return;
			}
		}

		$backUrl = array(
				'controller' => 'bbses',
				'action' => 'view',
				$frameId,
			);

		//記事一覧へリダイレクト
		$this->redirect($backUrl);
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

		$results = array(
				'bbsPosts' => $bbsPosts['BbsPost'],
				'contentStatus' => null,
			);
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
			'contentStatus' => $bbsPosts['BbsPost']['status'],
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
			$results = $this->BbsPostsUser->savePostsUsers($default);
		}
	}

}
