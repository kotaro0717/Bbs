<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/bbses/js/bbses.js', false); ?>

<div id="nc-bbs-comment-view-<?php echo (int)$frameId; ?>"
		ng-controller="BbsComment"
		ng-init="initialize(<?php echo h(json_encode($bbsPosts)); ?>,
							<?php echo h(json_encode($bbsComments)); ?>)">

<!-- パンくずリスト -->
<ol class="breadcrumb">
	<li><a href="<?php echo $this->Html->url(
				'/bbses/bbses/index/' . $frameId) ?>">
		<?php echo $bbses['name']; ?></a>
	</li>
	<li>
		<a href="<?php echo $this->Html->url(
				'/bbses/bbsPosts/view/' . $frameId . '/' . $bbsPosts['id']) ?>">
			<?php echo mb_substr(strip_tags($bbsPosts['title']), 0, 20, 'UTF-8'); ?>
			<?php echo (mb_substr(strip_tags($bbsPosts['title']), 21, null, 'UTF-8') === '')? '' : '…'; ?>
		</a>
	</li>
	<li class="active">
		<?php echo mb_substr(strip_tags($bbsCurrentComments['title']), 0, 20, 'UTF-8'); ?>
		<?php echo (mb_substr(strip_tags($bbsCurrentComments['title']), 21, null, 'UTF-8') === '')? '' : '…'; ?>
	</li>
</ol>

<div class="text-left">
		<!-- 記事タイトル -->
		<h3><a href="<?php echo $this->Html->url(
					'/bbses/bbsPosts/view/' . $frameId . '/' . $bbsPosts['id']) ?>">
			<?php echo $bbsPosts['title']; ?></a>に戻る</h3>
</div>

