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
 * @param $frameId frame.id フレームID
 * @param $currentPage ページ番号
 * @param $sortParams ソートID
 * @param $visiblePostRow 表示件数
 * @param $narrowDownParams 絞り込みID
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
 * @param $frameId frame.id フレームID
 * @param $currentPage ページ番号
 * @param $sortParams ソートID
 * @param $visiblePostRow 表示件数
 * @param $narrowDownParams 絞り込みID
 * @return void
 */
	public function view($frameId, $currentPage = '', $sortParams = '',
								$visiblePostRow = '', $narrowDownParams = '') {

		//一覧ページのURLをBackURLに保持
		if ($this->request->isGet()) {
				CakeSession::write('backUrl', Router::url(null, true));
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

		//BbsFrameSettingを取得
		$this->__setBbsSetting();

		//表示件数を設定
		$visiblePostRow = ($visiblePostRow === '')?
				$this->viewVars['bbsSettings']['visible_post_row'] : $visiblePostRow;
		$this->set('currentVisibleRow', $visiblePostRow);

		$this->__initBbs();

		//フレーム置いた直後
		if (! isset($this->viewVars['bbses']['id'])) {
			$this->view = 'Bbses/notCreateBbs';
			return;
		}

		//記事一覧情報取得
		$this->__setPost($currentPage, $sortParams, $visiblePostRow, $narrowDownParams);

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
		$this->__initBbs();

		if ($this->request->isGet()) {
			$referer = $this->request->referer();
			if (! strstr($referer, '/bbses')) {
				CakeSession::write('backUrl', $this->request->referer());
			}
		}

		if ($this->request->isPost()) {
			$data = $this->data;
			//$blockId, $userId, $contentCreatable, $contentEditable, $is_post_list
			if (!$bbs = $this->Bbs->getBbs(
				isset($this->data['Block']['id']) ? (int)$this->data['Block']['id'] : null
			)) {
				//bbsテーブルデータ作成とkey格納
				$bbs = $this->Bbs->create(['key' => Security::hash('bbs' . mt_rand() . microtime(), 'md5')]);
				$bbs['Bbs']['block_id'] = 0;

				//Todo:デフォルト値が文字列で判断されるがどうにかならないか？
				$bbs['Bbs']['post_create_authority'] = ($bbs['Bbs']['post_create_authority'] === '1') ? true : false;
				$bbs['Bbs']['post_publish_authority'] = ($bbs['Bbs']['post_publish_authority'] === '1') ? true : false;
				$bbs['Bbs']['comment_create_authority'] = ($bbs['Bbs']['comment_create_authority'] === '1') ? true : false;
			}

			$bbs['Bbs']['name'] = $data['Bbs']['name'];

			//Todo:デフォルト値が文字列で判断されるがどうにかならないか？
			$bbs['Bbs']['use_comment'] = ($data['Bbs']['use_comment'] === '1') ? true : false;
			$bbs['Bbs']['auto_approval'] = ($data['Bbs']['auto_approval'] === '1') ? true : false;
			$bbs['Bbs']['use_like_button'] = ($data['Bbs']['use_like_button'] === '1') ? true : false;
			$bbs['Bbs']['use_unlike_button'] = ($data['Bbs']['use_unlike_button'] === '1') ? true : false;

			//作成時間,更新時間を再セット
			unset($bbs['Bbs']['created'], $bbs['Bbs']['modified']);
			$data = Hash::merge($bbs, $data);

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
 * __setBbsSetting method
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
	private function __initBbs() {
		//ログインユーザIDを取得し、Viewにセット
		$this->set('userId', $this->Session->read('Auth.User.id'));

		//掲示板データを取得
		if (!$bbses = $this->Bbs->getBbs(
				isset($this->viewVars['blockId'])? $this->viewVars['blockId'] : ''
			)
		) {
			//掲示板が作成されていない場合
			$bbses = $this->Bbs->create(['key' => Security::hash('bbs' . mt_rand() . microtime(), 'md5')]);
			$bbses['Bbs']['name'] = '掲示板_' . date('YmdHis');
			$bbses['Bbs']['use_comment'] = ($bbses['Bbs']['use_comment'] === '1') ? true : false;
			$bbses['Bbs']['auto_approval'] = ($bbses['Bbs']['auto_approval'] === '1') ? true : false;
			$bbses['Bbs']['use_like_button'] = ($bbses['Bbs']['use_like_button'] === '1') ? true : false;
			$bbses['Bbs']['use_unlike_button'] = ($bbses['Bbs']['use_unlike_button'] === '1') ? true : false;
			$this->set(array(
				'bbses' => $bbses['Bbs'],
			));
			return;
		}

		$this->set(array(
			'bbses' => $bbses['Bbs'],
		));
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setPost($currentPage, $sortParams, $visiblePostRow, $narrowDownParams) {
		//ソート条件をセット
		$sortOrder = $this->setSortOrder($sortParams);

		//絞り込み条件をセット
		$conditions = $this->setNarrowDown($narrowDownParams);

		//取得条件をセット
		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];
		$conditions['parent_id'] = null;

		if (! $bbsPosts = $this->BbsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,			//order by指定
				$visiblePostRow,	//limit指定
				$currentPage,		//ページ番号指定
				$conditions			//検索条件をセット
		)) {
			$bbsPosts = $this->BbsPost->create();
			$results = array(
					'bbsPosts' => $bbsPosts['BbsPost'],
					'bbsPostNum' => 0,
				);

		} else {

			//記事を$results['bbsPosts']にセット
			foreach ($bbsPosts as $bbsPost) {

				//未読or既読セット
				//$readStatus true:read, false:not read
				$readStatus = $this->BbsPostsUser->getReadPostStatus(
									$bbsPost['BbsPost']['id'],
									$this->viewVars['userId']
								);
				$bbsPost['BbsPost']['readStatus'] = $readStatus;

				//絞り込みで未読が選択された場合
				if ($narrowDownParams === '7' && $readStatus === true) {
					//debug('既読');

				} else {
					//公開データ以外を含めたコメント数をセット
					$bbsPost['BbsPost']['all_comment_num'] = $this->__setCommentNum($bbsPost['BbsPost']);

					//記事データを配列にセット
					$results['bbsPosts'][] = $bbsPost['BbsPost'];

				}
			}
			//該当記事がない場合は空をセット
			if (!isset($results)) {
				$bbsPosts = $this->BbsPost->create();
				$results = array(
						'bbsPosts' => $bbsPosts['BbsPost'],
						'bbsPostNum' => 0,
					);

			} else {
				//記事数を$results['bbsPostNum']セット
				$results['bbsPostNum'] = count($results['bbsPosts']);

			}
		}
		$this->set($results);

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
					$visiblePostRow,	//limit指定
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
				$visiblePostRow,	//limit指定
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
				$visiblePostRow,	//limit指定
				$nextSecondPage,	//2ページ先の番号指定
				$conditions
			);
		$hasNextSecondPage = (empty($nextSecondPosts))? false : true;
		$this->set('hasNextSecondPage', $hasNextSecondPage);

		//1,2ページの時のみ4,5ページがあるかどうか取得（モックとしてとりあえず）
		//if ($currentPage === 1 || $currentPage === 2) {
			//4ページがあるか取得（モックとしてとりあえず）
			$posts = $this->BbsPost->getPosts(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,			//order by指定
					$visiblePostRow,	//limit指定
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
					$visiblePostRow,	//limit指定
					5,					//5ページ先の番号指定
					$conditions
				);
			$hasFivePage = (empty($posts))? false : true;
			$this->set('hasFivePage', $hasFivePage);
		//}
	}

/**
 * __setCommentNum method
 *
 * @param $bbsPost
 * @return string order for search
 */
	private function __setCommentNum($bbsPost) {
		//検索条件をセット
		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];
		$conditions['and']['lft >'] = $bbsPost['lft'];
		$conditions['and']['rght <'] = $bbsPost['rght'];

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
 * __setCommentNum method
 *
 * @param $bbsPost
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
		return;
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
