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
				'/bbses/bbses/edit/' . $frameId) ?>" class="btn btn-primary">
				<span class="glyphicon glyphicon-cog"> </span>
			</a>
		</div>
	<?php endif; ?>

	<!-- 掲示板名称:フレームに表示できるならばいらない -->
	<div class="text-left">
		<strong><?php echo $dataForView['bbses']['name']; ?></strong>
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
				<?php
					/*echo $this->Form->input('number', array(
								'label' => false,
								'type' => 'select',
								'options' => BbsFrameSetting::getDisplayNumberOptions(),
								'class' => 'form-control',
								'ng-controller' => 'Bbses',
								'ng-model' => 'bbses.currentVisiblePostRow',
								//'ng-change' => 'changeBbsPostList()',
							)
						);*/
				?>

				<button type="button" class="btn btn-default">{{bbses.currentVisiblePostRow}}<?php echo "件"; ?></button>
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
	</div>

	<br />

	<table class="table table-striped">
		<tr ng-repeat="post in bbses.bbsPosts" ng-cloak>
			<td>
				<!-- 詳細ページへ -->
				<a href="/bbses/bbsPosts/view/<?php echo $frameId;?>/{{post.id}}"
				   class="text-left">

					<!-- 記事のタイトル -->
					{{post.title | Bbses.filter:30 :false}}

					<!-- コメント数 -->
					<span class="glyphicon glyphicon-comment">{{post.comment_num}}</span>

				</a>

				<!-- ステータス -->
				<span class="ng-hide" ng-show="{{post.status}}"
						ng-switch="{{post.status}}" ng-cloak>
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
				<div class="text-left" style="float:right;">{{post.created}}</div>

				<!-- 本文 -->
				<p ng-bind-html="post.content | Bbses.filter:75 :true"></p>

				<!-- フッター -->
				<span class="text-left">
					<span ng-if="post.status == <?php echo NetCommonsBlockComponent::STATUS_PUBLISHED; ?>">
						<span class="glyphicon glyphicon-thumbs-up">{{post.up_vote_num}}</span>
						<span class="glyphicon glyphicon-thumbs-down">{{post.down_vote_num}}</span>
					</span>&nbsp;
				</span>

				<!-- 編集/削除 -->
				<span class="text-right" style="float:right;">

					<!-- 公開権限があれば編集／削除できる -->
					<!-- もしくは　編集権限があり、公開されていなければ、編集／削除できる -->
					<!-- もしくは 作成権限があり、自分の書いた記事で、公開されていなければ、編集／削除できる -->
					<!-- TODO:型まで見るように -->
					<span ng-if="bbses.contentPublishable ||
							(bbses.contentEditable && post.status != <?php echo NetCommonsBlockComponent::STATUS_PUBLISHED; ?>) ||
							(bbses.contentCreatable && post.status != <?php echo NetCommonsBlockComponent::STATUS_PUBLISHED; ?> && post.createdUser == bbses.userId)">

						<a href="/bbses/bbsPosts/edit/<?php echo $frameId; ?>/{{post.id}}"
								class="btn btn-primary btn-xs" tooltip="<?php echo __d('bbses', 'Edit'); ?>">
								<span class="glyphicon glyphicon-edit"></span></a>
					</span>

					<span ng-if="bbses.contentPublishable ||
							(bbses.contentEditable && post.status != <?php echo NetCommonsBlockComponent::STATUS_PUBLISHED; ?>) ||
							(bbses.contentCreatable && post.status != <?php echo NetCommonsBlockComponent::STATUS_PUBLISHED; ?> && post.createdUser == bbses.userId)">

						<button
								class="btn btn-danger btn-xs" tooltip="<?php echo __d('bbses', 'Delete'); ?>">
								<span class="glyphicon glyphicon-trash"></span>
						</button>
					</span>
				</span>
			</td>
		</tr>
	</table>

	<!-- ページャーの表示 -->
	<div class="text-center">
	<?php echo $this->element('pager'); ?>
	</div>

</div>
