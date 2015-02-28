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
 * setBbsSetting method
 *
 * @param int $currentPage currentPage
 * @param int $sortParams sortParameter
 * @param int $visibleRow visibleRow
 * @param int $narrowDownParams narrowDownParameter
 * @return void
 */
	public function initParams($currentPage = '', $sortParams = '', $visibleRow = '', $narrowDownParams = '') {
		$baseUrl = Inflector::variable($this->plugin) . '/' .
				Inflector::variable($this->name) . '/' . $this->action;
		$this->set('baseUrl', $baseUrl);

		//現在の一覧表示ページ番号をセット
		$currentPage = ($currentPage === '')? 1: (int)$currentPage;
		$this->set('currentPage', $currentPage);

		//現在のソートパラメータをセット
		$sortParams = ($sortParams === '')? '1': $sortParams;
		$this->set('sortParams', $sortParams);

		//表示件数をセット
		$visibleRow =
			($visibleRow === '')? $this->viewVars['bbsSettings']['visible_comment_row'] : $visibleRow;
		$this->set('currentVisibleRow', $visibleRow);

		//現在の絞り込みをセット
		$narrowDownParams = ($narrowDownParams === '')? '6' : $narrowDownParams;
		$this->set('narrowDownParams', $narrowDownParams);
	}

/**
 * setBbsSetting method
 *
 * @return void
 */
	public function setBbsSetting() {
		//掲示板の表示設定情報を取得
		$bbsSettings = $this->BbsFrameSetting->getBbsSetting(
										$this->viewVars['frameKey']);
		$results = array(
			'bbsSettings' => $bbsSettings['BbsFrameSetting'],
		);
		$this->set($results);
	}

/**
 * setBbs method
 *
 * @return void
 */
	public function setBbs() {
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
 * setComment method
 *
 * @param int $postId bbsPosts.id
 * @param int $currentPage currentPage
 * @param int $sortParams sortParameter
 * @param int $visibleRow visibleRow
 * @param int $narrowDownParams narrowDownParameter
 * @param array $conditions condition for search
 * @return void
 */
	public function setComment($postId, $currentPage, $sortParams,
								$visibleRow, $narrowDownParams, $conditions) {
		//ソート条件をセット
		$sortOrder = $this->setSortOrder($sortParams);

		//絞り込み条件をセット
		$conditions[] = $this->setNarrowDown($narrowDownParams);
		$conditions['bbs_key'] = $this->viewVars['bbses']['key'];

		$bbsCommnets = $this->BbsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,			//order by指定
				$visibleRow,		//limit指定
				$currentPage,		//ページ番号指定
				$conditions
			);

		//コメントなしの場合
		if (empty($bbsCommnets)) {
			$this->set('bbsComments', false);
			return;
		}

		foreach ($bbsCommnets as $bbsComment) {
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
			//取得した記事の配列にユーザ名を追加
			$bbsComment['BbsPost']['username'] = $user['User']['username'];
			$bbsComment['BbsPost']['userId'] = $user['User']['id'];
			$bbsComment['BbsPost']['likesNum'] = $likes['likesNum'];
			$bbsComment['BbsPost']['unlikesNum'] = $likes['unlikesNum'];
			$bbsComment['BbsPost']['likesFlag'] = $likes['likesFlag'];
			$bbsComment['BbsPost']['unlikesFlag'] = $likes['unlikesFlag'];

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
					$visibleRow,		//limit指定
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
				$visibleRow,		//limit指定
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
				$visibleRow,		//limit指定
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
				$visibleRow,		//limit指定
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
				$visibleRow,		//limit指定
				5,					//5ページ先の番号指定
				$conditions
			);
		$hasFivePage = (empty($posts))? false : true;
		$this->set('hasFivePage', $hasFivePage);
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
 * setCommentCreateAuth method
 *
 * @return void
 */
	public function setCommentCreateAuth() {
		if (((int)$this->viewVars['rolesRoomId'] !== 0 &&
				(int)$this->viewVars['rolesRoomId'] < 4) ||
				($this->viewVars['bbses']['comment_create_authority'] &&
				$this->viewVars['contentCreatable'])) {

			$this->set('commentCreatable', true);

		} else {
			$this->set('commentCreatable', false);

		}
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
 * @param int $narrowDownParams narrowDownParams
 * @return array order conditions for narrow down, or void
 */
	public function setNarrowDown($narrowDownParams) {
		switch ($narrowDownParams) {
		case '1':
				//公開
				$narrowDownStr = __d('bbses', 'Published');
				$this->set('narrowDown', $narrowDownStr);
				$conditions = array(
						'status' => NetCommonsBlockComponent::STATUS_PUBLISHED
					);
				return $conditions;

		case '2':
				//承認待ち
				$narrowDownStr = __d('net_commons', 'Approving');
				$this->set('narrowDown', $narrowDownStr);
				$conditions = array(
						'status' => NetCommonsBlockComponent::STATUS_APPROVED
					);
				return $conditions;

		case '3':
				//一時保存
				$narrowDownStr = __d('net_commons', 'Temporary');
				$this->set('narrowDown', $narrowDownStr);
				$conditions = array(
						'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT
					);
				return $conditions;

		case '4':
				//非承認
				$narrowDownStr = __d('bbses', 'Remand');
				$this->set('narrowDown', $narrowDownStr);
				$conditions = array(
						'status' => NetCommonsBlockComponent::STATUS_DISAPPROVED
					);
				return $conditions;

		case '5':
				//非承認
				$narrowDownStr = __d('bbses', 'Disapproval');
				$this->set('narrowDown', $narrowDownStr);
				$conditions = array(
						'status' => '5'
					);
				return $conditions;

		case '6':
		default :
				//全件表示
				$narrowDownStr = __d('bbses', 'Display all posts');
				$this->set('narrowDown', $narrowDownStr);
				return;

		case '7':
				//未読
				$narrowDownStr = __d('bbses', 'Do not read');
				$this->set('narrowDown', $narrowDownStr);
				//未読or既読セット中に未読のみ取得する
				return;
		}
	}

}
