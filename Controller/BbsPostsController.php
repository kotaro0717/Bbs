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
	public function view($frameId, $postId, $currentPage = '', $sortParams = '',
							$visibleCommentRow = '', $narrowDownParams = '') {

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		//プラグイン名からアクション名までのurlを$baseUrlにセット
		$baseUrl = Inflector::variable($this->plugin) . '/' .
				Inflector::variable($this->name) . '/' . $this->action;
		$this->set('baseUrl', $baseUrl);

		//現在の一覧表示ページ番号をセット
		$currentPage = ($currentPage === '')? 1 : (int)$currentPage;
		$this->set('currentPage', $currentPage);

		//現在のソートパラメータをセット
		$sortParams = ($sortParams === '')? '1' : $sortParams;
		$this->set('sortParams', $sortParams);

		//現在の絞り込みをセット
		$narrowDownParams = ($narrowDownParams === '')? '6' : $narrowDownParams;
		$this->set('narrowDownParams', $narrowDownParams);

		$this->__setBbsSetting();

		//表示件数をセット
		$visibleCommentRow =
			($visibleCommentRow === '')?
				$this->viewVars['bbsSettings']['visible_comment_row'] : $visibleCommentRow;

		$this->set('currentVisibleRow', $visibleCommentRow);

		//掲示板名等をセット
		$this->__setBbs();

		//選択した記事をセット
		$this->__setPost($postId);

		//記事に関するコメントをセット
		$this->__setComment($postId, $currentPage, $sortParams, $visibleCommentRow, $narrowDownParams);

		//既読情報を登録
		$this->__saveReadStatus($postId);
	}

