<?php
/**
 * BbsPost Model
 *
 * @property Block $Block
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('BbsesAppModel', 'Bbses.Model');

/**
 * BbsPost Model
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Bbses\Model
 */
class BbsPost extends BbsesAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		// TODO: disabled for debug
		/* 'NetCommons.Publishable', */
		'Containable',	//Todo:全部ツリーでいける?
		'Tree',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();
	//The Associations below have been created with all possible keys, those that are not needed can be removed
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
//		'Bbs' => array(
//			'className' => 'Bbses.Bbs',
//			'foreignKey' => 'bbs_id',
//			'conditions' => '',
//			'fields' => '',
//			'order' => ''
//		),
//		'BbsPost' => array(
//			'className' => 'Bbses.BbsPost',
//			'foreignKey' => 'parent_id',
//			'conditions' => '',
//			'fields' => '',
//			'order' => ''
//		)
	);

/**
 * hasMany associations
 *s
 * @var array
 */
	public $hasMany = array(
//		'BbsPost' => array(
//            'className' => 'Bbses.BbsPost',
//            'foreignKey' => 'parent_id',
//            'order' => 'BbsPost.created DESC',
//            'dependent' => true
//        )
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'key' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			'bbs_id' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			//parent_id = null は親記事で許す
			/*'parent_id' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),*/

			//status to set in PublishableBehavior.
			'is_auto_translated' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				)
			),
			'title' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Title')),
					'required'
					=> true
				),
			),
			'content' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Content')),
					'required' => true
				),
			),
		));
		return parent::beforeValidate($options);
	}

