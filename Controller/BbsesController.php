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
 * index method
 *
 * @return void
 */
	public function index($frameId, $key = '', $params = '') {
		$this->view = 'Bbses/index';

		$this->__setBbsSetting();
		if (!isset($this->viewVars['bbsSettings'])) {
			debug(1);
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		$this->__setBbs();
		if (!isset($this->viewVars['bbses'])) {
			debug(2);
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//フレーム置いただけの場合
		if (!isset($this->viewVars['bbses']['key'])) {
			$this->view = 'Bbses/notCreateBbs';
			return;
		}

		if ($this->viewVars['bbsPostNum']) {
			$this->__setPost($postId = 0, $key, $params);
			if (!isset($this->viewVars['bbsPosts'])) {
				debug(3);
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
			'currentVisiblePostRow' => $bbsSettings['BbsFrameSetting']['visible_post_row']
		);
		$this->set($results);

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
				$is_post_list = true
			)
		) {
			$bbses = $this->Bbs->create();
				$results = array(
				'bbses' => $bbses['Bbs'],
				'bbsPostNum' => 0
			);
			$results = $this->camelizeKeyRecursive($results);
			$this->set($results);
			return;
		}
		//camelize
		$results = array(
			'bbses' => $bbses['Bbs'],
			'bbsPostNum' => count($bbses['BbsPost'])
		);
		//$results = $this->camelizeKeyRecursive($results);
		$this->set($results);

		//記事をcamelize（配列のため別途行う）
//		foreach ($bbses['BbsPost'] as $bbsPost) {
//			$results = array(
//				'bbsPosts' => $bbsPost,
//			);
//			$posts[] = $this->camelizeKeyRecursive($results);
//		}
//
//		//配列の再構成 TODO:スリムじゃない
//		foreach ($posts as $post) {
//			$result[] = $post['bbsPosts'];
//		}
//		$results['bbsPosts'] = $result;
//		$this->set($results);
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setPost($postId, $key = '', $params = '') {
		//初期設定
		$visiblePostRow = $this->viewVars['bbsSettings']['visible_post_row'];
		$sortOrder = $this->__setSortOrder($params);
		//key(1):ソート順、key(2):表示件数
		if ($key === '2') {
			$visiblePostRow = $params;
			$results = array(
				'currentVisiblePostRow' => $visiblePostRow

			);
			$this->set($results);
		}

		$bbsPosts = $this->BbsPost->getPosts(
				$this->viewVars['bbses']['id'],
				$visiblePostRow,
				$this->viewVars['contentCreatable'],
				$postId,
				$sortOrder
			);

		//camelize
		foreach ($bbsPosts as $bbsPost) {
			$results = array(
				'bbsPosts' => $bbsPost['BbsPost'],
			);
			//$posts[] = $this->camelizeKeyRecursive($results);
			$posts[] = $results;
		}

		//配列の再構成 TODO:スリムじゃない
		foreach ($posts as $post) {
			$result[] = $post['bbsPosts'];
		}
		$results['bbsPosts'] = $result;

		$this->set($results);

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
			$sortStr = __d('bbses', 'Latest post order');
			$this->set('currentPostSortOrder', $sortStr);
			return 'BbsPost.created DESC';
		case '2':
			//古い投稿順
			$sortStr = __d('bbses', 'Older post order');
			$this->set('currentPostSortOrder', $sortStr);
			return 'BbsPost.created ASC';
		case '3':
			//コメントの多い順
			$sortStr = __d('bbses', 'Descending order of comments');
			$this->set('currentPostSortOrder', $sortStr);
			return 'BbsPost.comment_num DESC';
		case '4':
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
