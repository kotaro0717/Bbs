<!-- パンくずリスト -->
<ol class="breadcrumb">
	<li><a href="<?php echo $this->Html->url(
				'/bbses/bbses/index/' . $frameId) ?>">
		<?php echo $dataForView['bbses']['name']; ?></a>
	</li>
	<li><a href="<?php echo $this->Html->url(
				'/bbses/bbsPosts/view/' . $frameId . '/' . $dataForView['bbsPosts']['id']) ?>">
		<?php echo $dataForView['bbsPosts']['title']; ?></a>
	</li>
	<li class="active">#<?php echo $dataForView['bbsCurrentPosts']['id']; ?></li>
</ol>

<div class="text-left">
	<!-- 記事タイトル -->
	<h3><a href="<?php echo $this->Html->url(
				'/bbses/bbsPosts/view/' . $frameId . '/' . $dataForView['bbsPosts']['id']) ?>">
		<?php echo $dataForView['bbsPosts']['title']; ?></a>に戻る</h3>

	<div class="text-left" style="float:right;">
		<!-- コメント数 -->
		<span class="glyphicon glyphicon-comment"><?php echo $dataForView['bbsCurrentPosts']['commentNum']; ?>&nbsp;</span>
		<!-- ソート用プルダウン -->
		<div class="btn-group">
			<button type="button" class="btn btn-default"><?php echo "未実装"; ?></button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/view' . '/' . $frameId . '/' . 1 . '/' . 1); ?>"><?php echo __d('bbses', 'Latest comment order'); ?></a></li>
				<li><a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/view' . '/' . $frameId . '/' . 1 . '/' . 2); ?>"><?php echo __d('bbses', 'Older comment order'); ?></a></li>
				<?php if ($contentCreatable) : ?>
					<li><a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/view' . '/' . $frameId . '/' . 1 . '/' . 3); ?>"><?php echo __d('bbses', 'Status order'); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
		<!-- 表示件数 -->
		<div class="btn-group">
			<button type="button" class="btn btn-default"><?php echo "未実装"; ?></button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="#"><?php echo '1' . "件"; ?></a></li>
				<li><a href="#"><?php echo '5' . "件"; ?></a></li>
				<li><a href="#"><?php echo '10' . "件"; ?></a></li>
				<li><a href="#"><?php echo '20' . "件"; ?></a></li>
				<li><a href="#"><?php echo '50' . "件"; ?></a></li>
				<li><a href="#"><?php echo '100' . "件"; ?></a></li>
			</ul>
		</div>
	</div>

</div><br />

