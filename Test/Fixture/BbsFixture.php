<?php
/**
 * BbsFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * BbsFixture
 */
class BbsFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => 'ID | | | '),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'bbs key | 掲示板キー | Hash値 | ', 'charset' => 'utf8'),
		'block_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'bbs name | 掲示板名称 | | ', 'charset' => 'utf8'),
		'use_comment' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'auto_approval' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'use_like_button' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'use_unlike_button' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'post_create_authority' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'editor_publish_authority' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'general_publish_authority' => array('type' => 'boolean', 'null' => false, 'default' => false),
		'comment_create_authority' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'is_auto_translated' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'translation type. 0:original , 1:auto translation | 翻訳タイプ 0:オリジナル、1:自動翻訳 | | '),
		'translation_engine' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'translation engine | 翻訳エンジン | | ', 'charset' => 'utf8'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => '0', 'comment' => 'created user | 作成者 | users.id | '),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'created datetime | 作成日時 | | '),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => '0', 'comment' => 'modified user | 更新者 | users.id | '),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'modified datetime | 更新日時 | | '),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'key' => 'bbs_1',
			'block_id' => 'block_1',
			'name' => 'テスト掲示板1',
			'use_comment' => true,
			'auto_approval' => false,
			'use_like_button' => true,
			'use_unlike_button' => true,
			'post_create_authority' => true,
			'editor_publish_authority' => false,
			'general_publish_authority' => false,
			'comment_create_authority' => true,
			'created_user' => 1,
			'created' => '2014-06-18 02:06:22',
			'modified_user' => 1,
			'modified' => '2014-06-18 02:06:22',
		)
	);

}
