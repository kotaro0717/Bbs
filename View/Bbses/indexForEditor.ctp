<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/bbses/js/bbses.js', false); ?>

<div id="nc-bbs-<?php echo (int)$frameId; ?>">

	<div><strong><?php echo $dataForView['bbses']['name']; ?></strong></div>
	<div class="text-left">
		<!-- 右に来るやつ -->
		<div class="text-right" style="float:right;">

			<!-- 記事件数の表示 -->
			<div class="glyphicon glyphicon-duplicate"><?php echo "30"; ?></div>

			<!-- ソート用のプルダウン -->
			<div class="btn-group">
				<button type="button" class="btn btn-default"><?php echo __d('bbses', 'Latest post order'); ?></button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="/bbses/bbses/index/<?php echo $frameId; ?>"><?php echo __d('bbses', 'Latest post order'); ?></a></li>
					<li><a href="/bbses/bbses/indexOld/<?php echo $frameId; ?>"><?php echo __d('bbses', 'Older post order'); ?></a></li>
					<li><a href="/bbses/bbses/indexComments/<?php echo $frameId; ?>"><?php echo __d('bbses', 'Descending order of comments'); ?></a></li>
					<li><a href="/bbses/bbses/indexStatus/<?php echo $frameId; ?>"><?php echo __d('bbses', 'Status order'); ?></a></li>
				</ul>
			</div>

			<!-- 表示件数のドロップダウン -->
			<div class="btn-group">
				<button type="button" class="btn btn-default"><?php echo  '10' . "件"; ?></button>
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
		<!-- 記事作成ボタン -->
		<span class="nc-tooltip" tooltip="<?php echo __d('bbses', 'Create post'); ?>">
			<a href="<?php echo $this->Html->url(
				'/bbses/bbses/add/' . $frameId) ?>" class="btn btn-success">
				<span class="glyphicon glyphicon-plus"> </span>
			</a>
		</span>
	</div>

	<br />

	<table class="table table-striped table-hover table-condensed">
		<?php //TODO:$dataForView？ ?>
		<?php foreach ($dataForView['bbsPosts'] as $post) : ?>
			<tr>
				<td class="col-md-offset-1 col-md-8">
					<span><?php echo $this->Html->link($post['title'], array(
						'controller' => 'bbses',
						'action' => 'view/' . $frameId,
						$post['id'])); ?>
					</span>
					<span class="glyphicon glyphicon-comment"></span><?php echo $post['upVoteNum'];?>
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
