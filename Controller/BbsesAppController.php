<?php
/**
 * BbsesApp Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * BbsesApp Controller
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Bbses\Controller
 */
class BbsesAppController extends AppController {

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		//'Security'
	);

/**
 * redirectBackUrl
 *
 * @throws BadRequestException
 * @return mixed status on success, false on error
 */
	public function redirectBackUrl() {
		$backUrl = CakeSession::read('backUrl');
		CakeSession::delete('backUrl');
		$this->redirect($backUrl);
	}

/**
 * Parse content status from request
 *
 * @throws BadRequestException
 * @return mixed status on success, false on error
 */
	public function parseStatus() {
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
 * setAddSaveData
 *
 * @param array $postData post data
 * @param int $status bbsPosts.status
 * @param int $parentId bbsPosts.id
 * @return array
 */
	public function setAddSaveData($postData, $status, $parentId = '') {
		$data = Hash::merge(
			$postData,
			['BbsPost' => ['status' => $status]]
		);

		//新規登録のため、データ生成
		$comment = $this->BbsPost->create(['key' => Security::hash('bbsPost' . mt_rand() . microtime(), 'md5')]);

		$comment['BbsPost']['bbs_key'] = $data['Bbs']['key'];

		if ($parentId) {
			//コメント番号取得のために親記事情報取得
			$conditions['bbs_key'] = $data['Bbs']['key'];
			$conditions['id'] = $parentId;
			$parentPosts = $this->BbsPost->getOnePosts(
					false,
					false,
					false,
					$conditions
				);

			$comment['BbsPost']['comment_index'] = $parentPosts['BbsPost']['comment_index'];
		}

		return Hash::merge($comment, $data);
	}

/**
 * setEditSaveData
 *
 * @param array $postData post data
 * @param int $postId bbsPosts.id
 * @return array
 */
	public function setEditSaveData($postData, $postId) {
		if (! $status = $this->parseStatus()) {
			return false;
		}

		$data = Hash::merge(
			$postData,
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

		$results = Hash::merge($bbsPosts, $data);

		//UPDATE
		$results['BbsPost']['id'] = $bbsPosts['BbsPost']['id'];

		return $results;
	}

/**
 * Handle validation error
 *
 * @param array $errors validation errors
 * @return bool true on success, false on error
 */
	public function handleValidationError($errors) {
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
 * setBbs method
 *
 * @return void
 */
	public function setBbs() {
		//掲示板の表示設定情報を取得
		$bbsSettings = $this->BbsFrameSetting->getBbsSetting(
										$this->viewVars['frameKey']);
		$this->set(array(
			'bbsSettings' => $bbsSettings['BbsFrameSetting'],
		));

		//ログインユーザIDを取得し、Viewにセット
		$this->set('userId', $this->Session->read('Auth.User.id'));

		$blockId = isset($this->viewVars['blockId'])? $this->viewVars['blockId'] : null;

		//掲示板データを取得
		if (! $bbs = $this->Bbs->getBbs($blockId)) {
			$bbs = $this->initBbs();
		}

		$this->set(array(
			'bbses' => $bbs['Bbs']
		));
	}

/**
 * initBbs method
 *
 * @return void
 */
	public function initBbs() {
		//掲示板が作成されていない場合
		$bbs = $this->Bbs->create(['key' => Security::hash('bbs' . mt_rand() . microtime(), 'md5')]);

		$bbs['Bbs'] = array(
			'name' => '掲示板_' . date('YmdHis'),
			'use_comment' => true,
			'auto_approval' => false,
			'use_like_button' => true,
			'use_unlike_button' => true,
			'post_create_authority' => true,
			'editor_publish_authority' => false,
			'general_publish_authority' => false,
			'comment_create_authority' => true,
		);

		return $bbs;
	}

/**
 * setComment method
 *
 * @param array $conditions condition for search
 * @return void
 */
	public function setComment($conditions) {
		//ソート条件をセット
		$sortOrder = $this->setSortOrder($this->viewVars['sortParams']);

		//絞り込み条件をセット
		$conditions = $this->setNarrowDown($conditions, $this->viewVars['narrowDownParams'], true);

		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];

		$bbsCommnets = $this->BbsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,									//order by指定
				$this->viewVars['currentVisibleRow'],		//limit指定
				$this->viewVars['currentPage'],				//ページ番号指定
				$conditions
			);

		//コメントなしの場合
		if (empty($bbsCommnets)) {
			$this->set('bbsComments', false);
			return;
		}

		foreach ($bbsCommnets as $bbsComment) {
			$conditions = '';
			$conditions['bbs_key'] = $this->viewVars['bbses']['key'];
			$conditions['id'] = $bbsComment['BbsPost']['parent_id'];
			//親記事の記事取得
			$parentPosts = $this->BbsPost->getOnePosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$conditions
			);

			//いいね・よくないねを取得
			$likes = $this->BbsPostsUser->getLikes(
						$bbsComment['BbsPost']['id'],
						$this->viewVars['userId']
					);

			//取得した記事の作成者IDからユーザ情報を取得
			$user = $this->User->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $bbsComment['BbsPost']['created_user'],
					)
				)
			);
			//取得した情報を配列に追加
			$bbsComment['BbsPost']['parent_comment_index'] = $parentPosts['BbsPost']['comment_index'];
			$bbsComment['BbsPost']['username'] = $user['User']['username'];
			$bbsComment['BbsPost']['userId'] = $user['User']['id'];
			$bbsComment['BbsPost']['likesNum'] = $likes['likesNum'];
			$bbsComment['BbsPost']['unlikesNum'] = $likes['unlikesNum'];
			$bbsComment['BbsPost']['likesFlag'] = $likes['likesFlag'];
			$bbsComment['BbsPost']['unlikesFlag'] = $likes['unlikesFlag'];
			$results[] = $bbsComment['BbsPost'];
		}
		$this->set('bbsComments', $results);
	}

