<?php
/**
 * BbsFrameSetting Model
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
 * BbsFrameSetting Model
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Bbses\Model
 */
class BbsFrameSetting extends BbsesAppModel {

	const DISPLAY_NUMBER_UNIT = '件';

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
		'Frame' => array(
			'className' => 'Frames.Frame',
			'foreignKey' => 'frame_key',
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
			'frame_key' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			'visible_post_row' => array(
				'boolean' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			'visible_comment_row' => array(
				'boolean' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
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
	public function getBbsSetting($frameKey) {
		//固定化
		//$frameKey = 'frame_30';
		$conditions = array(
			'frame_key' => $frameKey,
		);
		if (!$bbsSetting = $this->find('first', array(
				'recursive' => -1,
				'conditions' => $conditions,
				'order' => 'BbsFrameSetting.id DESC'
			))
		) {
			//初期値を設定
			$bbsSetting = $this->create();
			$bbsSetting['BbsFrameSetting']['frame_key'] = $frameKey;
			$bbsSetting['BbsFrameSetting']['id'] = '0';
		}
		return $bbsSetting;
	}

/**
 * save bbs
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveBbsSetting($data) {
		//モデル定義
//		$this->setDataSource('master');
//		$models = array(
//			'Frame' => 'Frames.Frame',
//		);
//		foreach ($models as $model => $class) {
//			$this->$model = ClassRegistry::init($class);
//			$this->$model->setDataSource('master');
//		}
		//トランザクションBegin
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try {
			/* var_dump($this->Comment); */
			if (!$this->validateBbsSetting($data)) {
				return false;
			}

			//BbsFrameSettingの登録
			$this->data['BbsFrameSetting']['frame_key'] = $this->data['Frame']['key'];
			$bbsSetting = $this->save(null, false);
			if (!$bbsSetting) {
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
		return $bbsSetting;
	}

/**
 * validate announcement
 *
 * @param array $data received post data
 * @return bool True on success, false on error
 */
	public function validateBbsSetting($data) {
		$this->set($data);
		$this->validates();
		return $this->validationErrors ? false : true;
	}

/**
 * getDisplayNumberOptions
 *
 * @return array
 */
	public static function getDisplayNumberOptions() {
		return array(
			1 => 1 . self::DISPLAY_NUMBER_UNIT,
			5 => 5 . self::DISPLAY_NUMBER_UNIT,
			10 => 10 . self::DISPLAY_NUMBER_UNIT,
			20 => 20 . self::DISPLAY_NUMBER_UNIT,
			50 => 50 . self::DISPLAY_NUMBER_UNIT,
			100 => 100 . self::DISPLAY_NUMBER_UNIT,
		);
	}
}