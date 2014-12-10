<?php
/**
 * BbsController Test Case
 *
* @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
* @link     http://www.netcommons.org NetCommons Project
* @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('BbsController', 'Bbs.Controller');

/**
 * Summary for BbsController Test Case
 */
class BbsControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.bbs.bb',
		'plugin.bbs.site_setting'
	);

}
