<?php
/**
 * Bbses Controller
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
class BbsesController extends BbsesAppController {

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Frames.Frame',
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
				'contentPublishable' => array('edit'),
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
 * index method
 *
 * @return void
 */
	public function index($frameId, $currentPage = '', $sortParams = '', $visiblePostRow = '') {
		$this->view = 'Bbses/view';
		$this->view($frameId, $currentPage, $sortParams, $visiblePostRow);
	}

/**
 * index method
 *
 * @return void
 */
	public function view($frameId, $currentPage = '', $sortParams = '', $visiblePostRow = '') {
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

		//BbsFrameSettingを取得
		$this->__setBbsSetting();
		if (!isset($this->viewVars['bbsSettings'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//表示件数を設定
		$visiblePostRow =
			($visiblePostRow === '')? $this->viewVars['bbsSettings']['visible_post_row'] : $visiblePostRow;
		$this->set('currentVisibleRow', $visiblePostRow);

		$this->__setBbs();
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//フレーム置いた直後
		if (!isset($this->viewVars['bbses']['key'])) {
			$this->view = 'Bbses/notCreateBbs';
			return;
		}

		if ($this->viewVars['bbsPostNum']) {
			$this->__setPost($postId = 0, $currentPage, $sortParams, $visiblePostRow);
			//$sortParam, $visibleNum
			if (!isset($this->viewVars['bbsPosts'])) {
				throw new NotFoundException(__d('net_commons', 'Not Found'));
			}
		} else {
			$this->view = 'Bbses/notCreatePost';
			return;
		}

		if (! $this->viewVars['bbses']) {
			$this->autoRender = false;
		}

	}

/**
 * edit method
 *
 * @return void
 */
	public function edit() {
		//不要:defaultでBbses/editを見てくれる
		//$this->view = 'Bbses/edit';

		$this->__setBbs();
		//debug用
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//TODO:歯車から飛んできたときのURLを保持するように
		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		if ($this->request->isPost()) {
			$data = $this->data;
			//$blockId, $userId, $contentCreatable, $contentEditable, $is_post_list
			if (!$bbs = $this->Bbs->getBbs(
				isset($this->data['Block']['id']) ? (int)$this->data['Block']['id'] : null,
				false,
				false,
				false,
				false
			)) {
				//bbsテーブルデータ作成とkey格納
				$bbs = $this->Bbs->create(['key' => Security::hash('bbs' . mt_rand() . microtime(), 'md5')]);
			}

			// Hash::mergeが上手くいかないため、とりあえず編集項目を個別で格納
			//$data = Hash::merge($data, $bbs);
			$bbs['Bbs']['name'] = $data['Bbs']['name'];
			$bbs['Bbs']['comment_flag'] = ($data['Bbs']['comment_flag'] === '1') ? true : false;
			$bbs['Bbs']['vote_flag'] = ($data['Bbs']['vote_flag'] === '1') ? true : false;
			$data = Hash::merge($data, $bbs);

			if (!$bbs = $this->Bbs->saveBbs($data)) {
				if (!$this->__handleValidationError($this->Bbs->validationErrors)) {
					return;
				}
			}

			$this->set('blockId', $bbs['Bbs']['block_id']);
			if (!$this->request->is('ajax')) {
				$backUrl = CakeSession::read('backUrl');
				CakeSession::delete('backUrl');
				$this->redirect($backUrl);
			}
			return;
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
		if (!$bbses = $this->Bbs->getBbs(
				(isset($this->viewVars['blockId'])? $this->viewVars['blockId'] : ''),
				$this->viewVars['userId'],
				$this->viewVars['contentCreatable'],
				$this->viewVars['contentEditable'],
				true	//記事一覧である
			)
		) {
			//おそらく置かれた直後の話
			$bbses = $this->Bbs->create();
			$results = array(
				'bbses' => $bbses['Bbs'],
				'bbsPostNum' => 0
			);
			$this->set($results);
			return;
		}

		$results = array(
			'bbses' => $bbses['Bbs'],
			'bbsPostNum' => count($bbses['BbsPost'])
		);

		$this->set($results);
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setPost($postId, $currentPage, $sortParams, $visiblePostRow) {
		//ソート条件をセット
		$sortOrder = $this->__setSortOrder($sortParams);

		//BbsPost->find
		$bbsPosts = $this->BbsPost->getPosts(
				$this->viewVars['bbses']['id'],
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$postId,			//欲しい記事のID指定
				$sortOrder,			//order by指定
				$visiblePostRow,	//limit指定
				$currentPage		//ページ番号指定
			);
		$this->set('bbsPosts', $bbsPosts);

		//前のページがあるか取得
		if ($currentPage === 1) {
			$this->set('hasPrevPage', false);
		} else {
			$prevPage = $currentPage - 1;
			$prevPosts = $this->BbsPost->getPosts(
					$this->viewVars['bbses']['id'],
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$postId,			//欲しい記事のID指定
					$sortOrder,			//order by指定
					$visiblePostRow,	//limit指定
					$prevPage			//前のページ番号指定
				);
			$hasPrevPage = (empty($prevPosts))? false : true;
			$this->set('hasPrevPage', $hasPrevPage);
		}

		//次のページがあるか取得
		$nextPage = $currentPage + 1;
		$nextPosts = $this->BbsPost->getPosts(
				$this->viewVars['bbses']['id'],
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$postId,			//欲しい記事のID指定
				$sortOrder,			//order by指定
				$visiblePostRow,	//limit指定
				$nextPage			//次のページ番号指定
			);
		$hasNextPage = (empty($nextPosts))? false : true;
		$this->set('hasNextPage', $hasNextPage);

		//2ページ先のページがあるか取得
		$nextSecondPage = $currentPage + 2;
		$nextSecondPosts = $this->BbsPost->getPosts(
				$this->viewVars['bbses']['id'],
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$postId,			//欲しい記事のID指定
				$sortOrder,			//order by指定
				$visiblePostRow,	//limit指定
				$nextSecondPage		//2ページ先の番号指定
			);
		$hasNextSecondPage = (empty($nextSecondPosts))? false : true;
		$this->set('hasNextSecondPage', $hasNextSecondPage);

		//1,2ページの時のみ4,5ページがあるかどうか取得（モックとしてとりあえず）
		//if ($currentPage === 1 || $currentPage === 2) {
			//4ページがあるか取得（モックとしてとりあえず）
			$posts = $this->BbsPost->getPosts(
					$this->viewVars['bbses']['id'],
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$postId,			//欲しい記事のID指定
					$sortOrder,			//order by指定
					$visiblePostRow,	//limit指定
					4					//4ページ先の番号指定
				);
			$hasFourPage = (empty($posts))? false : true;
			$this->set('hasFourPage', $hasFourPage);

			//5ページがあるか取得（モックとしてとりあえず）
			$posts = $this->BbsPost->getPosts(
					$this->viewVars['bbses']['id'],
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$postId,			//欲しい記事のID指定
					$sortOrder,			//order by指定
					$visiblePostRow,	//limit指定
					5					//5ページ先の番号指定
				);
			$hasFivePage = (empty($posts))? false : true;
			$this->set('hasFivePage', $hasFivePage);
		//}
	}

/**
 * __setPost method
 *
 * @param $sortParams
 * @return string order for search
 */
	private function __setSortOrder($sortParams) {
		switch ($sortParams) {
		case '1':
		default :
			//最新の投稿順
			$sortStr = __d('bbses', 'Latest post order');
			$this->set('currentPostSortOrder', $sortStr);
			return 'BbsPost.created DESC';
		case '2':
			//古い投稿順
			$sortStr = __d('bbses', 'Older post order');
			$this->set('currentPostSortOrder', $sortStr);
			return 'BbsPost.created ASC';
		case '3':
			//最新の投稿順（未読のみ）
			$sortStr = __d('bbses', 'Do not read');
			$this->set('currentPostSortOrder', $sortStr);
			return 'BbsPost.comment_num DESC';
		case '4':
			//コメントの多い順
			$sortStr = __d('bbses', 'Descending order of comments');
			$this->set('currentPostSortOrder', $sortStr);
			return 'BbsPost.comment_num DESC';
		case '5':
			//ステータス順
			$sortStr = __d('bbses', 'Status order');
			$this->set('currentPostSortOrder', $sortStr);
			return 'BbsPost.status DESC';
		}
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

}
