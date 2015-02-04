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
		/* 'NetCommons.Publishable' */
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
//			'foreignKey' => 'bbs_key',
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
			'bbs_key' => array(
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
					'required' => true
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
 * @param int $frameId frames.id
 * @param int $blockId blocks.id
 * @param bool $contentEditable true can edit the content, false not can edit the content.
 * @return array
 */
	public function getPosts($bbsKey, $visiblePostRow, $contentEditable, $postId) {
		$conditions = array(
			'bbs_key' => $bbsKey,
			//TODO:parent_keyが0なら親記事、それ以外はコメントに
			'parent_id' => '0',
		);
 		if (! $contentEditable) {
			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}
		if ($postId) {
			//view表示のために記事指定
			$conditions['id'] = $postId;

			//対象記事のみ取得
			$bbs_posts = $this->find('all', array(
					'recursive' => 1,
					'conditions' => $conditions,
					'order' => 'BbsPost.created DESC',
				)
			);
			$bbs_posts = $this->__setDateTime($bbs_posts);
			return $bbs_posts;
		}
		$bbs_posts = $this->find('all', array(
				'recursive' => -1,
				'conditions' => $conditions,
				'order' => 'BbsPost.created DESC',
				'limit' => $visiblePostRow,
			)
		);
		$bbs_posts = $this->__setDateTime($bbs_posts);
		return $bbs_posts;
	}

/**
 * save post
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function savePost($data) {
//		//モデル定義
//		$this->setDataSource('master');
//		$models = array(
//			'Block' => 'Blocks.Block',
//			'Comment' => 'Comments.Comment',
//		);
//		foreach ($models as $model => $class) {
//			$this->$model = ClassRegistry::init($class);
//			$this->$model->setDataSource('master');
//		}
//		//トランザクションBegin
//		$dataSource = $this->getDataSource();
//		$dataSource->begin();
//		try {
//			//ブロックの登録
//			$block = $this->Block->saveByFrameId($data['Frame']['id'], false);
//			//お知らせの登録
//			$this->data['Announcement']['block_id'] = (int)$block['Block']['id'];
//			$announcement = $this->save(null, false);
//			if (! $announcement) {
//				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
//			}
//			//コメントの登録
//			if ($this->Comment->data) {
//				if (! $this->Comment->save(null, false)) {
//					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
//				}
//			}
//			//トランザクションCommit
//			$dataSource->commit();
//			return $announcement;
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