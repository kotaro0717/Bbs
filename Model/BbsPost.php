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
		'Containable'
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
		'BbsPost' => array(
            'className' => 'Bbses.BbsPost',
            'foreignKey' => 'parent_id',
            //'conditions' => array('Comment.status' => '1'),
            'order' => 'BbsPost.created DESC',
            //'limit' => '5',
            'dependent' => true
        )
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
			'parent_id' => array(
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
 * @param int $bbsId bbses.id
 * @param string $visibleRow
 * @param bool $contentCreatable true can edit the content, false not can edit the content.
 * @param int $postId
 * @param array $sortOrder
 * @return array
 */
	public function getPosts($bbsId, $userId, $contentEditable,
			$contentCreatable, $postId, $sortOrder, $visiblePostRow, $currentPage) {

		$conditions = array(
			'bbs_id' => $bbsId,
		);

//自分で書いた記事とステータスが&&になっているからまずい
//公開されているけど、自分で書いてないやつがでてこないはず。

		//作成権限あり:自分で書いた記事のみ取得
		if ($contentCreatable && ! $contentEditable) {
			$conditions['created_user'] = $userId;
		}

		//作成・編集権限なし:公開中の記事のみ取得
		if (! $contentCreatable && ! $contentEditable) {
			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		//選択した記事を一件取得
		if ($postId) {
			//view表示のために記事指定
			$conditions['id'] = $postId;

			//対象記事のみ取得
			$bbsPosts = $this->find('first', array(
					'recursive' => -1,
					'conditions' => $conditions,
				)
			);
			return $this->__setDateTime(array($bbsPosts));
		}

		$conditions['parent_id'] = $postId;
		//debug(array($conditions, $sortOrder, $visiblePostRow, $currentPage));
		$bbsPosts = $this->find('all', array(
				'recursive' => -1,
				'conditions' => $conditions,
				'order' => $sortOrder,
				'limit' => $visiblePostRow,
				'page' => $currentPage,
				//'contain' => $contains,
			)
		);

		//作成時間をフォーマット
		return $this->__setDateTime($bbsPosts);
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
	public function getCurrentPosts($bbsId, $contentCreatable, $postId) {
		$conditions = array(
			'bbs_id' => $bbsId,
			'id' => $postId
		);
 		if (! $contentCreatable) {
			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		$posts = $this->find('first', array(
				'recursive' => -1,
				'conditions' => $conditions,
			)
		);
		return $this->__setDateTime(array($posts));
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
	public function getComments($bbsId, $userId, $contentEditable,
			$contentCreatable, $postId, $sortOrder, $visibleCommentRow, $currentPage) {
		//利用箇所
		//記事詳細表示

		//$bbsId => 掲示板を指定　//$postId =>親記事のidを持つコメントを指定
		$conditions = array(
			'bbs_id' => $bbsId,
			'post_id' => $postId,
		);

		//作成権限あり:自分で書いた記事のみ取得
		if ($contentCreatable && ! $contentEditable) {
			$conditions['created_user'] = $userId;
		}

		//作成・編集権限なし:公開中の記事のみ取得
		if (! $contentCreatable && ! $contentEditable) {
			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

//		$contains = array(
//			'BbsPost' => array(
//				'BbsPost' => array(
//					'BbsPost' => array(
//						'BbsPost' => array(
//						)
//					)
//				)
//			)
//		);
		$bbsComments = $this->find('all', array(
				'recursive' => -1,
				'conditions' => $conditions,
				'order' => $sortOrder,
				'limit' => $visibleCommentRow,
				'page' => $currentPage,
//				'contain' => $contains,
			)
		);
		return $this->__setDateTime($bbsComments);
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
	public function getReplies($bbsId, $visibleRow, $contentCreatable, $postId, $sortOrder) {
		//利用箇所
		//コメント詳細表示

		//debug(array($bbsId, $visibleRow, $contentCreatable, $postId, $sortOrder));

		//$bbsId => 掲示板を指定　//$postId =>親記事のidを持つコメントを指定
		$conditions = array(
			'bbs_id' => $bbsId,
			'parent_id' => $postId,
		);

 		if (! $contentCreatable) {
			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		$bbsComments = $this->find('all', array(
				'recursive' => -1,
				'conditions' => $conditions,
				'order' => $sortOrder,
				'limit' => $visibleRow,
			)
		);
		//debug($bbsComments);
		return $this->__setDateTime($bbsComments);
	}

/**
 * save posts
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function savePosts($data) {
		debug($data);
		//モデル定義
//		$this->setDataSource('master');
//		$models = array(
//			'Bbs' => 'Bbses.Bbs',
//		);
//		foreach ($models as $model => $class) {
//			$this->$model = ClassRegistry::init($class);
//			$this->$model->setDataSource('master');
//		}
//		//トランザクションBegin
//		$dataSource = $this->getDataSource();
//		$dataSource->begin();
//		//validationを実行
//		//$ret = $this->__validateIframe($data);
//		$ret = null;
//		if (is_array($ret)) {
//			$this->validationErrors = $ret;
//			return false;
//		}
//		try {
			//記事データの登録
			//$this->data['Iframe']['block_id'] = (int)$block['Block']['id'];



//			$iframe = $this->save(null, false);
//			if (! $iframe) {
//				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
//			}
//			//コメントの登録
//			if ($this->Comment->data) {
//				if (! $this->Comment->save(null, false)) {
//					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
//				}
//			}
			//トランザクションCommit
//			$dataSource->commit();
//			return true;
//		} catch (Exception $ex) {
//			//トランザクションRollback
//			$dataSource->rollback();
//			//エラー出力
//			CakeLog::write(LOG_ERR, $ex);
//			throw $ex;
//		}
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
		return $this->validationErrors ? $this->validationErrors : true;
	}

/**
 * __setDateTime method
 *
 * @return void
 */
	private function __setDateTime($bbs_posts) {
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
				$bbs_posts[$i]['BbsPost']['created'] = date('G:i', strtotime($date));
			} else if ($year !== $createdYear) {
				//昨年以前
				$bbs_posts[$i]['BbsPost']['created'] =  date('Y/m/d', strtotime($date));
			} else if ($today > $createdDay) {
				//今日より前 かつ 今年
				$bbs_posts[$i]['BbsPost']['created'] =  date('m/d', strtotime($date));
			}
			$i++;
		}
		return $bbs_posts;
	}
}