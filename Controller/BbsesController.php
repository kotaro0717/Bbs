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
 * @param int $frameId frames.id
 * @param int $currentPage currentPage
 * @param int $sortParams sortParameter
 * @param int $visiblePostRow visiblePostRow
 * @param int $narrowDownParams narrowDownParameter
 * @return void
 */
	public function index($frameId, $currentPage = '', $sortParams = '',
								$visiblePostRow = '', $narrowDownParams = '') {
		$this->view = 'Bbses/view';
		$this->view($frameId, $currentPage, $sortParams, $visiblePostRow, $narrowDownParams);
	}

/**
 * index method
 *
 * @param int $frameId frames.id
 * @param int $currentPage currentPage
 * @param int $sortParams sortParameter
 * @param int $visiblePostRow visiblePostRow
 * @param int $narrowDownParams narrowDownParameter
 * @return void
 */
	public function view($frameId, $currentPage = '', $sortParams = '',
								$visiblePostRow = '', $narrowDownParams = '') {
		//一覧ページのURLをBackURLに保持
		if ($this->request->isGet()) {
				CakeSession::write('backUrl', Router::url(null, true));
		}

		//コメント表示数/掲示板名等をセット
		$this->setBbs();

		//フレーム置いた直後
		if (! isset($this->viewVars['bbses']['id'])) {
			if ((int)$this->viewVars['rolesRoomId'] === 0) {
				$this->autoRender = false;
				return;
			}
			$this->view = 'Bbses/notCreateBbs';
			return;
		}

		//各パラメータをセット
		$this->initParams($currentPage, $sortParams, $narrowDownParams);

		//表示件数を設定
		$visiblePostRow = ($visiblePostRow === '')?
				$this->viewVars['bbsSettings']['visible_post_row'] : $visiblePostRow;
		$this->set('currentVisibleRow', $visiblePostRow);

		//記事一覧情報取得
		$this->__setPost();

		//ページング情報取得
		$this->setPagination();

		//記事数取得
		$this->__setPostNum();
	}

/**
 * edit method
 *
 * @return void
 */
	public function add() {
		$this->view = 'bbsPosts/edit';
		$this->view();
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit() {
		$this->setBbs();

		if ($this->request->isGet() &&
				! strstr($this->request->referer(), '/bbses')) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		if (! $this->request->isPost()) {
			return;
		}

		$data = $this->__setEditSaveData($this->data);

		if (!$bbs = $this->Bbs->saveBbs($data)) {
			if (!$this->handleValidationError($this->Bbs->validationErrors)) {
				return;
			}
		}

		$this->set('blockId', $bbs['Bbs']['block_id']);

		if (! $this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setPost() {
		//ソート条件をセット
		$sortOrder = $this->setSortOrder($this->viewVars['sortParams']);

		//取得条件をセット
		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];
		$conditions['parent_id'] = null;

		//絞り込み条件をセット
		$conditions = $this->setNarrowDown($conditions, $this->viewVars['narrowDownParams'], false);

		if (! $bbsPosts = $this->BbsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,								//order by指定
				$this->viewVars['currentVisibleRow'],	//limit指定
				$this->viewVars['currentPage'],			//ページ番号指定
				$conditions								//検索条件をセット
		)) {
			$bbsPosts = $this->BbsPost->create();
			$results = array(
					'bbsPosts' => $bbsPosts['BbsPost'],
					'bbsPostNum' => 0,
				);

		} else {
			$results = $this->__setPostRelatedData($bbsPosts);

		}
		$this->set($results);
	}

/**
 * __setPostRelatedData method
 *
 * @param array $bbsPosts bbsPosts
 * @return array
 */
	private function __setPostRelatedData($bbsPosts) {
		//記事を$results['bbsPosts']にセット
		foreach ($bbsPosts as $bbsPost) {

			//いいね・よくないねを取得
			$likes = $this->BbsPostsUser->getLikes(
						$bbsPost['BbsPost']['id'],
						$this->viewVars['userId']
					);

			$bbsPost['BbsPost']['likesNum'] = $likes['likesNum'];
			$bbsPost['BbsPost']['unlikesNum'] = $likes['unlikesNum'];

			//未読or既読セット
			//$readStatus true:read, false:not read
			$readStatus = $this->BbsPostsUser->getPostsUsers(
								$bbsPost['BbsPost']['id'],
								$this->viewVars['userId']
							);
			$bbsPost['BbsPost']['readStatus'] = $readStatus;

			//絞り込みで未読が選択された場合
			if ($this->viewVars['narrowDownParams'] === '7' && $readStatus === true) {
				//debug('既読');

			} else {
				//公開データ以外を含めたコメント数をセット
				$bbsPost['BbsPost']['allCommentNum'] =
						$this->__setCommentNum(
							$bbsPost['BbsPost']['lft'],
							$bbsPost['BbsPost']['rght']
					);

				//記事データを配列にセット
				$results['bbsPosts'][] = $bbsPost['BbsPost'];

			}
		}

		//該当記事がない場合は空をセット
		if (! isset($results)) {
			$bbsPosts = $this->BbsPost->create();
			$results = array(
					'bbsPosts' => $bbsPosts['BbsPost'],
					'bbsPostNum' => 0,
				);

		} else {
			//記事数を$results['bbsPostNum']セット
			$results['bbsPostNum'] = count($results['bbsPosts']);

		}

		return $results;
	}

/**
 * __setCommentNum method
 *
 * @param int $lft bbsPosts.lft
 * @param int $rght bbsPosts.rght
 * @return string order for search
 */
	private function __setCommentNum($lft, $rght) {
		//検索条件をセット
		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];
		$conditions['and']['lft >'] = $lft;
		$conditions['and']['rght <'] = $rght;

		//公開データ以外も含めたコメント数を取得
		$bbsCommnets = $this->BbsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				null,
				null,
				null,
				$conditions
			);

		return count($bbsCommnets);
	}

/**
 * __setPostNum method
 *
 * @return string order for search
 */
	private function __setPostNum() {
		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];
		$conditions['parent_id'] = '';

		$bbsPosts = $this->BbsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				null,
				null,
				null,
				$conditions
			);

		$results['postNum'] = count($bbsPosts);
		$this->set($results);
	}

/**
 * setEditSaveData
 *
 * @param array $postData post data
 * @return array
 */
	private function __setEditSaveData($postData) {
		$blockId = isset($postData['Block']['id']) ? (int)$postData['Block']['id'] : null;

		if (! $bbs = $this->Bbs->getBbs($blockId)) {
			//bbsテーブルデータ作成とkey格納
			$bbs = $this->initBbs();
			$bbs['Bbs']['block_id'] = 0;
		}

		$data['Bbs'] = $this->__convertStringToBoolean($postData, $bbs);

		$results = Hash::merge($postData, $bbs, $data);

		//IDリセット
		unset($results['Bbs']['id']);

		return $results;
	}

/**
 * __convertStringToBoolean
 *
 * @param array $data post data
 * @param array $bbs bbses
 * @return array
 */
	private function __convertStringToBoolean($data, $bbs) {
		//boolean値が文字列になっているため個別で格納し直し
		return $data['Bbs'] = array(
				'name' => $data['Bbs']['name'],
				'use_comment' => ($data['Bbs']['use_comment'] === '1') ? true : false,
				'auto_approval' => ($data['Bbs']['auto_approval'] === '1') ? true : false,
				'use_like_button' => ($data['Bbs']['use_like_button'] === '1') ? true : false,
				'use_unlike_button' => ($data['Bbs']['use_unlike_button'] === '1') ? true : false,
			);
	}
}
