<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/bbses/js/bbses.js', false); ?>

<div id="nc-bbs-post-view-<?php echo (int)$frameId; ?>"
		ng-controller="Bbses"
		ng-init="initialize(<?php echo h(json_encode($this->viewVars)); ?>)">

<!-- パンくずリスト -->
<ol class="breadcrumb">
	<li><a href="<?php echo $this->Html->url(
				'/bbses/bbses/view/' . $frameId . '/') ?>">
		<?php echo $bbses['name']; ?></a>
	</li>
	<li class="active"><?php echo __d('bbses', 'Display posts'); ?></li>
</ol>

<div class="text-left">
	<!-- 記事タイトル -->
	<h3><?php echo $bbsPosts['title']; ?></h3>
	<!-- ステータス -->
	<?php echo $this->element('NetCommons.status_label',
						array('status' => $bbsPosts['status'])); ?>
</div>

<?php if ($bbsPosts['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>
	<br />
	<div class="text-right">
		<!-- コメント数 -->
		<span class="glyphicon glyphicon-comment"><?php echo $bbsPosts['comment_num']; ?>&nbsp;</span>
		<!-- ソート用プルダウン -->
		<div class="btn-group">
			<button type="button" class="btn btn-default"><?php echo $currentCommentSortOrder; ?></button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/view' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 1 . '/' . 1 . '/' . $currentVisibleRow); ?>"><?php echo __d('bbses', 'Latest comment order'); ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/view' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 1 . '/' . 2 . '/' . $currentVisibleRow); ?>"><?php echo __d('bbses', 'Older comment order'); ?></a></li>
				<?php if ($contentCreatable) : ?>
					<li><a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/view' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 1 . '/' . 3 . '/' . $currentVisibleRow); ?>"><?php echo __d('bbses', 'Status order'); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
		<!-- 表示件数 -->
		<div class="btn-group">
			<button type="button" class="btn btn-default"><?php echo $currentVisibleRow . "件"; ?></button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/view' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 1 . '/' . $sortParams . '/' . 1); ?>"><?php echo '1' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/view' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 1 . '/' . $sortParams . '/' . 5); ?>"><?php echo '5' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/view' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 1 . '/' . $sortParams . '/' . 10); ?>"><?php echo '10' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/view' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 1 . '/' . $sortParams . '/' . 20); ?>"><?php echo '20' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/view' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 1 . '/' . $sortParams . '/' . 50); ?>"><?php echo '50' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/view' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 1 . '/' . $sortParams . '/' . 100); ?>"><?php echo '100' . "件"; ?></a></li>
			</ul>
		</div>
	</div>
<?php endif; ?>

<br />

