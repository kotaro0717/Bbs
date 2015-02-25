<?php
/**
 * BbsPostsUser Model
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
 * BbsPostsUser Model
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Bbses\Model
 */
class BbsPostsUser extends BbsesAppModel {

/**
 * use tables
 *
 * @var string
 */
	public $useTable = 'bbs_posts_users';

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
			'post_id' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			'user_id' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
		));
		return parent::beforeValidate($options);
	}
/**
 * getReadPostStatus
 *
 * @param int $postId bbsPosts.id
 * @param int $userId users.id
 * @return bool
 */
	public function getReadPostStatus($postId, $userId) {
		$conditions = array(
			'post_id' => $postId,
			'user_id' => $userId,
		);
		if (! $this->find('first', array(
				'recursive' => -1,
				'conditions' => $conditions,
				//'order' => 'BbsPostsUsers.id DESC'
			))
		) {
			//未読
			return false;
		}
		//既読
		return true;
	}

/**
 * save bbs
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveReadStatus($data) {
		$this->loadModels([
			'BbsPostsUser' => 'Bbses.BbsPostsUser',
		]);

		//トランザクションBegin
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try {
			if (! $this->validateBbsPostsUser($data)) {
				return false;
			}

			$bbsPostsUser = $this->save(null, false);
			if (! $bbsPostsUser) {
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
		return $bbsPostsUser;
	}

/**
 * validate announcement
 *
 * @param array $data received post data
 * @return bool True on success, false on error
 */
	public function validateBbsPostsUser($data) {
		$this->set($data);
		$this->validates();
		return $this->validationErrors ? false : true;
	}
}