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
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		$this->__setBbs();
		if (!isset($this->viewVars['bbses'])) {
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
	public function view() {
		$this->view = 'Bbses/edit';
		$this->__setBbs();
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
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
		$results = $this->camelizeKeyRecursive($results);
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
	private function __setPost($postId, $key, $params) {
		//初期設定
		$visiblePostRow = $this->viewVars['bbsSettings']['visiblePostRow'];
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
			$posts[] = $this->camelizeKeyRecursive($results);
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
}
