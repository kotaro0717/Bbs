
<div class="text-left">
	<!-- 右に来るやつ -->
	<div class="text-right" style="float:right;">
		<!-- 記事件数の表示 -->
		<?php echo "30"; ?><div class="glyphicon glyphicon-duplicate"></div>
		<!-- ソート順のドロップダウン -->
		<div class="btn-group">
		  <button type="button" class="btn btn-default"><?php echo __d('bbses', 'Latest posts order'); ?></button>
		  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		  </button>
		  <ul class="dropdown-menu" role="menu">
			<li><a href="#"><?php echo __d('bbses', 'Latest post order'); ?></a></li>
			<li><a href="#"><?php echo __d('bbses', 'Older post order'); ?></a></li>
			<li><a href="#"><?php echo __d('bbses', 'Descending order of comments'); ?></a></li>
			<?php if ($contentCreatable = true) : ?>
				<li><a href="#"><?php echo __d('bbses', 'Status order'); ?></a></li>
			<?php endif; ?>
		  </ul>
		</div>
		<!-- 表示件数のドロップダウン -->
		<div class="btn-group">
		  <button type="button" class="btn btn-default"><?php echo __d('bbses', '10') . "件"; ?></button>
		  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		  </button>
		  <ul class="dropdown-menu" role="menu">
			<li><a href="#"><?php echo __d('bbses', '1') . "件"; ?></a></li>
			<li><a href="#"><?php echo __d('bbses', '5') . "件"; ?></a></li>
			<li><a href="#"><?php echo __d('bbses', '10') . "件"; ?></a></li>
			<li><a href="#"><?php echo __d('bbses', '20') . "件"; ?></a></li>
			<li><a href="#"><?php echo __d('bbses', '50') . "件"; ?></a></li>
			<li><a href="#"><?php echo __d('bbses', '100') . "件"; ?></a></li>
		  </ul>
		</div>
	</div>
	<!-- 左に来るやつ -->
	<?php if ($contentCreatable = true) : ?>
	<button class="btn btn-success"
		tooltip="<?php echo __d('bbses', 'Add post'); ?>"
					ng-click="addPostView()">

				<span class="glyphicon glyphicon-plus"></span>
	</button>
	<?php endif; ?>
</div>

<?php //var_dump($bbs_post_list); ?>