<?php if ($bbsCurrentComments['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

	<div class="btn-group text-left">
		<button type="button" class="btn btn-default">
			<?php echo $narrowDown; ?>
		</button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<!-- URL:controller:BbsCommentsController action:view -->
			<!--     argument:frameId, postId(記事), $bbsCurrentComments(対象コメント) -->
			<!--     pageNumber(コメント一覧ページ番号), sortParams(ソート), visibleRow(表示件数), narrowDown(絞り込み) -->
			<li><a href="<?php echo $this->Html->url(
					'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . 6); ?>"><?php echo __d('bbses', 'Display all posts'); ?></a></li>
			<li><a href="<?php echo $this->Html->url(
					'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . NetCommonsBlockComponent::STATUS_PUBLISHED); ?>"><?php echo __d('bbses', 'Published'); ?></a></li>
			<li><a href="<?php echo $this->Html->url(
					'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . NetCommonsBlockComponent::STATUS_IN_DRAFT); ?>"><?php echo __d('net_commons', 'Temporary'); ?></a></li>
			<li><a href="<?php echo $this->Html->url(
					'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . 5); ?>"><?php echo __d('bbses', 'Disapproval'); ?></a></li>
		</ul>
	</div>

	<div class="text-left" style="float:right;">
		<!-- コメント数 -->
		<span class="glyphicon glyphicon-comment"><?php echo $commentNum; ?></span>
		<small><?php echo __d('bbses', 'Comments'); ?></small>&nbsp;

		<!-- ソート用プルダウン -->
		<div class="btn-group">
			<button type="button" class="btn btn-default">
				<?php echo $currentSortOrder; ?>
			</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<!-- URL:controller:BbsCommentsController action:view -->
				<!--     argument:frameId, postId(記事), $bbsCurrentComments(対象コメント) -->
				<!--     pageNumber(コメント一覧ページ番号), sortParams(ソート), visibleRow(表示件数), narrowDown(絞り込み) -->
				<li><a href="<?php echo $this->Html->url(
								'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . 1 . '/' . 1 . '/' . $currentVisibleRow . '/' . $narrowDownParams); ?>"><?php echo __d('bbses', 'Latest comment order'); ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . 1 . '/' . 2 . '/' . $currentVisibleRow . '/' . $narrowDownParams); ?>"><?php echo __d('bbses', 'Older comment order'); ?></a></li>
			</ul>
		</div>

		<!-- 表示件数 -->
		<div class="btn-group">
			<button type="button" class="btn btn-default">
				<?php echo $currentVisibleRow . BbsFrameSetting::DISPLAY_NUMBER_UNIT; ?>
			</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<!-- URL:controller:BbsCommentsController action:view -->
				<!--     argument:frameId, postId(記事), $bbsCurrentComments(対象コメント) -->
				<!--     pageNumber(コメント一覧ページ番号), sortParams(ソート), visibleRow(表示件数), narrowDown(絞り込み) -->
				<li><a href="<?php echo $this->Html->url(
								'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . 1 . '/' . $narrowDownParams); ?>"><?php echo '1' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . 5 . '/' . $narrowDownParams); ?>"><?php echo '5' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . 10 . '/' . $narrowDownParams); ?>"><?php echo '10' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . 20 . '/' . $narrowDownParams); ?>"><?php echo '20' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . 50 . '/' . $narrowDownParams); ?>"><?php echo '50' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . 100 . '/' . $narrowDownParams); ?>"><?php echo '100' . "件"; ?></a></li>
			</ul>
		</div>
	</div>
	<br />
<?php endif; ?>

<br />

<!-- 親記事 -->
<div class="panel-group">
	<div class="panel panel-info">
		<div class="panel-heading">
			<span class="text-left">
				<!-- id -->
				<span>
					<?php echo $this->Html->image('/bbses/img/avatar.PNG', array('alt'=>'アバターが設定されていません')); ?>
				</span>

				<!-- ユーザ情報 -->
				<span>
					<a href="" ng-click="user.showUser(<?php echo $bbsPosts['userId'] ?>)">
						<?php echo $bbsPosts['username']; ?>
					</a>
				</span>
			</span>
			<!-- 右に表示 -->
			<span class="text-left" style="float:right;">
				<!-- 作成時間 -->
				<span><?php echo $bbsPosts['create_time']; ?></span>
			</span>
		</div>
		<div class="panel-body">
			<!-- 本文 -->
			<div><?php echo $bbsPosts['content']; ?></div>
		</div>
		<div class="panel-footer">
			<!-- いいね！ -->
			<span class="text-left">
				<span class="glyphicon glyphicon-thumbs-up"><?php echo $bbsPosts['like_num']; ?></span>
				<span class="glyphicon glyphicon-thumbs-down"><?php echo $bbsPosts['unlike_num']; ?></span>
			</span>

			<!-- 右に表示 -->
			<span class="text-left" style="float:right;">
				<?php if ($bbsPosts['created_user'] === $userId && $contentCreatable
								&& $bbsPosts['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED
							|| $contentPublishable) : ?>

					<!-- 削除 -->
					<span class="text-left" style="float:right;">
						<?php echo $this->Form->create('',
							array(
								'div' => false,
								'type' => 'post',
								'url' => '/bbses/bbsPosts/delete/' . $frameId . '/' . $bbsPosts['id']
							)); ?>

							<?php echo $this->Form->button('<span class="glyphicon glyphicon-trash"></span>',
									array(
										'label' => false,
										'div' => false,
										'type' => 'submit',
										'class' => 'btn btn-danger btn-xs',
										'tooltip' => __d('bbses', 'Delete'),
									)); ?>
						<?php echo $this->Form->end(); ?>
					</span>

					<!-- 編集 -->
					<span class="text-left" style="float:right;">

						<a href="<?php echo $this->Html->url(
										'/bbses/bbsPosts/edit' . '/' . $frameId . '/' . $bbsPosts['id']); ?>"
										class="btn btn-primary btn-xs" tooltip="<?php echo __d('bbses', 'Edit'); ?>">
										<span class="glyphicon glyphicon-edit"></span>
						</a>
						&ensp;
					</span>
				<?php endif; ?>
			</span>

			<!-- Warn:style 一行に表示するため指定 -->
			<span class="text-left" style="float:right;">
				<?php if ($contentCreatable && $bbses['use_comment']
							&& $bbsPosts['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

					<?php echo $this->Form->create('',
								array(
									'div' => false,
									'type' => 'get',
									'url' => '/bbses/bbsComments/add/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsPosts['id']
								)); ?>

						<label>
							<!-- 引用するか否か -->
							<?php echo $this->Form->input('quotFlag',
										array(
											'label' => false,
											'div' => false,
											'type' => 'checkbox',
										)); ?>
							<?php echo __d('bbses', 'Quote this posts'); ?>
						</label>
						&nbsp;

						<?php echo $this->Form->button('<span class="glyphicon glyphicon-comment"></span>',
								array(
									'label' => false,
									'div' => false,
									'type' => 'submit',
									'class' => 'btn btn-success btn-xs',
									'tooltip' => __d('bbses', 'Write comment'),
								)); ?>
						&ensp;

					<?php echo $this->Form->end(); ?>
				<?php endif; ?>
			</span>
		</div>
	</div>
</div>

<!-- 対象の記事 -->
<div class="panel-group">
	<div class="panel panel-success">
		<div class="panel-heading">
			<span class="text-left">
				<!-- id -->
				<?php echo $bbsCurrentComments['id']; ?>.
				<span><?php echo $this->Html->image('/bbses/img/avatar.PNG', array('alt'=>'アバターが設定されていません')); ?></span>
				<!-- ユーザ情報 -->
				<span>
					<a href="" ng-click="user.showUser(<?php echo $bbsCurrentComments['userId'] ?>)">
						<?php echo $bbsCurrentComments['username']; ?>
					</a>
				</span>

				<!-- タイトル※対象記事のためリンクを貼らない -->
				<h4 style="display:inline;"><strong><?php echo $bbsCurrentComments['title']; ?></strong></h4>
				<!-- ステータス -->
				<span><?php echo $this->element('Bbses.status_label',
									array('status' => $bbsCurrentComments['status'])); ?></span>
			</span>

			<span class="text-left" style="float:right;">
				<!-- 作成時間 -->
				<span><?php echo $bbsCurrentComments['create_time']; ?></span>
			</span>
		</div>
		<div class="panel-body">
			<!-- 本文 -->
			<?php if ($bbsPosts['id'] !== $bbsCurrentComments['parent_id']) : ?>
				<div><a href="<?php echo $this->Html->url(
							'/bbses/bbsComments/view' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['parent_id']); ?>">
						>><?php echo $bbsCurrentComments['parent_id']; ?></a></div>
			<?php endif; ?>
			<div><?php echo $bbsCurrentComments['content']; ?></div>
		</div>

		<!-- フッター -->
		<div class="panel-footer">
			<!-- いいね！ -->
			<span class="text-left">
				<?php if ($bbsCurrentComments['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>
					<?php if ($bbses['use_like_button']) : ?>
						<span class="glyphicon glyphicon-thumbs-up"><?php echo $bbsCurrentComments['like_num']; ?></span>
					<?php endif; ?>
					<?php if ($bbses['use_unlike_button']) : ?>
						<span class="glyphicon glyphicon-thumbs-down"><?php echo $bbsCurrentComments['unlike_num']; ?></span>
					<?php endif; ?>
				<?php endif; ?>
				&nbsp;
			</span>

			<!-- 右に表示 -->
			<?php if ($bbsCurrentComments['created_user'] === $userId && $contentCreatable
							&& $bbsCurrentComments['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED
						|| $contentPublishable) : ?>

				<!-- 削除 -->
				<span class="text-left" style="float:right;">
					<?php echo $this->Form->create('',
						array(
							'div' => false,
							'type' => 'post',
							'url' => '/bbses/bbsComments/delete/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id']
						)); ?>

						<?php echo $this->Form->button('<span class="glyphicon glyphicon-trash"></span>',
								array(
									'label' => false,
									'div' => false,
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'tooltip' => __d('bbses', 'Delete'),
								)); ?>
					<?php echo $this->Form->end(); ?>
				</span>

				<!-- 編集 -->
				<span class="text-left" style="float:right;">
					<a href="<?php echo $this->Html->url(
							'/bbses/bbsComments/edit' . '/' . $frameId . '/' . $bbsCurrentComments['id']); ?>"
							class="btn btn-primary btn-xs" tooltip="<?php echo __d('bbses', 'Edit'); ?>">
							<span class="glyphicon glyphicon-edit"></span>
					</a>
					&ensp;
				</span>
			<?php endif; ?>

			<!-- Warn:style 一行に表示するため指定 -->
			<span class="text-left" style="float:right;">
				<?php if ($contentCreatable && $bbses['use_comment']
							&& $bbsCurrentComments['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

					<?php echo $this->Form->create('',
								array(
									'div' => false,
									'type' => 'get',
									'url' => '/bbses/bbsComments/add/' . $frameId . '/' . $bbsPosts['id'] . '/' . $bbsCurrentComments['id']
								)); ?>

						<label>
							<!-- 引用するか否か -->
							<?php echo $this->Form->input('quotFlag',
										array(
											'label' => false,
											'div' => false,
											'type' => 'checkbox',
										)); ?>
							<?php echo __d('bbses', 'Quote this posts'); ?>
						</label>
						&nbsp;

						<?php echo $this->Form->button('<span class="glyphicon glyphicon-comment"></span>',
								array(
									'label' => false,
									'div' => false,
									'type' => 'submit',
									'class' => 'btn btn-success btn-xs',
									'tooltip' => __d('bbses', 'Write comment'),
								)); ?>
						&ensp;

					<?php echo $this->Form->end(); ?>
				<?php endif; ?>

				<!-- '5'(非承認) -->
				<?php if ($bbsCurrentComments['status'] === '5' && $contentPublishable) : ?>
					<!-- 承認するボタン -->
					<?php echo $this->element('approving_buttons',
									array('comment' => $bbsCurrentComments)); ?>
					&emsp;
				<?php endif; ?>
			</span>
		</div>
	</div>
</div>

<?php if ($bbsCurrentComments['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

	<!-- 記事が公開されていない場合 -->
	<div class='col-md-offset-1'>
		<hr />
		<?php echo __d('bbses', 'This comments has not yet been published'); ?>
	</div>

<?php elseif (empty($bbsComments)) : ?>

	<!-- コメントがない場合 -->
	<div class='col-md-offset-1'>
		<hr />
		<?php echo __d('bbses', 'There are not comments'); ?>
	</div>

<?php else : ?>

	<!-- 全体の段落下げ -->
	<?php foreach ($bbsComments as $comment) { ?>
	<div class="panel-group col-md-offset-1 col-md-offset-1 col-xs-offset-1 col-sm-13 col-sm-13 col-xs-13">
		<div class="panel panel-default">
			<div class="panel-heading">

				<span class="text-left">
					<!-- id -->
					<?php echo $comment['id']; ?>.<span><?php echo $this->Html->image('/bbses/img/avatar.PNG', array('alt'=>'アバターが設定されていません')); ?></span>
					<!-- ユーザ情報 -->
					<span>
						<a href="" ng-click="user.showUser(<?php echo $comment['userId'] ?>)">
							<?php echo $comment['username']; ?>
						</a>
					</span>
					<!-- タイトル -->
					<a href="<?php echo $this->Html->url(
									'/bbses/bbsComments/view' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $comment['id']); ?>">
									<h4 style="display:inline;"><strong><?php echo $comment['title']; ?></strong></h4></a>
					<!-- ステータス -->
					<span><?php echo $this->element('Bbses.status_label',
										array('status' => $comment['status'])); ?></span>
				</span>

				<span class="text-left" style="float:right;">
					<!-- 時間 -->
					<span><?php echo $comment['create_time']; ?></span>
				</span>

			</div>
			<!-- 本文 -->
			<div class="panel panel-body">
				<?php if ($bbsCurrentComments['id'] !== $comment['parent_id']) : ?>
					<div><a href="<?php echo $this->Html->url(
								'/bbses/bbsComments/view' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $comment['id']); ?>">
							>><?php echo $comment['parent_id']; ?></a></div>
				<?php endif; ?>
				<div><?php echo $comment['content']; ?></div>
			</div>
			<!-- フッター -->
			<div class="panel-footer">
				<!-- いいね！ -->
				<span class="text-left">
					<?php if ($comment['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>
						<?php if ($bbses['use_like_button']) : ?>
							<span class="glyphicon glyphicon-thumbs-up"><?php echo $comment['like_num']; ?></span>
						<?php endif; ?>
						<?php if ($bbses['use_unlike_button']) : ?>
							<span class="glyphicon glyphicon-thumbs-down"><?php echo $comment['unlike_num']; ?></span>
						<?php endif; ?>
					<?php endif; ?>
					&nbsp;
				</span>

				<span class="text-left" style="float:right;">
					<!-- コメント編集/削除 -->
					<?php if ($comment['created_user'] === $userId && $contentCreatable
									&& $comment['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED
								|| $contentPublishable) : ?>

						<!-- 削除 -->
						<span class="text-left" style="float:right;">
							<?php echo $this->Form->create('',
								array(
									'div' => false,
									'type' => 'post',
									'url' => '/bbses/bbsComments/delete/' . $frameId . '/' .  $bbsPosts['id'] . '/' . $bbsCurrentComments['id'] . '/' . $comment['id']
								)); ?>

								<?php echo $this->Form->button('<span class="glyphicon glyphicon-trash"></span>',
										array(
											'label' => false,
											'div' => false,
											'type' => 'submit',
											'class' => 'btn btn-danger btn-xs',
											'tooltip' => __d('bbses', 'Delete'),
										)); ?>
							<?php echo $this->Form->end(); ?>
						</span>

						<!-- 編集 -->
						<span class="text-left" style="float:right;">
							<a href="<?php echo $this->Html->url(
									'/bbses/bbsComments/edit' . '/' . $frameId . '/' . $comment['id']); ?>"
									class="btn btn-primary btn-xs" tooltip="<?php echo __d('bbses', 'Edit'); ?>">
									<span class="glyphicon glyphicon-edit"></span>
							</a>
							&ensp;
						</span>
					<?php endif; ?>
				</span>

				<!-- コメント作成 -->
				<!-- Warn:style 一行に表示するため指定 -->
				<span class="text-left" style="float:right;">
					<?php if ($contentCreatable && $bbses['use_comment']
								&& $comment['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

						<?php echo $this->Form->create('',
									array(
										'div' => false,
										'type' => 'get',
										'url' => '/bbses/bbsComments/add/' . $frameId . '/' . $bbsPosts['id'] . '/' . $comment['id']
									)); ?>

							<label>
								<!-- 引用するか否か -->
								<?php echo $this->Form->input('quotFlag',
											array(
												'label' => false,
												'div' => false,
												'type' => 'checkbox',
											)); ?>
								<?php echo __d('bbses', 'Quote this posts'); ?>
							</label>
							&nbsp;

							<?php echo $this->Form->button('<span class="glyphicon glyphicon-comment"></span>',
									array(
										'label' => false,
										'div' => false,
										'type' => 'submit',
										'class' => 'btn btn-success btn-xs',
										'tooltip' => __d('bbses', 'Write comment'),
									)); ?>
							&ensp;

						<?php echo $this->Form->end(); ?>
					<?php endif; ?>

					<!-- '5'(非承認) -->
					<?php if ($comment['status'] === '5' && $contentPublishable) : ?>
						<!-- 承認するボタン -->
						<?php echo $this->element('approving_buttons',
										array('comment' => $comment)); ?>
						&emsp;
					<?php endif; ?>
				</span>
			</div>
		</div>
	</div>
	<?php } ?>

	<!-- ページャーの表示 -->
	<div class="text-center">
		<?php echo $this->element('BbsComments/pager'); ?>
	</div>

<?php endif; ?>

</div>