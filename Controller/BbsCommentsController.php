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
 * @param $frameId
 * @param $postId
 * @param $commentId
 * @param $currentPage
 * @param $sortParams
 * @param $visibleResponsRow
 * @param $narrowDownParams
 * @return void
 */
	public function view($frameId, $postId, $commentId, $currentPage = '',
			$sortParams = '', $visibleResponsRow = '', $narrowDownParams = '') {

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		//プラグイン名からアクション名までのurlを$baseUrlにセット
		$baseUrl = Inflector::variable($this->plugin) . '/' .
				Inflector::variable($this->name) . '/' . $this->action;
		$this->set('baseUrl', $baseUrl);

		//現在の一覧表示ページ番号をセット
		$currentPage = ($currentPage === '')? 1: (int)$currentPage;
		$this->set('currentPage', $currentPage);

		//現在のソートパラメータをセット
		$sortParams = ($sortParams === '')? '1': $sortParams;
		$this->set('sortParams', $sortParams);

		//現在の絞り込みをセット
		$narrowDownParams = ($narrowDownParams === '')? '6' : $narrowDownParams;
		$this->set('narrowDownParams', $narrowDownParams);

		//コメント表示数をセット
		$this->__setBbsSetting();

		//表示件数をセット
		$visibleResponsRow =
			($visibleResponsRow === '')? $this->viewVars['bbsSettings']['visible_comment_row'] : $visibleResponsRow;
		$this->set('currentVisibleRow', $visibleResponsRow);

		//掲示板名等をセット
		$this->__setBbs();

		//親記事情報をセット
		$this->__setPost($postId);

		//選択したコメントをセット
		$this->__setCurrentComment($commentId);

		//レスデータをセット
		$this->__setComment($commentId, $currentPage, $sortParams, $visibleResponsRow, $narrowDownParams);

		//コメント数をセットする
		//$this->viewVars['bbsCurrentComments']がセットされていること前提
		$this->setCommentNum($this->viewVars['bbsCurrentComments']);

		//コメント作成権限をセットする
		//$this->viewVars['bbses']がセットされていること前提
		$this->setCommentCreateAuth();
		var_dump($this->viewVars['roomRoleKey']);

	}

/**
 * view method
 *
 * @return void
 */
	public function add($frameId, $parentId, $postId) {
		//引用フラグをURLパラメータからセット
		$this->set('quotFlag', $this->params->query['quotFlag']);

		//掲示板名等をセット
		$this->__setBbs();

		//親記事情報をセット
		$this->__setPost($postId);

		//新規コメントデータセット
		$bbsComment = $this->BbsPost->create();
		$bbsComment['BbsPost']['title'] = '新規コメント_' . date('YmdHis');
		$results = array(
				'bbsComments' => $bbsComment['BbsPost'],
				'contentStatus' => null,
			);
		$this->set($results);

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

			//新規登録のため、データ生成
			$bbsComment = $this->BbsPost->create(['key' => Security::hash('bbsPost' . mt_rand() . microtime(), 'md5')]);
			//初期化
			$bbsComment['BbsPost']['bbs_key'] = $data['Bbs']['key'];
			$data = Hash::merge($bbsComment, $data);

			if (!$bbsComment = $this->BbsPost->savePost($data)) {
				if (!$this->__handleValidationError($this->BbsPost->validationErrors)) {
					return;
				}
			}

			//親記事のコメント数の更新処理(公開中のコメント数のみカウントする)
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
			$comments = $this->BbsPost->getPosts(
					$this->viewVars['userId'],
					false,
					false,
					false,
					false,
					false,
					$conditions
				);

			//Treeビヘイビアでコメント数を取得
			$parentPosts['BbsPost']['comment_num'] = count($comments);
			$parentPosts['Bbs']['key'] = $data['Bbs']['key'];
			if (!$bbsComment = $this->BbsPost->savePost($parentPosts)) {
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
			$conditions['id'] = $postId;
			$bbsComment = $this->BbsPost->getOnePosts(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$conditions
					);

			//更新時間をセット
			$bbsComment['BbsPost']['modified'] = date('Y-m-d H:i:s');
			$data = Hash::merge($bbsComment, $data);

			//Todo:UPDATE
			$data['BbsPost']['id'] = $bbsComment['BbsPost']['id'];

			if (!$bbsComment = $this->BbsPost->savePost($data)) {
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
	public function delete($frameId, $postId, $parentId, $commentId = '') {
		if (! $this->request->isPost()) {
			return;
		}

		if (!$bbsPost = $this->BbsPost->delete(
				($commentId)? $commentId : $parentId
		)) {
			if (!$this->__handleValidationError($this->BbsPost->validationErrors)) {
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
				$this->viewVars['blockId']
			);

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
		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];
		$conditions['id'] = $postId;

		if (! $bbsPosts = $this->BbsPost->getOnePosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$conditions
		)) {
			//return;
		}
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
		$this->set($results);
	}

/**
 * Set current comment method
 *
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
			return;
		}
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
		$this->set($results);
	}

/**
 * __setComment method
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
		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];

		//Treeビヘイビアのlft,rghtカラムを利用して対象記事のコメントのみ取得
		$conditions['and']['lft >'] = $this->viewVars['bbsCurrentComments']['lft'];
		$conditions['and']['rght <'] = $this->viewVars['bbsCurrentComments']['rght'];

		$bbsCommnets = $this->BbsPost->getPosts(
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

		//前のページがあるか取得
		if ($currentPage === 1) {
			$this->set('hasPrevPage', false);
		} else {
			$prevPage = $currentPage - 1;
			$prevPosts = $this->BbsPost->getPosts(
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
		$nextPosts = $this->BbsPost->getPosts(
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
		$nextSecondPosts = $this->BbsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,			//order by指定
				$visibleCommentRow,	//limit指定
				$nextSecondPage,	//2ページ先の番号指定
				$conditions
			);
		$hasNextSecondPage = (empty($nextSecondPosts))? false : true;
		$this->set('hasNextSecondPage', $hasNextSecondPage);

		//4ページがあるか取得（モックとしてとりあえず）
		$posts = $this->BbsPost->getPosts(
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
		$posts = $this->BbsPost->getPosts(
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
	}

}
