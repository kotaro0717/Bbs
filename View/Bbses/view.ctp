<!-- jsファイル読み込み -->
<?php //echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php //echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php //echo $this->Html->script('/bbses/js/bbses.js', false); ?>

<!-- angularのコントローラ宣言、jsにデータ渡し -->
<div id="nc-bbs-index-for-editor-<?php echo (int)$frameId; ?>">

	<!-- 管理ボタン:コンテンツが公開できる人ならば表示 -->
	<?php if ($contentPublishable) : ?>
		<div class="text-right">
			<a href="<?php echo $this->Html->url(
				'/bbses/bbses/edit/' . $frameId) ?>" class="btn btn-primary">
				<span class="glyphicon glyphicon-cog"> </span>
			</a>
		</div>
	<?php endif; ?>

	<!-- 掲示板名称:フレームに表示できるならばいらない -->
	<div class="text-left">
		<strong><?php echo $bbses['name']; ?></strong>
	</div>

	<div class="text-left">
		<!-- 記事作成ボタン:コンテンツが作成できる人ならば表示 -->
		<?php if ($contentCreatable) : ?>
			<span class="nc-tooltip" tooltip="<?php echo __d('bbses', 'Create post'); ?>">
				<a href="<?php echo $this->Html->url(
						'/bbses/bbsPosts/add' . '/' . $frameId); ?>" class="btn btn-success">
					<span class="glyphicon glyphicon-plus"> </span></a>
			</span>
		<?php else : ?>
			&nbsp;
		<?php endif; ?>

		<!-- 右に表示 -->
		<span class="text-left" style="float:right;">
			<!-- 記事件数の表示 -->
			<div class="glyphicon glyphicon-duplicate"><?php echo $bbsPostNum . __d('bbses', 'Posts'); ?>&nbsp;</div>

			<!-- ソート -->
			<div class="btn-group">
				<button type="button" class="btn btn-default"><?php echo $currentPostSortOrder; ?></button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . 1 . '/' . $currentVisibleRow) ?>"><?php echo __d('bbses', 'Latest post order'); ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . 2 . '/' . $currentVisibleRow); ?>"><?php echo __d('bbses', 'Older post order'); ?></a></li>
					<!-- 未実装:未読 -->
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . 3 . '/' . $currentVisibleRow); ?>"><?php echo __d('bbses', 'Do not read'); ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . 4 . '/' . $currentVisibleRow); ?>"><?php echo __d('bbses', 'Descending order of comments'); ?></a></li>
					<?php if ($contentCreatable) : ?>
						<li><a href="<?php echo $this->Html->url(
								'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . 5 . '/' . $currentVisibleRow); ?>"><?php echo __d('bbses', 'Status order'); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>

			<!-- 表示件数 -->
			<div class="btn-group">
				<?php
					/*echo $this->Form->input('number', array(
								'label' => false,
								'type' => 'select',
								'options' => BbsFrameSetting::getDisplayNumberOptions(),
								'class' => 'form-control',
								'ng-controller' => 'Bbses',
								'ng-model' => 'bbses.currentVisibleRow',
								//'ng-change' => 'changeBbsPostList()',
							)
						);*/
				?>

				<button type="button" class="btn btn-default"><?php echo $currentVisibleRow . "件"; ?></button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 1); ?>"><?php echo '1' . "件"; ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 5); ?>"><?php echo '5' . "件"; ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 10); ?>"><?php echo '10' . "件"; ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 20); ?>"><?php echo '20' . "件"; ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 50); ?>"><?php echo '50' . "件"; ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 100); ?>"><?php echo '100' . "件"; ?></a></li>
				</ul>
			</div>
		</span>
	</div>

	<br />

	<table class="table table-striped">
		<?php foreach ($bbsPosts as $bbsPost) : ?>
			<tr>
				<td>
					<!-- 詳細ページへ -->
					<a href="/bbses/bbsPosts/view/<?php echo $frameId; ?>/<?php echo $bbsPost['BbsPost']['id']; ?>"
					   class="text-left">

						<!-- 記事のタイトル TODO:最大表示数をCONST化する -->
						<?php echo mb_substr(strip_tags($bbsPost['BbsPost']['title']), 0, 30, 'UTF-8'); ?>
						<?php echo (mb_substr(strip_tags($bbsPost['BbsPost']['title']), 31, null, 'UTF-8') === '')? '' : '…'; ?>

						<!-- コメント数 -->
						<span class="glyphicon glyphicon-comment"><?php echo $bbsPost['BbsPost']['comment_num']; ?></span>

					</a>

					<!-- ステータス -->
					<?php echo $this->element('NetCommons.status_label',
								array('status' => $bbsPost['BbsPost']['status'])); ?>

					<!-- 作成日時 -->
					<div class="text-left" style="float:right;"><?php echo $bbsPost['BbsPost']['created']; ?></div>

					<!-- 本文 TODO:最大表示数をCONST化する -->
					<p>
						<?php echo mb_substr(strip_tags($bbsPost['BbsPost']['content']), 0, 75, 'UTF-8'); ?>
						<?php echo (mb_substr(strip_tags($bbsPost['BbsPost']['title']), 76, null, 'UTF-8') === '')? '' : '…'; ?>
					</p>

					<!-- フッター -->
					<span class="text-left">
						<?php if ($bbsPost['BbsPost']['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>
							<span class="glyphicon glyphicon-thumbs-up"><?php echo $bbsPost['BbsPost']['up_vote_num']; ?></span>
							<span class="glyphicon glyphicon-thumbs-down"><?php echo $bbsPost['BbsPost']['down_vote_num']; ?></span>
						<?php endif ?>&nbsp;
					</span>

					<!-- 編集/削除 -->
					<span class="text-right" style="float:right;">

						<!-- 公開権限があれば編集／削除できる -->
						<!-- もしくは　編集権限があり、公開されていなければ、編集／削除できる -->
						<!-- もしくは 作成権限があり、自分の書いた記事で、公開されていなければ、編集／削除できる -->
						<!-- TODO:型まで見るように -->
						<?php if ($contentPublishable ||
								($contentEditable && $bbsPost['BbsPost']['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) ||
								($contentCreatable && $bbsPost['BbsPost']['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) && $bbsPost['BbsPost']['createdUser'] === $userId): ?>

							<a href="/bbses/bbsPosts/edit/<?php echo $frameId; ?>/<?php echo $bbsPost['BbsPost']['id']; ?>"
									class="btn btn-primary btn-xs" tooltip="<?php echo __d('bbses', 'Edit'); ?>">
									<span class="glyphicon glyphicon-edit"></span></a>

							<button
									class="btn btn-danger btn-xs" tooltip="<?php echo __d('bbses', 'Delete'); ?>">
									<span class="glyphicon glyphicon-trash"></span>
							</button>

						<?php endif; ?>
					</span>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<!-- ページャーの表示 -->
	<div class="text-center">
	<?php echo $this->element('Bbses/pager'); ?>
	</div>

</div>