/**
 * get bbs data
 *
 * @param int $bbsId bbses.id
 * @param string $visibleRow
 * @param bool $contentCreatable true can edit the content, false not can edit the content.
 * @param int $postId
 * @param array $sortOrder
 * @return array
 */
	public function getPosts($bbsId, $userId, $contentEditable,
			$contentCreatable, $postId, $sortOrder, $visiblePostRow, $currentPage, $conditions = '') {

		//Todo:bbs_postはconditionsに纏めて渡すように変更する
		//debug($conditions);
		$conditions['bbs_id'] =	$bbsId;

		//作成権限まで
		if ($contentCreatable && ! $contentEditable) {
			//自分で書いた記事と公開中の記事を取得
			$conditions['or']['created_user'] = $userId;
			$conditions['or']['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		//作成・編集権限なし:公開中の記事のみ取得
		if (! $contentCreatable && ! $contentEditable) {
			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		//選択した記事を一件取得/////////////////////////////////////////////////
		if ($postId) {
			//view表示のために記事指定
			$conditions['id'] = $postId;

			//対象記事のみ取得
			$bbsPosts = $this->find('first', array(
					'recursive' => -1,
					'conditions' => $conditions,
				)
			);

			//日時フォーマット（一件）
			return $this->__setDateTime(array($bbsPosts), false);
		}

		//記事一覧取得//////////////////////////////////////////////////////////
		$conditions['parent_id'] = $postId;
		$group = array('BbsPost.key');
		$params = array(
				'conditions' => $conditions,
				'recursive' => -1,
				//Todo:array外して良いかも
				'order' => $sortOrder,
				'group' => $group,
				'limit' => $visiblePostRow,
				'page' => $currentPage,
			);
		$bbsPosts = $this->find('all',$params);

		//日時フォーマット（記事群）
		return $this->__setDateTime($bbsPosts, true);
	}

/**
 * get bbs data
 *
 * @param int $bbsId bbses.id
 * @param string $visibleRow
 * @param bool $contentCreatable true can edit the content, false not can edit the content.
 * @param int $postId
 * @param array $sortOrder
 * @return array
 */
	public function getCurrentComments($bbsId, $postId, $contentEditable, $contentCreatable) {
		$conditions = array(
			'bbs_id' => $bbsId,
			'id' => $postId
		);

 		if (! $contentCreatable && ! $contentEditable) {
			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		$posts = $this->find('first', array(
				'recursive' => -1,
				'conditions' => $conditions,
			)
		);

		//日時フォーマット（一件）
		return $this->__setDateTime(array($posts), false);
	}

/**
 * get bbs data
 *
 * @param string $userId
 * @param bool $contentEditable true can edit the content, false not can edit the content.
 * @param bool $contentCreatable true can create the content, false not can create the content.
 * @param array $sortOrder
 * @param string $visibleCommentRow
 * @param string $currentPage
 * @param array $conditions
 * @return array
 */
	public function getComments($userId, $contentEditable, $contentCreatable,
			$sortOrder, $visibleCommentRow, $currentPage, $conditions) {

		//作成権限まで
		if ($contentCreatable && ! $contentEditable) {
			//自分で書いた記事と公開中の記事を取得
			$conditions['or']['created_user'] = $userId;
			$conditions['or']['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		//作成・編集権限なし:公開中の記事のみ取得
		if (! $contentCreatable && ! $contentEditable) {
			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		//debug($conditions);
		$group = array('BbsPost.key');
		$params = array(
				'conditions' => $conditions,
				'recursive' => -1,
				'order' => $sortOrder,
				'group' => $group,
				'limit' => $visibleCommentRow,
				'page' => $currentPage,
			);
		$bbsComments = $this->find('all', $params);

		//日時フォーマット（記事群）
		return $this->__setDateTime($bbsComments, true);
	}

/**
 * get bbs data
 *
 * @param int $bbsId bbses.id
 * @param string $visibleRow
 * @param bool $contentCreatable true can edit the content, false not can edit the content.
 * @param int $postId
 * @param array $sortOrder
 * @return array
 */
//	public function getReplies($bbsId, $userId, $contentEditable,
//			$contentCreatable, $postId, $sortOrder, $visibleCommentRow, $currentPage) {
//
//		//Todo:木構造で取ってくる必要がある
//
//		//$bbsId => 掲示板を指定　//$postId =>
//		$conditions = array(
//			'bbs_id' => $bbsId,
//			'parent_id' => $postId,
//		);
//
//		//作成権限あり:自分で書いた記事のみ取得
//		if ($contentCreatable && ! $contentEditable) {
//			$conditions['created_user'] = $userId;
//		}
//
//		//作成・編集権限なし:公開中の記事のみ取得
//		if (! $contentCreatable && ! $contentEditable) {
//			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
//		}
//
//		$bbsComments = $this->find('all', array(
//				'recursive' => -1,
//				'conditions' => $conditions,
//				'order' => $sortOrder,
//				'limit' => $visibleCommentRow,
//				'page' => $currentPage,
//			)
//		);
//
//		//日時フォーマット（記事群）
//		return $this->__setDateTime($bbsComments, true);
//	}

/**
 * save posts
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function savePost($data) {
		$this->loadModels([
			'BbsPost' => 'Bbses.BbsPost',
		]);

		//トランザクションBegin
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try {
			if (!$this->validatePost($data)) {
				return false;
			}
			$this->data['BbsPost']['bbs_id'] = (int)$this->data['Bbs']['id'];
			$bbsPost = $this->save(null, false);
			if (!$bbsPost) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//トランザクションCommit
			$dataSource->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$dataSource->rollback();
			//エラー出力
			CakeLog::write(LOG_ERR, $ex);
			throw $ex;
		}
		return $bbsPost;
	}

/**
 * validate post
 *
 * @param array $data received post data
 * @return bool|array True on success, validation errors array on error
 */
	public function validatePost($data) {
		$this->set($data);
		$this->validates();
		return $this->validationErrors ? false : true;
	}

/**
 * __setDateTime method
 *
 * @return void
 */
	private function __setDateTime($bbs_posts, $is_array) {
		$today = date("Y-m-d");
		$year = date("Y");
		$i = 0;
		//再フォーマット
		foreach ($bbs_posts as $post) {
			$date = $post['BbsPost']['created'];
			//日付切り出し
			$createdDay = substr($post['BbsPost']['created'], 0, 10);
			//年切り出し
			$createdYear = substr($post['BbsPost']['created'], 0, 4);
			//変換
			if ($today === $createdDay) {
				//今日
				$bbs_posts[$i]['BbsPost']['create_time'] = date('G:i', strtotime($date));
			} else if ($year !== $createdYear) {
				//昨年以前
				$bbs_posts[$i]['BbsPost']['create_time'] =  date('Y/m/d', strtotime($date));
			} else if ($today > $createdDay) {
				//今日より前 かつ 今年
				$bbs_posts[$i]['BbsPost']['create_time'] =  date('m/d', strtotime($date));
			}
			$i++;
		}

		if ($is_array) {
			return $bbs_posts;
		}

		return $bbs_posts[0];
	}
}