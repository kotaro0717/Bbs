<?php
/**
 * Bbs Model
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
 * Bbs Model
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Bbses\Model
 */
class Bbs extends BbsesAppModel {

/**
 * use tables
 *
 * @var string
 */

	public $useTable = 'bbses';
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
		'Block' => array(
			'className' => 'Blocks.Block',
			'foreignKey' => 'block_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
//		'CreatedUser' => array(
//			'className' => 'Users.UserAttributesUser',
//			'foreignKey' => false,
//			'conditions' => array(
//				'Bbs.created_user = CreatedUser.user_id',
//				'CreatedUser.key' => 'nickname'
//			),
//			'fields' => array('CreatedUser.key', 'CreatedUser.value'),
//			'order' => ''
//		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'BbsPost' => array(
            'className' => 'Bbses.BbsPost',
            'foreignKey' => 'bbs_id',
//            'conditions' => array('BbsPost.status' => '1'),
//            'order' => 'BbsPost.created DESC',
//            'limit' => '5',
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
			'block_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => false,
					'required' => true,
				)
			),
			'key' => array(
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
			'name' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Bbs name')),
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
	public function getBbs($blockId, $userId, $contentCreatable, $contentEditable, $is_post_list) {
		//固定化
		$blockId = '30';
		//$contentEditable = false; //TODO:debug用
		$contains = false;
		if ($is_post_list) {
			$contains = $this->__setContainableParams($userId, $contentCreatable, $contentEditable);
		}
		$conditions = array(
			'block_id' => $blockId,
		);
		$bbses = $this->find('first', array(
				'conditions' => $conditions,
				'order' => 'Bbs.id DESC',
				'contain' => $contains,
			)
		);
		return $bbses;
	}

/**
 * save bbs
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveBbs($data) {
		//モデル定義
		$this->setDataSource('master');
		$models = array(
			'Block' => 'Blocks.Block',
		);
		foreach ($models as $model => $class) {
			$this->$model = ClassRegistry::init($class);
			$this->$model->setDataSource('master');
		}
		//トランザクションBegin
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try {
			/* var_dump($this->Comment); */
			if (!$this->validateBbs($data)) {
				return false;
			}
			//ブロックの登録
			//$block = $this->Block->saveByFrameId($data['Frame']['id'], false);
			//掲示板の登録
			//$this->data['Bbs']['block_id'] = (int)$block['Block']['id'];
			$this->data['Bbs']['block_id'] = (int)$this->data['Block']['id'];
			$bbs = $this->save(null, false);
			if (!$bbs) {
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
		return $bbs;
	}

/**
 * validate announcement
 *
 * @param array $data received post data
 * @return bool True on success, false on error
 */
	public function validateBbs($data) {
		$this->set($data);
		$this->validates();
		return $this->validationErrors ? false : true;
	}

/**
 * __setContainableParams method
 *
 * @return void
 */
	private function __setContainableParams($userId, $contentCreatable, $contentEditable) {
		//containableビヘイビア用の条件

		//親記事のみ取得
		$containConditions['BbsPost.parent_id ='] = 0;

		//作成権限あり:自分で書いた記事のみ取得
		if ($contentCreatable && ! $contentEditable) {
			$containConditions['BbsPost.created_user ='] = $userId;
		}

		//作成・編集権限なし:公開中の記事のみ取得
		if (! $contentCreatable && ! $contentEditable) {
			$containConditions['BbsPost.status ='] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		$contains = array(
			'BbsPost' => array(
				'conditions' => $containConditions,
				'order' => 'BbsPost.created DESC',
				//'limit' => $visiblePostRow,
			)
		);
		return $contains;
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
			$date = $post['created'];
			//日付切り出し
			$createdDay = substr($post['created'], 0, 10);
			//年切り出し
			$createdYear = substr($post['created'], 0, 4);
			//変換
			if ($today === $createdDay) {
				//今日
				$bbs_posts[$i]['created'] = date('G:i', strtotime($date));
			} else if ($year !== $createdYear) {
				//昨年以前
				$bbs_posts[$i]['created'] =  date('Y/m/d', strtotime($date));
			} else if ($today > $createdDay) {
				//今日より前 かつ 今年
				$bbs_posts[$i]['created'] =  date('m/d', strtotime($date));
			}
			$i++;
		}
		return $bbs_posts;
	}
}