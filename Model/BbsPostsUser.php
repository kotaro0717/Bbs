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
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		// TODO: disabled for debug
		/* 'NetCommons.Publishable', */
		//'Containable'
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
		'BbsPost' => array(
			'className' => 'Bbses.BbsPost',
			'foreignKey' => 'post_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'Users.User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
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
			'user_id' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			'post_id' => array(
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
 * get bbs data
 *
 * @param int $userId users.id
 * @param int $postId bbs_posts.id
 * @return array
 */
	public function getUsers($userId, $postId) {
		$conditions = array(
			'user_id' => $userId,
			'post_id' => $postId,
		);

		$bbsPostUser = $this->find('first', array(
				'recursive' => -1,
				'conditions' => $conditions,
			)
		);

		return $bbsPostUser;
	}

/**
 * save post
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveUser($data) {
		//
	}

/**
 * validate post
 *
 * @param array $data received post data
 * @return bool|array True on success, validation errors array on error
 */
	public function validateUser($data) {
//		$this->set($data);
//		$this->validates();
//		return $this->validationErrors ? $this->validationErrors : true;
	}
}