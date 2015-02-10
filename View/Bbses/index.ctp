<!-- jsファイル読み込み -->
<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/bbses/js/bbses.js', false); ?>

<!-- angularのコントローラ宣言、jsにデータ渡し -->
<div id="nc-bbs-index-for-editor-<?php echo (int)$frameId; ?>"
		ng-controller="Bbses"
		ng-init="initialize(<?php echo h(json_encode($this->viewVars)); ?>)">

	<!-- 管理ボタン:コンテンツが公開できる人ならば表示 -->
	<?php if ($contentPublishable) : ?>
			<div class="text-right">
				<a href="<?php echo $this->Html->url(
					'/bbses/bbses/view/' . $frameId) ?>" class="btn btn-primary">
					<span class="glyphicon glyphicon-cog"> </span>
				</a>
			</div>
	<?php endif; ?>

	<!-- 掲示板名称:フレームに表示できるならばいらない -->
	<div class="text-left">
		<strong><?php echo $dataForView['bbses']['name']; ?></strong>
	</div>

	<span class="text-left">

	<span class="text-right" style="float:right;">
		<!-- 記事件数の表示 -->
		<div class="glyphicon glyphicon-duplicate"><?php echo $bbsPostNum; ?>&nbsp;</div>

		<!-- ソート -->
		<div class="btn-group">
			<button type="button" class="btn btn-default"><?php echo $dataForView['currentPostSortOrder']; ?></button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="<?php echo $this->Html->url(
						'/bbses/bbses/index' . '/' . $frameId . '/' . 1 . '/' . 1); ?>"><?php echo __d('bbses', 'Latest post order'); ?></a></li>
				<li><a href="<?php echo $this->Html->url(
						'/bbses/bbses/index' . '/' . $frameId . '/' . 1 . '/' . 2); ?>"><?php echo __d('bbses', 'Older post order'); ?></a></li>
				<!-- 未実装:未読 -->
				<li><a href="#"><?php echo __d('bbses', 'Do not read') . "(未実装)" ; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
						'/bbses/bbses/index' . '/' . $frameId . '/' . 1 . '/' . 3); ?>"><?php echo __d('bbses', 'Descending order of comments'); ?></a></li>
				<?php if ($contentCreatable) : ?>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/index' . '/' . $frameId . '/' . 1 . '/' . 4); ?>"><?php echo __d('bbses', 'Status order'); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>

		<!-- 表示件数 -->
		<div class="btn-group">
			<button type="button" class="btn btn-default"><?php echo $dataForView['currentVisiblePostRow'] . "件"; ?></button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="<?php echo $this->Html->url(
						'/bbses/bbses/index' . '/' . $frameId . '/' . 2 . '/' . 1); ?>"><?php echo '1' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
						'/bbses/bbses/index' . '/' . $frameId . '/' . 2 . '/' . 5); ?>"><?php echo '5' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
						'/bbses/bbses/index' . '/' . $frameId . '/' . 2 . '/' . 10); ?>"><?php echo '10' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
						'/bbses/bbses/index' . '/' . $frameId . '/' . 2 . '/' . 20); ?>"><?php echo '20' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
						'/bbses/bbses/index' . '/' . $frameId . '/' . 2 . '/' . 50); ?>"><?php echo '50' . "件"; ?></a></li>
				<li><a href="<?php echo $this->Html->url(
						'/bbses/bbses/index' . '/' . $frameId . '/' . 2 . '/' . 100); ?>"><?php echo '100' . "件"; ?></a></li>
			</ul>
		</div>
	</span>

		<!-- 記事作成ボタン:コンテンツが作成できる人ならば表示 -->
		<?php if ($contentCreatable) : ?>
		<span class="nc-tooltip" tooltip="<?php echo __d('bbses', 'Create post'); ?>">
			<a href="<?php echo $this->Html->url(
					'/bbses/bbsPosts/add' . '/' . $frameId); ?>" class="btn btn-success">
				<span class="glyphicon glyphicon-plus"> </span></a>
		</span>
		<?php endif; ?>
	</span><br /><br />

