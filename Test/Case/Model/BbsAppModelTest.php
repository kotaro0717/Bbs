<?php
/**
 * Bbs Model Test Case
 *
 * @property Bbs $Bbs
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Bbs', 'Bbses.Model');
App::uses('BbsFrameSetting', 'Bbses.Model');
App::uses('BbsPost', 'Bbses.Model');
App::uses('BbsPostsUser', 'Bbses.Model');
App::uses('NetCommonsBlockComponent', 'NetCommons.Controller/Component');
App::uses('NetCommonsRoomRoleComponent', 'NetCommons.Controller/Component');
App::uses('YACakeTestCase', 'NetCommons.TestSuite');

/**
 * Bbs Model Test Case
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Bbses\Test\Case\Model
 */
class BbsAppModelTest extends YACakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.bbses.bbs',
		'plugin.bbses.bbs_frame_setting',
		'plugin.bbses.bbs_posts_user',
		'plugin.bbses.bbs_post',
		'plugin.bbses.block',
		'plugin.bbses.comment',
		'plugin.frames.box',
		'plugin.m17n.language',
		'plugin.rooms.room',
		'plugin.bbses.user_attributes_user',
		'plugin.bbses.user',
		'plugin.bbses.frame',
		'plugin.bbses.plugin',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		//$this->Bbs = ClassRegistry::init('Bbses.Bbs');
		//$this->BbsFrameSetting = ClassRegistry::init('Bbses.BbsFrameSetting');
		//$this->BbsPost = ClassRegistry::init('Bbses.BbsPost');
		//$this->BbsPostsUser = ClassRegistry::init('Bbses.BbsPostsUser');
		$this->Comment = ClassRegistry::init('Comments.Comment');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Bbs);
		//nset($this->BbsFrameSetting);
		//unset($this->BbsPost);
		//unset($this->BbsPostsUser);
		unset($this->Comment);
		parent::tearDown();
	}

/**
 * _assertArray method
 *
 * @param string $key target key
 * @param mixed $value array or string, number
 * @param array $result result data
 * @return void
 */
	protected function _assertArray($key, $value, $result) {
		if ($key !== null) {
			$this->assertArrayHasKey($key, $result);
			$target = $result[$key];
		} else {
			$target = $result;
		}
		if (is_array($value)) {
			foreach ($value as $nextKey => $nextValue) {
				$this->_assertArray($nextKey, $nextValue, $target);
			}
		} elseif (isset($value)) {
			$this->assertEquals($value, $target, 'key=' . print_r($key, true) . 'value=' . print_r($value, true) . 'result=' . print_r($result, true));
		}
	}

/**
 * testIndex method
 *
 * @return void
 */
	public function testIndex() {
	}
}
