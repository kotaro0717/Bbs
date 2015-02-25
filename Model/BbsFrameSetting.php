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
 * get bbs setting data
 *
 * @param int $frameKey frames.key
 * @return array
 */
	public function getBbsSetting($frameKey) {
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
			$bbsSetting = $this->create($conditions);
			$this->saveBbsSetting($bbsSetting);
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
		$this->loadModels([
			'BbsFrameSetting' => 'Bbses.BbsFrameSetting',
		]);

		//トランザクションBegin
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try {
			if (!$this->validateBbsSetting($data)) {
				return false;
			}

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