/**
 * setPagination method
 *
 * @param array $conditions condition for search
 * @param int $postId bbsPosts.id
 * @return void
 */
	public function setPagination($conditions = '', $postId = '') {
		//ソート条件をセット
		$sortOrder = $this->setSortOrder($this->viewVars['sortParams']);

		if (! $postId) {
			$conditions['parent_id'] = null;
		}
		//取得条件をセット
		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];

		//絞り込み条件をセット
		$conditions = $this->setNarrowDown($conditions, $this->viewVars['narrowDownParams'], false);

		//前のページがあるか取得
		if ($this->viewVars['currentPage'] === 1) {
			$this->set('hasPrevPage', false);

		} else {
			$this->set('hasPrevPage',
					$this->BbsPost->getPosts(
						$this->viewVars['userId'],
						$this->viewVars['contentEditable'],
						$this->viewVars['contentCreatable'],
						$sortOrder,								//order by指定
						$this->viewVars['currentVisibleRow'],	//limit指定
						$this->viewVars['currentPage'] - 1,		//前のページ番号指定
						$conditions
				)? true : false);
		}

		//次のページがあるか取得
		$this->set('hasNextPage',
				$this->BbsPost->getPosts(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,								//order by指定
					$this->viewVars['currentVisibleRow'],	//limit指定
					$this->viewVars['currentPage'] + 1,		//次のページ番号指定
					$conditions
			)? true : false);

		$this->setNextPagination($sortOrder, $conditions);
	}

/**
 * setNextPagination method
 *
 * @param array $sortOrder sort order for search
 * @param array $conditions conditions for search
 * @return void
 */
	public function setNextPagination($sortOrder, $conditions) {
		//2ページ先のページがあるか取得
		$this->set('hasNextSecondPage',
				$this->BbsPost->getPosts(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,								//order by指定
					$this->viewVars['currentVisibleRow'],	//limit指定
					$this->viewVars['currentPage'] + 2,		//2ページ先の番号指定
					$conditions
			)? true : false);

		//4ページがあるか取得（モックとしてとりあえず）
		$this->set('hasFourPage',
				$this->BbsPost->getPosts(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,								//order by指定
					$this->viewVars['currentVisibleRow'],	//limit指定
					4,										//4ページ先の番号指定
					$conditions
			)? true : false);

		//5ページがあるか取得（モックとしてとりあえず）
		$this->set('hasFivePage',
				$this->BbsPost->getPosts(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,								//order by指定
					$this->viewVars['currentVisibleRow'],	//limit指定
					5,										//5ページ先の番号指定
					$conditions
			)? true : false);
	}