/**
 * add method
 *
 * @return void
 */
	public function add($frameId) {
		//掲示板名を取得
		$this->__setBbs();
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		if ($this->request->isGet()) {
			$referer = $this->request->referer();
			if (! strstr($referer, '/bbses')) {
				CakeSession::write('backUrl', $this->request->referer());
			}

			//新規記事データセット
			$bbsPost = $this->BbsPost->create();
			$bbsPost['BbsPost']['title'] = '新規記事_' . date('YmdHis');
			$results = array(
					'bbsPosts' => $bbsPost['BbsPost'],
					'contentStatus' => null,
				);
			$this->set($results);
		}

        if ($this->request->isPost()) {
			if (!$status = $this->__parseStatus()) {
				return;
			}

			$data = Hash::merge(
				$this->data,
				['BbsPost' => ['status' => $status]]
			);

			//新規登録のため、データ生成
			$bbsPost = $this->BbsPost->create(['key' => Security::hash('bbsPost' . mt_rand() . microtime(), 'md5')]);
			$bbsPost['BbsPost']['bbs_id'] = '';
			$data = Hash::merge($bbsPost, $data);

			if (!$bbsPost = $this->BbsPost->savePost($data)) {
				if (!$this->__handleValidationError($this->BbsPost->validationErrors)) {
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
 * @return void
 */
	public function edit($frameId, $postId) {
		//掲示板名を取得
		$this->__setBbs();

		//編集する記事を取得
		$this->__setPost($postId);

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

        if ($this->request->isPost()) {
			if (!$status = $this->__parseStatus()) {
				return;
			}

			$data = Hash::merge(
				$this->data,
				['BbsPost' => ['status' => $status]]
			);

			//編集データ取得
			$bbsPost = $this->BbsPost->getPosts(
					$data['Bbs']['key'],
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$postId,
					null,
					null,
					null
					);
			//編集者のIDを格納
			//$bbsPost['BbsPost']['created_user'] = $data['User']['id'];

			//更新時間をセット
			$bbsPost['BbsPost']['modified'] = date('Y-m-d H:i:s');
			$data = Hash::merge($bbsPost, $data);

			//Todo:UPDATE
			$data['BbsPost']['id'] = $bbsPost['BbsPost']['id'];

			if (!$bbsPost = $this->BbsPost->savePost($data)) {
				if (!$this->__handleValidationError($this->BbsPost->validationErrors)) {
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
 * @param string $postId postId
 * @throws NotFoundException
 * @return void
 */
	public function delete($frameId, $postId) {
		if (! $this->request->isPost()) {
			return;
		}
		if (!$bbsPost = $this->BbsPost->delete($postId)) {
			if (!$this->__handleValidationError($this->BbsPost->validationErrors)) {
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
 * Parse content status from request
 *
 * @throws BadRequestException
 * @return mixed status on success, false on error
 */
	private function __parseStatus() {
		if ($matches = preg_grep('/^save_\d/', array_keys($this->data))) {
			list(, $status) = explode('_', array_shift($matches));
		} else {
			if ($this->request->is('ajax')) {
				$this->renderJson(
					['error' => ['validationErrors' => ['status' => __d('net_commons', 'Invalid request.')]]],
					__d('net_commons', 'Bad Request'), 400
				);
			} else {
				throw new BadRequestException(__d('net_commons', 'Bad Request'));
			}
			return false;
		}
		return $status;
	}

/**
 * Handle validation error
 *
 * @param array $errors validation errors
 * @return bool true on success, false on error
 */
	private function __handleValidationError($errors) {
		if (is_array($errors)) {
			$this->validationErrors = $errors;
			if ($this->request->is('ajax')) {
				$results = ['error' => ['validationErrors' => $errors]];
				$this->renderJson($results, __d('net_commons', 'Bad Request'), 400);
			}
			return false;
		}
		return true;
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
		$this->set($results);

	}

/**
 * __initBbs method
 *
 * @return void
 */
	private function __setBbs() {
		//ログインユーザIDを取得し、Viewにセット
		$this->set('userId', $this->Session->read('Auth.User.id'));

		//掲示板データを取得
		$bbses = $this->Bbs->getBbs(
				$this->viewVars['blockId'],
				$this->viewVars['userId'],
				$this->viewVars['contentCreatable'],
				$this->viewVars['contentEditable'],
				false	//記事一覧ではない
			);

		//Viewにセット
		$this->set(array(
			'bbses' => $bbses['Bbs']
		));
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setPost($postId) {
		//選択した記事を一件取得
		$bbsPosts = $this->BbsPost->getPosts(
				$this->viewVars['bbses']['key'],
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$postId,	//選択された記事のId
				null,
				null,
				null
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
			'contentStatus' => $bbsPosts['BbsPost']['status'],
		);

		//取得した記事の配列にユーザ名、ID、コメント数を追加
		$results['bbsPosts']['username'] = $user['User']['username'];
		$results['bbsPosts']['user_id'] = $user['User']['id'];
		$this->set($results);
	}

/**
 * __setPost method
 *
 * @param $postId
 * @param $currentPage
 * @param $sortParams
 * @param $visibleCommentRow
 * @param $narrowDownParams
 * @return void
 */
	private function __setComment($postId, $currentPage, $sortParams,
									$visibleCommentRow, $narrowDownParams) {

		//ソート条件をセット
		$sortOrder = $this->setSortOrder($sortParams);

		//絞り込み条件をセット
		$conditions = $this->setNarrowDown($narrowDownParams);
		$conditions['bbs_key'] =	$this->viewVars['bbses']['key'];
		//Treeビヘイビアのlft,rghtカラムを利用して対象記事のコメントのみ取得
		$conditions['or']['and']['lft >'] = $this->viewVars['bbsPosts']['lft'];
		$conditions['or']['and']['rght <'] = $this->viewVars['bbsPosts']['rght'];

		$bbsCommnets = $this->BbsPost->getComments(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,			//order by指定
				$visibleCommentRow,	//limit指定
				$currentPage,		//ページ番号指定
				$conditions
			);

		//コメントなしの場合
		if (empty($bbsCommnets)) {
			//空配列を渡す
			//Todo:空ではなく、falseを渡して、Viewでは「空ならメッセージ」に修正
			$this->set('bbsComments', array());
			$this->set('commentNum', 0);
			return;
		}

		foreach ($bbsCommnets as $bbsComment) {
			//取得した記事の作成者IDからユーザ情報を取得
			$user = $this->User->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $bbsComment['BbsPost']['created_user'],
					)
				)
			);
			//取得した記事の配列にユーザ名を追加
			$bbsComment['BbsPost']['username'] = $user['User']['username'];
			$bbsComment['BbsPost']['userId'] = $user['User']['id'];
			$results[] = $bbsComment['BbsPost'];
		}
		$this->set('bbsComments', $results);
		//コメント数をセット
		$this->set('commentNum', count($results));

		//前のページがあるか取得
		if ($currentPage === 1) {
			$this->set('hasPrevPage', false);
		} else {
			$prevPage = $currentPage - 1;
			$prevPosts = $this->BbsPost->getComments(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,			//order by指定
					$visibleCommentRow,	//limit指定
					$prevPage,			//前のページ番号指定
					$conditions
				);
			$hasPrevPage = (empty($prevPosts))? false : true;
			$this->set('hasPrevPage', $hasPrevPage);
		}

		//次のページがあるか取得
		$nextPage = $currentPage + 1;
		$nextPosts = $this->BbsPost->getComments(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,			//order by指定
				$visibleCommentRow,	//limit指定
				$nextPage,			//次のページ番号指定
				$conditions
			);
		$hasNextPage = (empty($nextPosts))? false : true;
		$this->set('hasNextPage', $hasNextPage);

		//2ページ先のページがあるか取得
		$nextSecondPage = $currentPage + 2;
		$nextSecondPosts = $this->BbsPost->getComments(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,			//order by指定
				$visibleCommentRow,	//limit指定
				$nextSecondPage,		//2ページ先の番号指定
				$conditions
			);
		$hasNextSecondPage = (empty($nextSecondPosts))? false : true;
		$this->set('hasNextSecondPage', $hasNextSecondPage);

		//1,2ページの時のみ4,5ページがあるかどうか取得（モックとしてとりあえず）
		//if ($currentPage === 1 || $currentPage === 2) {
			//4ページがあるか取得（モックとしてとりあえず）
			$posts = $this->BbsPost->getComments(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,			//order by指定
					$visibleCommentRow,	//limit指定
					4,					//4ページ先の番号指定
					$conditions
				);
			$hasFourPage = (empty($posts))? false : true;
			$this->set('hasFourPage', $hasFourPage);

			//5ページがあるか取得（モックとしてとりあえず）
			$posts = $this->BbsPost->getComments(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,			//order by指定
					$visibleCommentRow,	//limit指定
					5,					//5ページ先の番号指定
					$conditions
				);
			$hasFivePage = (empty($posts))? false : true;
			$this->set('hasFivePage', $hasFivePage);
		//}
	}

/**
 * __saveReadStatus method
 *
 * @return void
 */
	private function __saveReadStatus($postId) {
		//既読情報がなければデータ登録
		if (! $readStatus = $this->BbsPostsUser->getReadPostStatus(
				$postId,
				$this->viewVars['userId']
		)) {
			$default = $this->BbsPostsUser->create();
			$default['BbsPostsUser'] = array(
						'post_id' => $postId,
						'user_id' => $this->viewVars['userId'],
				);
			$results = $this->BbsPostsUser->saveReadStatus($default);
		}
	}

}
