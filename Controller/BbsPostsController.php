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
	public function view($frameId, $postId, $currentPage = '', $sortParams = '', $visibleCommentRow = '') {
		$this->view = 'BbsPosts/view';

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

		$this->__setBbsSetting();
		if (!isset($this->viewVars['bbsSettings'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//表示件数を設定
		$visibleCommentRow =
			($visibleCommentRow === '')? $this->viewVars['bbsSettings']['visible_comment_row'] : $visibleCommentRow;
		$this->set('currentVisibleRow', $visibleCommentRow);

		$this->__setBbs();
		if (!isset($this->viewVars['bbses'])) {
			debug(2);
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//選択した記事をセット
		$this->__setPost($postId);
		if (!isset($this->viewVars['bbsPosts'])) {
			debug(3);
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//記事に関するコメントをセット
		$this->__setComment($postId, $currentPage, $sortParams, $visibleCommentRow);
		if (!isset($this->viewVars['bbsComments'])) {
			debug(4);
			//throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

	}

/**
 * view method
 *
 * @return void
 */
	public function add($frameId, $postId = '', $postFlag = '') {
		$this->view = 'BbsPosts/viewForAdd';
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

        if ($this->request->is('post')) {
            $this->BbsPost->create();
			debug($this->viewVars['bbses']);
			debug($this->viewVars['bbsPosts']);

//            if ($this->BbsPost->save($this->request->data)) {
//                $this->Session->setFlash(__('Your post has been saved.'));
//                return $this->redirect(array('action' => 'index'));
//            }
            $this->Session->setFlash(__('Unable to add your post.'));
        }
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit($frameId, $postId) {
		$this->view = 'BbsPosts/viewForAdd';
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

	}

	/**
	 * form method
	 *
	 * @param int $frameId frames.id
	 * @return CakeResponse A response object containing the rendered view.
	 */
	   public function form($frameId = 0) {
		   $this->view($frameId);
		   return $this->render('BbsPostEdit/form', false);
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
				$this->viewVars['bbses']['id'],
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$postId,
				null,
				1,		//CONST化する?
				null
			);

		//取得した記事の作成者IDからユーザ情報を取得
		$user = $this->User->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					//[0]は気持ち悪い
					'id' => $bbsPosts[0]['BbsPost']['created_user'],
				)
			)
		);

		//TODO:↓きれいに整理できるはず。
			$results = array(
				//[0]は気持ち悪い
				'bbsPosts' => $bbsPosts[0]['BbsPost'],
			);
			//取得した記事の配列にユーザ名を追加
			$results['bbsPosts']['username'] = $user['User']['username'];
			$this->set($results);
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setComment($postId, $currentPage, $sortParams, $visibleCommentRow) {
		//ソート条件をセット
		$sortOrder = $this->__setSortOrder($sortParams);

		//BbsPost->find
		$bbsCommnets = $this->BbsPost->getComments(
				$this->viewVars['bbses']['id'],
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$postId,
				$sortOrder,			//order by指定
				$visibleCommentRow,	//limit指定
				$currentPage		//ページ番号指定
			);

		//コメントなしの場合
		if (empty($bbsCommnets)) {
			return $this->set('bbsComments', array());
		}

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
			//取得した記事の配列にユーザ名を追加
			$bbsComment['BbsPost']['username'] = $user['User']['username'];
			$results[] = $bbsComment['BbsPost'];
		}
		$this->set('bbsComments', $results);

		//前のページがあるか取得
		if ($currentPage === 1) {
			$this->set('hasPrevPage', false);
		} else {
			$prevPage = $currentPage - 1;
			$prevPosts = $this->BbsPost->getComments(
					$this->viewVars['bbses']['id'],
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$postId,			//欲しい記事のID指定
					$sortOrder,			//order by指定
					$visibleCommentRow,	//limit指定
					$prevPage			//前のページ番号指定
				);
			$hasPrevPage = (empty($prevPosts))? false : true;
			$this->set('hasPrevPage', $hasPrevPage);
		}

		//次のページがあるか取得
		$nextPage = $currentPage + 1;
		$nextPosts = $this->BbsPost->getComments(
				$this->viewVars['bbses']['id'],
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$postId,			//欲しい記事のID指定
				$sortOrder,			//order by指定
				$visibleCommentRow,	//limit指定
				$nextPage			//次のページ番号指定
			);
		$hasNextPage = (empty($nextPosts))? false : true;
		$this->set('hasNextPage', $hasNextPage);

		//2ページ先のページがあるか取得
		$nextSecondPage = $currentPage + 2;
		$nextSecondPosts = $this->BbsPost->getComments(
				$this->viewVars['bbses']['id'],
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$postId,			//欲しい記事のID指定
				$sortOrder,			//order by指定
				$visibleCommentRow,	//limit指定
				$nextSecondPage		//2ページ先の番号指定
			);
		$hasNextSecondPage = (empty($nextSecondPosts))? false : true;
		$this->set('hasNextSecondPage', $hasNextSecondPage);

		//1,2ページの時のみ4,5ページがあるかどうか取得（モックとしてとりあえず）
		//if ($currentPage === 1 || $currentPage === 2) {
			//4ページがあるか取得（モックとしてとりあえず）
			$posts = $this->BbsPost->getComments(
					$this->viewVars['bbses']['id'],
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$postId,			//欲しい記事のID指定
					$sortOrder,			//order by指定
					$visibleCommentRow,	//limit指定
					4					//4ページ先の番号指定
				);
			$hasFourPage = (empty($posts))? false : true;
			$this->set('hasFourPage', $hasFourPage);

			//5ページがあるか取得（モックとしてとりあえず）
			$posts = $this->BbsPost->getComments(
					$this->viewVars['bbses']['id'],
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$postId,			//欲しい記事のID指定
					$sortOrder,			//order by指定
					$visibleCommentRow,	//limit指定
					5					//5ページ先の番号指定
				);
			$hasFivePage = (empty($posts))? false : true;
			$this->set('hasFivePage', $hasFivePage);
		//}
	}

/**
 * __setPost method
 *
 * @return void
 */
//	private function __setPostUser($postId) {
//		$user = $this->User->find('first', array(
//				'recursive' => -1,
//				'conditions' => array(
//					'id' => $this->viewVars['bbsPosts']['createdUser'],
//				)
//			)
//		);
//
//		$results = array(
//			'users' => $user['User'],
//		);
//		return $this->set($this->camelizeKeyRecursive($results));
//	}

/**
 * __setPost method
 *
 * @param $sortParams
 * @return string
 */
	private function __setSortOrder($sortParams) {
		switch ($sortParams) {
		case '1':
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
