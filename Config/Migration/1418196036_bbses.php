<?php
class Bbses extends CakeMigration {
	
/**
 * Migration description
 *
 * @var string
 */
	public $description = 'bbses';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'bbses' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => 'ID | | | '),
					'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'bbs key | 掲示板キー | Hash値 | ', 'charset' => 'utf8'),
					'block_id' => array('type' => 'integer', 'null' => false, 'default' => null),
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'bbs name | 掲示板名称 | | ', 'charset' => 'utf8'),
					'is_active' => array('type' => 'boolean', 'null' => false, 'default' => null),
					'comment_flag' => array('type' => 'boolean', 'null' => false, 'default' => true),
					'vote_flag' => array('type' => 'boolean', 'null' => false, 'default' => true),
					'posts_authority' => array('type' => 'boolean', 'null' => false, 'default' => true),
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
				),
				'bbs_frame_settings' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => 'ID | | | '),
					'frame_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'frame key | フレームKey | frames.key | ', 'charset' => 'utf8'),
					'visible_post_row' => array('type' => 'integer', 'null' => false, 'default' => '10', 'comment' => 'visible row, 1 post or 5, 10, 20, 50, 100 posts | 表示記事数 1件、5件、10件、20件、50件、100件 | | '),
					'visible_comment_row' => array('type' => 'integer', 'null' => false, 'default' => '10', 'comment' => 'visible row, 1 post or 5, 10, 20, 50, 100 posts | 表示記事数 1件、5件、10件、20件、50件、100件 | | '),
					'created_user' => array('type' => 'integer', 'null' => true, 'default' => '0', 'comment' => 'created user | 作成者 | users.id | '),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'created datetime | 作成日時 | | '),
					'modified_user' => array('type' => 'integer', 'null' => true, 'default' => '0', 'comment' => 'modified user | 更新者 | users.id | '),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'modified datetime | 更新日時 | | '),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'bbs_posts' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => 'ID | | | '),
					'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'bbs posts key | 掲示板記事キー | Hash値 | ', 'charset' => 'utf8'),
					'bbs_id' => array('type' => 'integer', 'null' => false, 'default' => null),
					'post_id' => array('type' => 'integer', 'null' => false, 'default' => null),
					'parent_id' => array('type' => 'integer', 'null' => false, 'default' => null),
					'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'comment' => 'public status, 1: public, 2: public pending, 3: draft during 4: remand | 公開状況 1:公開中、2:公開申請中、3:下書き中、4:差し戻し | | '),
					'title' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'title | タイトル | |', 'charset' => 'utf8'),
					'content' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'content | 本文 | |', 'charset' => 'utf8'),
					'comment_num' => array('type' => 'integer', 'null' => false, 'default' => '0'),
					'up_vote_num' => array('type' => 'integer', 'null' => false, 'default' => '0'),
					'down_vote_num' => array('type' => 'integer', 'null' => false, 'default' => '0'),
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
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'bbses', 'bbs_frame_settings', 'bbs_posts',
			),
		),
	);