<!-- 親記事 -->
<div class="panel-group">
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="text-left">
				<!-- id -->
				1.<span><?php echo $this->Html->image('/bbses/img/avatar.PNG', array('alt'=>'アバターが設定されていません')); ?></span>
				<!-- ユーザ情報 -->
				<span><a href=""><?php echo $dataForView['postUsers']['username']; ?></a></span>
				<!-- ステータス -->
				<span><?php echo $this->element('NetCommons.status_label',
									array('status' => $dataForView['bbsPosts']['status'])); ?></span>

				<!-- 右に表示 -->
				<div class="text-left" style="float:left;">
					<!-- 作成時間 -->
					<span><?php echo $dataForView['bbsPosts']['created']; ?></span>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<!-- 本文 -->
			<div><?php echo $dataForView['bbsPosts']['content']; ?></div>
		</div>
		<div class="panel-footer">
			<!-- いいね！ -->
			<div class="text-left">
				<span class="glyphicon glyphicon-thumbs-up"><?php echo $dataForView['bbsPosts']['upVoteNum']; ?></span>
				<span class="glyphicon glyphicon-thumbs-down"><?php echo $dataForView['bbsPosts']['downVoteNum']; ?></span>

				<!-- 右に表示 -->
				<div class="text-left" style="float:right;">
					<?php if ($contentCreatable && $dataForView['bbses']['commentFlag']
								&& $dataForView['bbsPosts']['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

						<a href="<?php echo $this->Html->url(
							'/bbses/bbsPosts/add' . '/' . $frameId . '/' . $dataForView['bbsPosts']['id'] . '/' . 2); ?>"
							class="btn btn-success btn-xs" tooltip="<?php echo __d('bbses', 'Write comment'); ?>">
							<span class="glyphicon glyphicon-comment"></span></a>
					<?php endif; ?>

					<?php if ($dataForView['bbsPosts']['createdUser'] === $dataForView['userId'] && $contentCreatable
									&& $dataForView['bbsPosts']['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED
								|| $contentPublishable) : ?>

						<a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/edit' . '/' . $frameId . '/' . $dataForView['bbsPosts']['id']); ?>"
								class="btn btn-primary btn-xs" tooltip="<?php echo __d('bbses', 'Edit'); ?>">
								<span class="glyphicon glyphicon-edit"></span></a>

						<button ng-click="delete(<?php echo $dataForView['bbsPosts']['id']; ?>)"
								class="btn btn-danger btn-xs" tooltip="<?php echo __d('bbses', 'Delete'); ?>"><span class="glyphicon glyphicon-trash"></span>
						</button>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- 対象の記事 -->
<div class="panel-group">
	<div class="panel panel-success">
		<div class="panel-heading">
			<div class="text-left">
				<!-- id -->
				<?php echo $dataForView['bbsCurrentPosts']['id']; ?>.
				<span><?php echo $this->Html->image('/bbses/img/avatar.PNG', array('alt'=>'アバターが設定されていません')); ?></span>
				<!-- ユーザ情報 -->
				<span><a href=""><?php echo $dataForView['currentPostUsers']['username']; ?></a></span>
				<!-- タイトル※対象記事のためリンクを貼らない -->
				<h4 style="display:inline;"><strong><?php echo $dataForView['bbsCurrentPosts']['title']; ?></strong></h4>
				<!-- ステータス -->
				<span><?php echo $this->element('NetCommons.status_label',
									array('status' => $dataForView['bbsCurrentPosts']['status'])); ?></span>

				<!-- 右に表示 -->
				<div class="text-left" style="float:right;">
					<!-- 作成時間 -->
					<span><?php echo $dataForView['bbsCurrentPosts']['created']; ?></span>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<!-- 本文 -->
			<?php if ($dataForView['bbsCurrentPosts']['postId'] !== $dataForView['bbsCurrentPosts']['parentId']) : ?>
				<div><a href="<?php echo $this->Html->url(
							'/bbses/bbsComments/view' . '/' . $frameId . '/' . $dataForView['bbsCurrentPosts']['postId'] . '/' . $dataForView['bbsCurrentPosts']['parentId']); ?>">
						>><?php echo $dataForView['bbsCurrentPosts']['parentId']; ?></a></div>
			<?php endif; ?>
			<div><?php echo $dataForView['bbsCurrentPosts']['content']; ?></div>
		</div>
		<div class="panel-footer">
			<!-- いいね！ -->
			<div class="text-left">
				<span class="glyphicon glyphicon-thumbs-up"><?php echo $dataForView['bbsCurrentPosts']['upVoteNum']; ?></span>
				<span class="glyphicon glyphicon-thumbs-down"><?php echo $dataForView['bbsCurrentPosts']['downVoteNum']; ?></span>

				<!-- 右に表示 -->
				<div class="text-left" style="float:right;">
					<?php if ($contentCreatable && $dataForView['bbses']['commentFlag']
								&& $dataForView['bbsCurrentPosts']['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

						<a href="<?php echo $this->Html->url(
							'/bbses/bbsPosts/add' . '/' . $frameId . '/' . $dataForView['bbsPosts']['id'] . '/' . 2); ?>"
							class="btn btn-success btn-xs" tooltip="<?php echo __d('bbses', 'Write comment'); ?>">
							<span class="glyphicon glyphicon-comment"></span></a>
					<?php endif; ?>

					<?php if ($dataForView['bbsCurrentPosts']['createdUser'] === $dataForView['userId'] && $contentCreatable
									&& $dataForView['bbsCurrentPosts']['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED
								|| $contentPublishable) : ?>

						<a href="<?php echo $this->Html->url(
								'/bbses/bbsPosts/edit' . '/' . $frameId . '/' . $dataForView['bbsCurrentPosts']['id']); ?>"
								class="btn btn-primary btn-xs" tooltip="<?php echo __d('bbses', 'Edit'); ?>">
								<span class="glyphicon glyphicon-edit"></span></a>

						<button ng-click="delete(<?php echo $dataForView['bbsCurrentPosts']['id']; ?>)"
								class="btn btn-danger btn-xs" tooltip="<?php echo __d('bbses', 'Delete'); ?>"><span class="glyphicon glyphicon-trash"></span>
						</button>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if (isset($dataForView['bbsComments'])) : ?>

	<?php //debug($dataForView['bbsComments']);?>

	<!-- 全体の段落下げ -->
	<?php foreach ($dataForView['bbsComments'] as $comment) { ?>
	<div class="panel-group col-md-offset-1 col-md-offset-1 col-xs-offset-1 col-sm-13 col-sm-13 col-xs-13">
		<div class="panel panel-default">
			<div class="panel-heading">
				<!-- id -->
				<?php echo $comment['id']; ?>.<span><?php echo $this->Html->image('/bbses/img/avatar.PNG', array('alt'=>'アバターが設定されていません')); ?></span>
				<!-- ユーザ情報 -->
				<span><a href=""><?php echo $comment['username']; ?></a></span>
				<!-- タイトル -->
				<a href="<?php echo $this->Html->url(
								'/bbses/bbsComments/view' . '/' . $frameId . '/' . $comment['postId'] . '/' . $comment['id']); ?>">
								<h4 style="display:inline;"><strong><?php echo $comment['title']; ?></strong></h4></a>
				<!-- 時間 -->
				<span><?php echo $comment['created']; ?></span>
				<!-- ステータス -->
				<span><?php echo $this->element('NetCommons.status_label',
									array('status' => $comment['status'])); ?></span>
			</div>
			<!-- 本文 -->
			<div class="panel panel-body">
				<?php if ($comment['postId'] !== $comment['parentId']) : ?>
					<div><a href="<?php echo $this->Html->url(
								'/bbses/bbsComments/view' . '/' . $frameId . '/' . $comment['postId'] . '/' . $comment['parentId']); ?>">
							>><?php echo $comment['parentId']; ?></a></div>
				<?php endif; ?>
				<div><?php echo $comment['content']; ?></div>
			</div>
			<!-- フッター -->
			<div class="panel-footer">
				<!-- いいね！ -->
				<div class="text-left">
					<div class="text-left" style="float:right;">
						<!-- コメント作成/編集/削除 -->
						<?php if ($contentCreatable && $dataForView['bbses']['commentFlag']
									&& $comment['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

							<a href="<?php echo $this->Html->url(
									'/bbses/bbsPosts/add' . '/' . $frameId . '/' . $comment['id'] . '/' . 2); ?>"
									class="btn btn-success btn-xs" tooltip="<?php echo __d('bbses', 'Write comment'); ?>"><span class="glyphicon glyphicon-comment"></span></a>
						<?php endif; ?>

						<?php if ($comment['createdUser'] === $dataForView['userId'] && $contentCreatable
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
					<span class="glyphicon glyphicon-thumbs-up"><?php echo $comment['upVoteNum']; ?></span>
					<span class="glyphicon glyphicon-thumbs-down"><?php echo $comment['downVoteNum']; ?></span>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
<?php endif; ?>

<!-- ページャーの表示 -->
<div class="text-center">
	<?php echo $this->element('pager'); ?>
</div>