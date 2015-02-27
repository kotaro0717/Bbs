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
 * setCommentNum method
 *
 * @return void
 */
	public function setCommentNum($posts) {
		$conditions['and']['lft >'] = $posts['lft'];
		$conditions['and']['rght <'] = $posts['rght'];
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
		if (((int)$this->viewVars['rolesRoomId'] < 4) ||
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
 * @param $sortParams
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
 * @param $narrowDownParams
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