/**
 * Records keyed by model name.
 *
 * @var array $records
 */
	public $records = array(
		'plugins' => array(
			array(
				'language_id' => 2,
				'key' => 'bbses',
				'namespace' => 'netcommons/bbses',
				'name' => '掲示板',
				'type' => 1,
			),
		),
		'plugins_roles' => array(
			array(
				'role_key' => 'room_administrator',
				'plugin_key' => 'bbses'
			),
		),
		'plugins_rooms' => array(
			array(
				'room_id' => '1',
				'plugin_key' => 'bbses'
			),
		),
		'frames' => array(
			array(
				'id' => '30',
				'language_id' => '2',
				'room_id' => '1',
				'box_id' => '3',
				'plugin_key' => 'bbses',
				'block_id' => '30',
				'key' => 'frame_30',
				'name' => '掲示板',
				'weight' => '1',
				'is_published' => true,
				'from' => null,
				'to' => null,
			),
		),
		'blocks' => array(
			array(
				'id' => '30',
				'language_id' => '2',
				'room_id' => '1',
				'key' => 'block_30',
			),
		),
		'bbs_frame_settings' => array(
			array(
				'id' => '1',
				'frame_key' => 'frame_30',
				'visible_post_row' => '10',
				'visible_comment_row' => '10',
			)
		),
		'bbses' => array(
			array(
				'id' => '1',
				'key' => 'bbs_1',
				'block_id' => '30',
				'name' => 'サンプル掲示板',
				'is_active' => true,
				'comment_flag' => true,
				'vote_flag' => true,
				'posts_authority' => true,
			)
		),
		'bbs_posts' => array(
			array(
				'id' => '1',
				'key' => 'bbs_post_1',
				'bbs_id' => '1',
				'post_id' => '0',
				'parent_id' => '0',
				'status' => '1',
				'title' => 'サンプル記事１',
				'content' => '<h2>サンプル記事の内容です。</h2><br /><strong>サンプル記事の内容です。サンプル記事の内容です。サンプル記事の内容です。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '0',
				'down_vote_num' => '0',
				'created_user' => '1',
				'created' => '2015-02-10 10:07:57',
			),
			array(
				'id' => '2',
				'key' => 'bbs_post_2',
				'bbs_id' => '1',
				'post_id' => '0',
				'parent_id' => '0',
				'status' => '4',
				'title' => 'サンプル記事２',
				'content' => '<h2>サンプル記事の内容です。</h2><br /><strong>サンプル記事の内容です。サンプル記事の内容です。サンプル記事の内容です。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '0',
				'down_vote_num' => '0',
				'created_user' => '1',
				'created' => '2015-02-10 11:12:57',
			),
			array(
				'id' => '3',
				'key' => 'bbs_post_3',
				'bbs_id' => '1',
				'post_id' => '0',
				'parent_id' => '0',
				'status' => '1',
				'title' => 'サンプル記事３',
				'content' => '<h2>サンプル記事の内容です。</h2><br /><strong>サンプル記事の内容です。サンプル記事の内容です。サンプル記事の内容です。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '0',
				'down_vote_num' => '0',
				'created_user' => '1',
				'created' => '2015-02-10 11:22:57',
			),
			array(
				'id' => '4',
				'key' => 'bbs_post_4',
				'bbs_id' => '1',
				'post_id' => '0',
				'parent_id' => '0',
				'status' => '3',
				'title' => 'サンプル記事４',
				'content' => '<h2>サンプル記事の内容です。</h2><br /><strong>サンプル記事の内容です。サンプル記事の内容です。サンプル記事の内容です。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '0',
				'down_vote_num' => '0',
				'created_user' => '1',
				'created' => '2015-02-10 11:34:57',
			),
			array(
				'id' => '5',
				'key' => 'bbs_post_5',
				'bbs_id' => '1',
				'post_id' => '0',
				'parent_id' => '0',
				'status' => '1',
				'title' => 'サンプル記事５',
				'content' => '<h2>サンプル記事の内容です。</h2><br /><strong>サンプル記事の内容です。サンプル記事の内容です。サンプル記事の内容です。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '0',
				'down_vote_num' => '0',
				'created_user' => '1',
				'created' => '2015-02-10 11:44:57',
			),
			array(
				'id' => '6',
				'key' => 'bbs_post_6',
				'bbs_id' => '1',
				'post_id' => '0',
				'parent_id' => '0',
				'status' => '2',
				'title' => 'サンプル記事６',
				'content' => '<h2>サンプル記事の内容です。</h2><br /><strong>サンプル記事の内容です。サンプル記事の内容です。サンプル記事の内容です。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '0',
				'down_vote_num' => '0',
				'created_user' => '1',
				'created' => '2015-02-10 11:54:57',
			),
			array(
				'id' => '7',
				'key' => 'bbs_post_7',
				'bbs_id' => '1',
				'post_id' => '0',
				'parent_id' => '0',
				'status' => '1',
				'title' => 'サンプル記事７',
				'content' => '<h2>サンプル記事の内容です。</h2><br /><strong>サンプル記事の内容です。サンプル記事の内容です。サンプル記事の内容です。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '0',
				'down_vote_num' => '0',
				'created_user' => '1',
				'created' => '2015-02-10 12:10:57',
			),
			array(
				'id' => '8',
				'key' => 'bbs_post_8',
				'bbs_id' => '1',
				'post_id' => '0',
				'parent_id' => '0',
				'status' => '1',
				'title' => 'サンプル記事８',
				'content' => '<h2>サンプル記事の内容です。</h2><br /><strong>サンプル記事の内容です。サンプル記事の内容です。サンプル記事の内容です。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '0',
				'down_vote_num' => '0',
				'created_user' => '1',
				'created' => '2015-02-10 12:24:57',
			),
			array(
				'id' => '9',
				'key' => 'bbs_post_9',
				'bbs_id' => '1',
				'post_id' => '0',
				'parent_id' => '0',
				'status' => '2',
				'title' => 'サンプル記事９',
				'content' => '<h2>サンプル記事の内容です。</h2><br /><strong>サンプル記事の内容です。サンプル記事の内容です。サンプル記事の内容です。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '0',
				'down_vote_num' => '0',
				'created_user' => '1',
				'created' => '2015-02-10 12:34:57',
			),
			array(
				'id' => '10',
				'key' => 'bbs_post_10',
				'bbs_id' => '1',
				'post_id' => '0',
				'parent_id' => '0',
				'status' => '1',
				'title' => 'サンプル記事１０',
				'content' => '<h2>サンプル記事１０の内容です。</h2><br /><strong>サンプル記事１０の内容です。サンプル記事１０の内容です。サンプル記事１０の内容です。</strong>',
				'comment_num' => '12',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:43:57',
			),
			array(
				'id' => '11',
				'key' => 'bbs_post_10_comment_1',
				'bbs_id' => '1',
				'post_id' => '10',
				'parent_id' => '10',
				'status' => '1',
				'title' => 'サンプル記事１０へのコメント１',
				'content' => '<h2>サンプル記事１０へのコメント。</h2><br /><strong>サンプル記事１０へのコメント。サンプル記事１０へのコメント。サンプル記事１０へのコメント。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:44:57',
			),
			array(
				'id' => '12',
				'key' => 'bbs_post_10_comment_2',
				'bbs_id' => '1',
				'post_id' => '10',
				'parent_id' => '10',
				'status' => '2',
				'title' => 'サンプル記事１０へのコメント２',
				'content' => '<h2>サンプル記事１０へのコメント。</h2><br /><strong>サンプル記事１０へのコメント。サンプル記事１０へのコメント。サンプル記事１０へのコメント。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:45:57',
			),
			array(
				'id' => '13',
				'key' => 'bbs_post_10_comment_3',
				'bbs_id' => '1',
				'post_id' => '10',
				'parent_id' => '10',
				'status' => '3',
				'title' => 'サンプル記事１０へのコメント３',
				'content' => '<h2>サンプル記事１０へのコメント。</h2><br /><strong>サンプル記事１０へのコメント。サンプル記事１０へのコメント。サンプル記事１０へのコメント。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:46:57',
			),
			array(
				'id' => '14',
				'key' => 'bbs_post_10_comment_4',
				'bbs_id' => '1',
				'post_id' => '10',
				'parent_id' => '10',
				'status' => '1',
				'title' => 'サンプル記事１０へのコメント４',
				'content' => '<h2>サンプル記事１０へのコメント。</h2><br /><strong>サンプル記事１０へのコメント。サンプル記事１０へのコメント。サンプル記事１０へのコメント。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:47:57',
			),
			array(
				'id' => '15',
				'key' => 'bbs_post_10_comment_5',
				'bbs_id' => '1',
				'post_id' => '10',
				'parent_id' => '10',
				'status' => '4',
				'title' => 'サンプル記事１０へのコメント５',
				'content' => '<h2>サンプル記事１０へのコメント。</h2><br /><strong>サンプル記事１０へのコメント。サンプル記事１０へのコメント。サンプル記事１０へのコメント。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:48:57',
			),
			array(
				'id' => '16',
				'key' => 'bbs_post_10_comment_6',
				'bbs_id' => '1',
				'post_id' => '10',
				'parent_id' => '10',
				'status' => '1',
				'title' => 'サンプル記事１０へのコメント６',
				'content' => '<h2>サンプル記事１０へのコメント。</h2><br /><strong>サンプル記事１０へのコメント。サンプル記事１０へのコメント。サンプル記事１０へのコメント。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:49:57',
			),
			array(
				'id' => '17',
				'key' => 'bbs_post_10_comment_7',
				'bbs_id' => '1',
				'post_id' => '10',
				'parent_id' => '10',
				'status' => '1',
				'title' => 'サンプル記事１０へのコメント７',
				'content' => '<h2>サンプル記事１０へのコメント。</h2><br /><strong>サンプル記事１０へのコメント。サンプル記事１０へのコメント。サンプル記事１０へのコメント。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:54:57',
			),
			array(
				'id' => '18',
				'key' => 'bbs_post_10_comment_8',
				'bbs_id' => '1',
				'post_id' => '10',
				'parent_id' => '10',
				'status' => '1',
				'title' => 'サンプル記事１０へのコメント８',
				'content' => '<h2>サンプル記事１０へのコメント。</h2><br /><strong>サンプル記事１０へのコメント。サンプル記事１０へのコメント。サンプル記事１０へのコメント。</strong>',
				'comment_num' => '4',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:55:57',
			),
			array(
				'id' => '19',
				'key' => 'bbs_post_10_comment_8_reply_1',
				'bbs_id' => '1',
				'post_id' => '10',
				'parent_id' => '18',
				'status' => '1',
				'title' => 'コメント８への返信１',
				'content' => '<h2>コメント８への返信。</h2><br /><strong>コメント８への返信。コメント８への返信。コメント８への返信。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:56:57',
			),
			array(
				'id' => '20',
				'key' => 'bbs_post_10_comment__8_reply_2',
				'bbs_id' => '1',
				'post_id' => '10',
				'parent_id' => '18',
				'status' => '1',
				'title' => 'コメント８への返信２',
				'content' => '<h2>コメント８への返信。</h2><br /><strong>コメント８への返信。コメント８への返信。コメント８への返信。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:57:57',
			),
			array(
				'id' => '21',
				'key' => 'bbs_post_10_comment__8_reply_3',
				'bbs_id' => '1',
				'post_id' => '10',
				'parent_id' => '18',
				'status' => '1',
				'title' => 'コメント８への返信３',
				'content' => '<h2>コメント８への返信。</h2><br /><strong>コメント８への返信。コメント８への返信。コメント８への返信。</strong>',
				'comment_num' => '1',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:58:57',
			),
			array(
				'id' => '22',
				'key' => 'bbs_post_10_comment__21_reply_1',
				'bbs_id' => '1',
				'post_id' => '10',
				'parent_id' => '21',
				'status' => '1',
				'title' => '返信３への返信１',
				'content' => '<h2>返信３への返信１。</h2><br /><strong>返信３への返信１。返信３への返信１。返信３への返信１。</strong>',
				'comment_num' => '0',
				'up_vote_num' => '12',
				'down_vote_num' => '1',
				'created_user' => '1',
				'created' => '2015-02-10 12:59:57',
			)
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction up or down direction of migration process
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction up or down direction of migration process
 * @return bool Should process continue
 */

	public function after($direction) {
		if ($direction === 'down') {
			return true;
		}
		foreach ($this->records as $model => $records) {
			if (!$this->updateRecords($model, $records)) {
				return false;
			}
		}
		return true;
	}

/**
 * Update model records
 *
 * @param string $model model name to update
 * @param string $records records to be stored
 * @param string $scope ?
 * @return bool Should process continue
 */
	public function updateRecords($model, $records, $scope = null) {
		$Model = $this->generateModel($model);
		foreach ($records as $record) {
			$Model->create();
			if (!$Model->save($record, false)) {
				return false;
			}
		}
		return true;
	}
}