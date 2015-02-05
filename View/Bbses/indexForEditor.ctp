<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/bbses/js/bbses.js', false); ?>

<div id="nc-bbs-<?php echo (int)$frameId; ?>">
	<p class="text-right">
		フレームに移動予定です。⇒<span class="nc-tooltip" tooltip="<?php echo __d('net_commons', 'Manage'); ?>">
			<a href="<?php echo $this->Html->url(
				'/bbses/bbses/edit/' . $frameId) ?>" class="btn btn-primary">
				<span class="glyphicon glyphicon-cog"> </span>
			</a>
		</span>
	</p>
	<div><strong><?php echo $dataForView['bbses']['name']; ?></strong></div>
	<div class="text-left">
		<!-- 右に来るやつ -->
		<div class="text-right" style="float:right;">

			<!-- 記事件数の表示 -->
			<div class="glyphicon glyphicon-duplicate"><?php echo $bbsPostNum; ?>&nbsp;</div>

			<!-- ソート用のプルダウン -->
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
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/index' . '/' . $frameId . '/' . 1 . '/' . 3); ?>"><?php echo __d('bbses', 'Descending order of comments'); ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/index' . '/' . $frameId . '/' . 1 . '/' . 4); ?>"><?php echo __d('bbses', 'Status order'); ?></a></li>
					<li><a href="#"><?php echo __d('bbses', 'Do not read'); ?></a></li>

				</ul>
			</div>

			<!-- 表示件数のドロップダウン -->
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
		</div>
		<!-- 記事作成ボタン -->
		<span class="nc-tooltip" tooltip="<?php echo __d('bbses', 'Create post'); ?>">
			<a href="<?php echo $this->Html->url(
					'/bbses/bbsPosts/add' . '/' . $frameId); ?>" class="btn btn-success">
				<span class="glyphicon glyphicon-plus"> </span></a>
		</span>
	</div>

	<br />

	<table class="table table-striped table-hover table-condensed">
		<?php //TODO:$dataForView？ ?>
		<?php foreach ($dataForView['bbsPosts'] as $post) : ?>
			<tr>
				<td class="col-md-offset-1 col-md-8">
					<a href="<?php echo $this->Html->url(
						'/bbses/bbsPosts/view/' . $frameId. '/' . $post['id']); ?>">
					<?php echo $post['title']; ?></a>
					<span class="glyphicon glyphicon-comment"><?php echo $post['commentNum'];?></span>
					<span><?php echo $this->element('NetCommons.status_label',
						array('status' => $post['status'])); ?></span>
				</td>
				<td class="text-right col-md-3">
					<div><?php echo $post['created']; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<!-- ページャーの表示 -->
	<div class="text-center">
		<?php echo $this->element('pager'); ?>
	</div>

</div>
