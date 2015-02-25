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
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'BbsPost' => array(
            'className' => 'Bbses.BbsPost',
            'foreignKey' => 'bbs_key',
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
	public function getBbs($blockId, $userId, $contentCreatable, $contentEditable, $isPostList) {
		//固定化
		//$blockId = '30';
		$contains = false;

		//記事一覧の場合
		if ($isPostList) {
			//関係する記事のみ取得するための条件を設定
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
		$this->loadModels([
			'Bbs' => 'Bbses.Bbs',
			'Block' => 'Blocks.Block',
		]);

		//トランザクションBegin
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		try {
			/* var_dump($this->Comment); */
			if (!$this->validateBbs($data)) {
				return false;
			}
			//ブロックの登録
			$block = $this->Block->saveByFrameId($data['Frame']['id'], false);

			//掲示板の登録
			$this->data['Bbs']['block_id'] = (int)$block['Block']['id'];
			//$this->data['Bbs']['block_id'] = (int)$this->data['Block']['id'];
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
		$containConditions['BbsPost.parent_id ='] = null;

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

}