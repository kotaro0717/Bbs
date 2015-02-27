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

	const DISPLAY_MAX_TITLE_LENGTH = '50';
	const DISPLAY_MAX_CONTENT_LENGTH = '200';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Tree',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Bbs' => array(
			'className' => 'Bbses.Bbs',
			'foreignKey' => 'bbs_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
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
			'bbs_key' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),

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
 * @param int $userId users.id
 * @param bool $contentEditable true can edit the content, false not can edit the content.
 * @param bool $contentCreatable true can create the content, false not can create the content.
 * @param array $conditions databese find condition
 * @return array
 */
	public function getOnePosts($userId, $contentEditable, $contentCreatable, $conditions) {
		if (! $conditions['id']) {
			return;
		}

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

		//対象記事のみ取得
		$bbsPosts = $this->find('first', array(
				'recursive' => -1,
				'conditions' => $conditions,
			)
		);

		//日時フォーマット（一件）
		return $this->__setDateTime(array($bbsPosts), false);
	}

/**
 * get bbs data
 *
 * @param int $userId users.id
 * @param bool $contentEditable true can edit the content, false not can edit the content.
 * @param bool $contentCreatable true can create the content, false not can create the content.
 * @param array $sortOrder databese find condition
 * @param int $visiblePostRow databese find condition
 * @param int $currentPage databese find condition
 * @param array $conditions databese find condition
 * @return array
 */
	public function getPosts($userId, $contentEditable, $contentCreatable,
				$sortOrder = '', $visiblePostRow = '', $currentPage = '', $conditions = '') {
		//他人の編集中の記事・コメントが見れない人
		if ($contentCreatable && ! $contentEditable) {
			$conditions['or']['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
			$conditions['or']['and']['created_user'] = $userId;
			$conditions['or']['and']['status <>'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		//公開中の記事・コメントしか見れない人
		if (! $contentCreatable && ! $contentEditable) {
			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		//記事一覧取得
		$group = array('BbsPost.key');
		$params = array(
				'conditions' => $conditions,
				'recursive' => -1,
				'order' => $sortOrder,
				'group' => $group,
				'limit' => $visiblePostRow,
				'page' => $currentPage,
			);
		$bbsPosts = $this->find('all', $params);

		//日時フォーマット（記事群）
		return $this->__setDateTime($bbsPosts, true);
	}

/**
 * get bbs data
 *
 * @param int $bbsKey bbses.key
 * @param int $postId bbsPosts.id
 * @param bool $contentEditable true can edit the content, false not can edit the content.
 * @param bool $contentCreatable true can create the content, false not can create the content.
 * @return array
 */
//	public function getCurrentComments($bbsKey, $postId, $contentEditable, $contentCreatable) {
//		$conditions = array(
//			'bbs_key' => $bbsKey,
//			'id' => $postId
//		);
//
//		if (! $contentCreatable && ! $contentEditable) {
//			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
//		}
//
//		$posts = $this->find('first', array(
//				'recursive' => -1,
//				'conditions' => $conditions,
//			)
//		);
//
//		//日時フォーマット（一件）
//		return $this->__setDateTime(array($posts), false);
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
 * @param array $bbsPosts bbsPosts
 * @param bool $isArray true is posts list, false is a posts
 * @return void
 */
	private function __setDateTime($bbsPosts, $isArray) {
		$today = date("Y-m-d");
		$year = date("Y");
		$i = 0;
		//再フォーマット
		foreach ($bbsPosts as $post) {
			$date = $post['BbsPost']['created'];
			//日付切り出し
			$createdDay = substr($post['BbsPost']['created'], 0, 10);
			//年切り出し
			$createdYear = substr($post['BbsPost']['created'], 0, 4);
			//変換
			if ($today === $createdDay) {
				//今日
				$bbsPosts[$i]['BbsPost']['create_time'] = date('G:i', strtotime($date));
			} elseif ($year !== $createdYear) {
				//昨年以前
				$bbsPosts[$i]['BbsPost']['create_time'] = date('Y/m/d', strtotime($date));
			} elseif ($today > $createdDay) {
				//今日より前 かつ 今年
				$bbsPosts[$i]['BbsPost']['create_time'] = date('m/d', strtotime($date));
			}
			$i++;
		}
		if ($isArray) {
			return $bbsPosts;
		}
		return $bbsPosts[0];
	}
}