<!-- 親記事 -->
<div class="panel-group">
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="text-left">
				<!-- id -->
				1.<span><?php echo $this->Html->image('/bbses/img/avatar.PNG', array('alt'=>'アバターが設定されていません')); ?></span>
				<!-- ユーザ情報 -->
				<span><a href=""><?php echo $bbsPosts['username']; ?></a></span>

				<!-- 右に表示 -->
				<span class="text-left" style="float:right;">
					<!-- 作成時間 -->
					<span><?php echo $bbsPosts['created']; ?></span>
				</span>
			</div>
		</div>
		<div class="panel-body">
			<!-- 本文 -->
			<div><?php echo $bbsPosts['content']; ?></div>
		</div>
		<div class="panel-footer">
			<div class="text-left">
				<!-- いいね！ -->
				<span class="glyphicon glyphicon-thumbs-up"><?php echo $bbsPosts['up_vote_num']; ?></span>
				<span class="glyphicon glyphicon-thumbs-down"><?php echo $bbsPosts['down_vote_num']; ?></span>

				<!-- 右に表示 -->
				<div class="text-left" style="float:right;">
					<?php if ($contentCreatable && $bbses['comment_flag']
								&& $bbsPosts['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

						<a href="<?php echo $this->Html->url(
							'/bbses/bbsPosts/add' . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 2); ?>"
							class="btn btn-success btn-xs" tooltip="<?php echo __d('bbses', 'Write comment'); ?>">
							<span class="glyphicon glyphicon-comment"></span></a>
					<?php endif; ?>

					<?php if ($bbsPosts['created_user'] === $userId && $contentCreatable
									&& $bbsPosts['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED
								|| $contentPublishable) : ?>

						<a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/edit' . '/' . $frameId . '/' . $bbsPosts['id']); ?>"
								class="btn btn-primary btn-xs" tooltip="<?php echo __d('bbses', 'Edit'); ?>">
								<span class="glyphicon glyphicon-edit"></span></a>

						<button ng-click="delete(<?php echo $bbsPosts['id']; ?>)"
								class="btn btn-danger btn-xs" tooltip="<?php echo __d('bbses', 'Delete'); ?>"><span class="glyphicon glyphicon-trash"></span>
						</button>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if ($bbsPosts['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

	<!-- 記事が公開されていない場合 -->
	<div class='col-md-offset-1'>
		<hr />
		<?php echo __d('bbses', 'This posts has not yet been published'); ?>
	</div>

<?php elseif (empty($bbsComments)) : ?>

	<!-- コメントがない場合 -->
	<div class='col-md-offset-1'>
		<hr />
		<?php echo __d('bbses', 'There are not comments now'); ?>
	</div>

<?php else : ?>

	<!-- コメントの表示 -->
	<?php foreach ($bbsComments as $comment) { ?>
	<div class="panel-group col-md-offset-1 col-md-offset-1 col-xs-offset-1 col-sm-13 col-sm-13 col-xs-13">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="text-left">
					<!-- id -->
					<?php echo $comment['id']; ?>.<span><?php echo $this->Html->image('/bbses/img/avatar.PNG', array('alt'=>'アバターが設定されていません')); ?></span>
					<!-- ユーザ情報 -->
					<span><a href=""><?php echo $comment['username']; ?></a></span>

					<!-- タイトル -->
					<a href="<?php echo $this->Html->url(
									'/bbses/bbsComments/view' . '/' . $frameId . '/' . $comment['post_id'] . '/' . $comment['id']); ?>">
									<h4 style="display:inline;"><strong><?php echo $comment['title']; ?></strong></h4></a>

					<!-- ステータス -->
					<span><?php echo $this->element('NetCommons.status_label',
										array('status' => $comment['status'])); ?></span>

					<!-- 右に表示 -->
					<div class="text-left" style="float:right;">
						<!-- 時間 -->
						<span><?php echo $comment['created']; ?></span>
					</div>
				</div>
			</div>
			<!-- 本文 -->
			<div class="panel panel-body">
				<?php if ($comment['post_id'] !== $comment['parent_id']) : ?>
					<div><a href="<?php echo $this->Html->url(
								'/bbses/bbsComments/view' . '/' . $frameId . '/' . $comment['post_id'] . '/' . $comment['parent_id']); ?>">
							>><?php echo $comment['parent_id']; ?></a></div>
				<?php endif; ?>
				<div><?php echo $comment['content']; ?></div>
			</div>
			<!-- フッター -->
			<div class="panel-footer">
				<!-- いいね！ -->
				<div class="text-left">
					<div class="text-right" style="float:right;">
						<!-- コメント作成/編集/削除 -->
						<?php if ($contentCreatable && $bbses['comment_flag']
									&& $comment['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

							<a href="<?php echo $this->Html->url(
									'/bbses/bbsPosts/add' . '/' . $frameId . '/' . $comment['id'] . '/' . 2); ?>"
									class="btn btn-success btn-xs" tooltip="<?php echo __d('bbses', 'Write comment'); ?>"><span class="glyphicon glyphicon-comment"></span></a>
						<?php endif; ?>

						<?php if ($comment['created_user'] === $userId && $contentCreatable
										&& $comment['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED
									|| $contentPublishable) : ?>

							<a href="<?php echo $this->Html->url(
									'/bbses/bbsPosts/edit' . '/' . $frameId . '/' . $comment['id']); ?>"
									class="btn btn-primary btn-xs" tooltip="<?php echo __d('bbses', 'Edit'); ?>"><span class="glyphicon glyphicon-edit"></span></a>

							<button ng-click="delete(<?php echo $comment['id']; ?>)"
									class="btn btn-danger btn-xs" tooltip="<?php echo __d('bbses', 'Delete'); ?>"><span class="glyphicon glyphicon-trash"></span>
							</button>
						<?php endif; ?>
					</div>
					<span class="glyphicon glyphicon-thumbs-up"><?php echo $comment['up_vote_num']; ?></span>
					<span class="glyphicon glyphicon-thumbs-down"><?php echo $comment['down_vote_num']; ?></span>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<!-- ページャーの表示 -->
	<div class="text-center">
		<?php echo $this->element('BbsPosts/pager'); ?>
	</div>

<?php endif; ?>

</div>