/**
 * initParams method
 *
 * @param int $currentPage currentPage
 * @param int $sortParams sortParameter
 * @param int $narrowDownParams narrowDownParameter
 * @return void
 */
	public function initParams($currentPage = '', $sortParams = '', $narrowDownParams = '') {
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
		$narrowDownParams = ($narrowDownParams === '')? '5' : $narrowDownParams;
		$this->set('narrowDownParams', $narrowDownParams);
	}

/**
 * setCommentNum method
 *
 * @param int $lft bbsPosts.lft
 * @param int $rght bbsPosts.rght
 * @return void
 */
	public function setCommentNum($lft, $rght) {
		$conditions['and']['lft >'] = $lft;
		$conditions['and']['rght <'] = $rght;
		if (! $comments = $this->BbsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				false,
				false,
				false,
				$conditions
		)) {
			$this->set('commentNum', 0);
		}
		$this->set('commentNum', count($comments));
	}

/**
 * setSortOrder method
 *
 * @param int $sortParams sortParams
 * @return string order for search
 */
	public function setSortOrder($sortParams) {
		switch ($sortParams) {
		case '1':
		default :
				//最新の投稿順
				$sortStr = __d('bbses', 'Latest post order');
				$this->set('currentSortOrder', $sortStr);
				return array('BbsPost.created DESC', 'BbsPost.title');

		case '2':
				//古い投稿順
				$sortStr = __d('bbses', 'Older post order');
				$this->set('currentSortOrder', $sortStr);
				return array('BbsPost.created ASC', 'BbsPost.title');

		case '3':
				//コメントの多い順
				$sortStr = __d('bbses', 'Descending order of comments');
				$this->set('currentSortOrder', $sortStr);
				return array('BbsPost.comment_num DESC', 'BbsPost.title');

		}
	}

/**
 * setNarrowDown method
 *
 * @param array $conditions find conditions
 * @param int $narrowDownParams narrowDownParams
 * @param bool $isComments true is comments list, or false is posts list
 * @return array order conditions for narrow down, or void
 */
	public function setNarrowDown($conditions, $narrowDownParams, $isComments) {
		switch ($narrowDownParams) {
		case '1':
				//公開
				$narrowDownStr = __d('bbses', 'Published');
				$this->set('narrowDown', $narrowDownStr);
				$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
				return $conditions;

		case '2':
				//承認待ち/不承認
				$narrowDownStr = ($isComments)? __d('bbses', 'Disapproval') : __d('net_commons', 'Approving');
				$this->set('narrowDown', $narrowDownStr);
				$conditions['status'] = NetCommonsBlockComponent::STATUS_APPROVED;
				return $conditions;

		case '3':
				//一時保存
				$narrowDownStr = __d('net_commons', 'Temporary');
				$this->set('narrowDown', $narrowDownStr);
				$conditions['status'] = NetCommonsBlockComponent::STATUS_IN_DRAFT;
				return $conditions;

		case '4':
				//差し戻し
				$narrowDownStr = __d('bbses', 'Remand');
				$this->set('narrowDown', $narrowDownStr);
				$conditions['status'] = NetCommonsBlockComponent::STATUS_DISAPPROVED;
				return $conditions;

		case '5':
		default :
				//全件表示
				$narrowDownStr = __d('bbses', 'Display all posts');
				$this->set('narrowDown', $narrowDownStr);
				$conditions['and']['status >='] = '1';
				$conditions['and']['status <='] = '4';
				return $conditions;

		case '6':
				//未読
				$narrowDownStr = __d('bbses', 'Unread');
				$this->set('narrowDown', $narrowDownStr);
				$conditions['and']['status >='] = '1';
				$conditions['and']['status <='] = '4';
				//未読or既読セット中に未読のみ取得する
				return $conditions;
		}
	}

}
