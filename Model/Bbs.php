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
		'CreatedUser' => array(
			'className' => 'Users.UserAttributesUser',
			'foreignKey' => false,
			'conditions' => array(
				'Bbs.created_user = CreatedUser.user_id',
				'CreatedUser.key' => 'nickname'
			),
			'fields' => array('CreatedUser.key', 'CreatedUser.value'),
			'order' => ''
		)
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
	public function getBbs($blockId) {
		$conditions = array(
			'block_id' => $blockId,
		);
		//containableビヘイビアで親記事を取得
		$contains = array(
			'BbsPost' => array(
				'conditions' => array(
					'BbsPost.parent_id =' => 0
				)
			)
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
//			$this->data['Bbs']['block_id'] = (int)$block['Block']['id'];
//			$bbs = $this->save(null, false);
//			if (! $bbs) {
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
//			return $bbs;
//		} catch (Exception $ex) {
//			//トランザクションRollback
//			$dataSource->rollback();
//			//エラー出力
//			CakeLog::write(LOG_ERR, $ex);
//			throw $ex;
//		}
	}
/**
 * validate bbs
 *
 * @param array $data received post data
 * @return bool|array True on success, validation errors array on error
 */
	public function validateBbs($data) {
//		$this->set($data);
//		$this->validates();
//		return $this->validationErrors ? $this->validationErrors : true;
	}
}