<?php
/**
 * BbsAuthoritySettingsController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BbsAuthoritySettingsController', 'Bbses.Controller');

/**
 * BbsAuthoritySettingsController Test Case
 */
class BbsAuthoritySettingsControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.bbses.bbs',
		'plugin.bbses.bbs_frame_setting',
		'plugin.bbses.bbs_post',
		'plugin.bbses.bbs_posts_user',
		'plugin.bbses.site_setting'
	);

/**
 * test method
 *
 * @return void
 */
	public function test() {
		$this->assertTrue(true);
	}
}