<!--	<div ng-repeat="post in bbses.bbsPosts" ng-cloak>
	<div class="panel-group" id="nc-bbs-accordion-{{post.id}}">
		<div class="panel panel-default">
			<div class="panel-heading" id="nc-bbs-head-{{post.id}}">
				<div class="panel-title">

					 記事のタイトル(詳細ページへのリンク)
					 特定文字数超える場合は『…』と省略する:未実装
					<a href="/bbses/bbsPosts/view/<?php //echo $frameId;?>/{{post.id}}"
					   class="text-left">

						{{post.title}}
					</a>

					 コメント数
					<span class="glyphicon glyphicon-comment">{{post.commentNum}}</span>

					 ステータス
					<span class="ng-hide" ng-show="{{post.status}}"
							ng-switch="{{post.status}}" ng-cloak>
						<span class="label label-warning"
								  ng-switch-when="<?php //echo NetCommonsBlockComponent::STATUS_APPROVED ?>">
							  <?php// echo __d('net_commons', 'Approving'); ?>
						</span>
						<span class="label label-danger"
								  ng-switch-when="<?php //echo NetCommonsBlockComponent::STATUS_DISAPPROVED ?>">
							  <?php// echo __d('net_commons', 'Disapproving'); ?>
						</span>
						<span class="label label-info"
								  ng-switch-when="<?php //echo NetCommonsBlockComponent::STATUS_IN_DRAFT ?>">
							  <?php //echo __d('net_commons', 'Temporary'); ?>
						</span>
						<span ng-switch-default=""></span>
					</span>

					 作成日時
					<a list-group-item
						href="#nc-bbs-post-{{post.id}}"
						data-toggle="collapse"
						data-parent="#nc-bbs-accordion-{{post.id}}"
						class="display-block text-right" style="float:right;">

						{{post.created}}
					</a>
				</div>
			</div>

			 アコーディオンでオープン
			<div id="nc-bbs-post-{{post.id}}"
				 class="panel-collapse collapse"
				 aria-labelledby="nc-bbs-head-{{post.id}}">

				 本文
				<div class="panel-body" >
					<div ng-bind-html="post.content"></div>
				</div>
				 フッター
				<div class="panel-footer">
					<span class="text-left">
						<span>
							<span class="glyphicon glyphicon-thumbs-up">{{post.upVoteNum}}</span>
							<span class="glyphicon glyphicon-thumbs-down">{{post.downVoteNum}}</span>
							<span ng-if="post.status != <?php //echo NetCommonsBlockComponent::STATUS_PUBLISHED; ?>">
								←未公開ならば隠れます。
							</span>
						</span>
					</span>

					 コメント作成/編集/削除
					<span class="text-right" style="float:right;">

						 作成権限があり、記事が公開されていれば、コメントできる
						 TODO:型まで見るように
						<span ng-if="<?php //echo $contentCreatable; ?> &&
							post.status == <?php //echo NetCommonsBlockComponent::STATUS_PUBLISHED; ?>" ng-cloak>

							<a href="/bbses/bbsPosts/add/<?php //echo $frameId; ?>/{{post.id}}/2"
									class="btn btn-success btn-xs" tooltip="<?php// echo __d('bbses', 'Write comment'); ?>">
									<span class="glyphicon glyphicon-comment"></span></a>
						</span>

						 公開権限があれば編集／削除できる
						 もしくは　編集権限があり、公開されていなければ、編集／削除できる
						 もしくは 作成権限があり、自分の書いた記事で、公開されていなければ、編集／削除できる
						 TODO:型まで見るように
						<span ng-cloak ng-if="<?php //echo $contentPublishable; ?> ||
								(<?php// echo $contentEditable; ?> && post.status != <?php //echo NetCommonsBlockComponent::STATUS_PUBLISHED; ?>) ||
								(<?php //echo $contentCreatable; ?> && post.status != <?php //echo NetCommonsBlockComponent::STATUS_PUBLISHED; ?> && post.createdUser == bbses.userId)">

							<a href="/bbses/bbsPosts/edit/<?php //echo $frameId; ?>/{{post.id}}"
									class="btn btn-primary btn-xs" tooltip="<?php //echo __d('bbses', 'Edit'); ?>">
									<span class="glyphicon glyphicon-edit"></span></a>
						</span>

						<span ng-cloak ng-if="<?php //echo $contentPublishable; ?> ||
								(<?php// echo $contentEditable; ?> && post.status != <?php //echo NetCommonsBlockComponent::STATUS_PUBLISHED; ?>) ||
								(<?php //echo $contentCreatable; ?> && post.status != <?php //echo NetCommonsBlockComponent::STATUS_PUBLISHED; ?> && post.createdUser == bbses.userId)">

							<button
									class="btn btn-danger btn-xs" tooltip="<?php //echo __d('bbses', 'Delete'); ?>">
									<span class="glyphicon glyphicon-trash"></span>
							</button>
						</span>

					</span>
				</div>
			</div>
		</div>
	</div>
	</div>-->

	<?php foreach ($dataForView['bbsPosts'] as $post) : ?>
	<div class="panel-group" id="nc-bbs-accordion-<?php echo $post['id']; ?>">
		<div class="panel panel-default">
			<div class="panel-heading" id="nc-bbs-head-<?php echo $post['id']; ?>">
				<div class="panel-title">

					<!-- 記事のタイトル(詳細ページへのリンク) -->
					<!-- 特定文字数超える場合は『…』と省略する:未実装 -->
					<a href="/bbses/bbsPosts/view/<?php echo $frameId;?>/<?php echo $post['id']; ?>"
					   class="text-left">

						<?php echo $post['title']; ?>
					</a>

					<!-- コメント数 -->
					<span class="glyphicon glyphicon-comment"><?php echo $post['commentNum']; ?></span>

					<!-- ステータス -->
					<span class="ng-hide" ng-show="<?php echo $post['status']; ?>"
							ng-switch="<?php echo $post['status']; ?>" ng-cloak>
						<span class="label label-warning"
								  ng-switch-when="<?php echo NetCommonsBlockComponent::STATUS_APPROVED ?>">
							  <?php echo __d('net_commons', 'Approving'); ?>
						</span>
						<span class="label label-danger"
								  ng-switch-when="<?php echo NetCommonsBlockComponent::STATUS_DISAPPROVED ?>">
							  <?php echo __d('net_commons', 'Disapproving'); ?>
						</span>
						<span class="label label-info"
								  ng-switch-when="<?php echo NetCommonsBlockComponent::STATUS_IN_DRAFT ?>">
							  <?php echo __d('net_commons', 'Temporary'); ?>
						</span>
						<span ng-switch-default=""></span>
					</span>

					<!-- 作成日時 -->
					<a list-group-item
						href="#nc-bbs-post-<?php echo $post['id']; ?>"
						data-toggle="collapse"
						data-parent="#nc-bbs-accordion-<?php echo $post['id']; ?>"
						class="display-block text-right" style="float:right;">

						<?php echo $post['created']; ?>
					</a>
				</div>
			</div>

			<!-- アコーディオンでオープン -->
			<div id="nc-bbs-post-<?php echo $post['id']; ?>"
				 class="panel-collapse collapse"
				 aria-labelledby="nc-bbs-head-<?php echo $post['id']; ?>">

				<!-- 本文 -->
				<div class="panel-body" >
					<?php echo $post['content']; ?>
				</div>
				<!-- フッター -->
				<div class="panel-footer">
					<span class="text-left">
						<span>
							<span class="glyphicon glyphicon-thumbs-up"><?php echo $post['upVoteNum']; ?></span>
							<span class="glyphicon glyphicon-thumbs-down"><?php echo $post['downVoteNum']; ?></span>
							<?php if ($post['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>
								←未公開ならば隠れます。
							<?php endif; ?>
						</span>
					</span>

					<!-- コメント作成/編集/削除 -->
					<span class="text-right" style="float:right;">

						<!-- 作成権限があり、記事が公開されていれば、コメントできる -->
						<!-- TODO:型まで見るように -->
						<?php if ($contentCreatable &&
							$post['status'] == NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

							<a href="/bbses/bbsPosts/add/<?php echo $frameId; ?>/<?php echo $post['id']; ?>/2"
									class="btn btn-success btn-xs" tooltip="<?php echo __d('bbses', 'Write comment'); ?>">
									<span class="glyphicon glyphicon-comment"></span></a>
						<?php endif; ?>

						<!-- 公開権限があれば編集／削除できる -->
						<!-- もしくは　編集権限があり、公開されていなければ、編集／削除できる -->
						<!-- もしくは 作成権限があり、自分の書いた記事で、公開されていなければ、編集／削除できる -->
						<!-- TODO:型まで見るように -->
						<?php if ($contentPublishable ||
								($contentEditable && $post['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) ||
								($contentCreatable && $post['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED && $post['createdUser'] == $dataForView['bbses']['userId'])) : ?>

							<a href="/bbses/bbsPosts/edit/<?php echo $frameId; ?>/<?php echo $post['id']; ?>"
									class="btn btn-primary btn-xs" tooltip="<?php echo __d('bbses', 'Edit'); ?>">
									<span class="glyphicon glyphicon-edit"></span></a>
						<?php endif; ?>

						<?php if ($contentPublishable ||
								($contentEditable && $post['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) ||
								($contentCreatable && $post['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED && $post['createdUser'] == $dataForView['bbses']['userId'])) : ?>

							<button
									class="btn btn-danger btn-xs" tooltip="<?php echo __d('bbses', 'Delete'); ?>">
									<span class="glyphicon glyphicon-trash"></span>
							</button>
						<?php endif; ?>
					</span>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>
