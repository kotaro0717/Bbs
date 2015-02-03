<div><strong><?php echo $bbs['Bbs']['name']; ?></strong></div>
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
			<li><a href="#"><?php echo __d('bbses', 'Latest post order'); ?></a></li>
			<li><a href="#"><?php echo __d('bbses', 'Older post order'); ?></a></li>
			<li><a href="#"><?php echo __d('bbses', 'Descending order of comments'); ?></a></li>
			<?php if ($contentCreatable) : ?>
				<li><a href="#"><?php echo __d('bbses', 'Status order'); ?></a></li>
			<?php endif; ?>
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
	<!-- 左に来るやつ -->
	<?php if ($contentCreatable) : ?>
		<form method="post" action="/bbses/bbses/add/<?php echo $frameId; ?>/">
			<button class="btn btn-success"
				tooltip="<?php echo __d('bbses', 'Create post'); ?>"
				type="submit">

						<span class="glyphicon glyphicon-plus"></span>
			</button>
		</form>
	<?php else : ?>
		<br />
	<?php endif; ?>
	<br />
</div>

<table class="table table-striped table-hover table-condensed">
	<?php foreach ($bbs_posts as $post) : ?>
		<tr>
			<td class="col-md-offset-1 col-md-8">
				<a href="/bbses/bbses/view/<?php echo $frameId; ?>/"><?php echo $post['BbsPost']['title']; ?></a>
				<span class="glyphicon glyphicon-comment"></span><?php echo $post['BbsPost']['up_vote_num'];?>
				<span><?php echo $this->element('NetCommons.status_label',
					array('status' => $post['BbsPost']['status'])); ?></span>
			</td>
			<td class="text-right col-md-3">
				<div><?php echo $post['BbsPost']['created']; ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>

<!-- ページャーの表示 -->
<div class="text-center">
	<nav>
	  <ul class="pagination">
		<li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
		<li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
		<li><a href="#">2</a></li>
		<li><a href="#">3</a></li>
		<li><a href="#">4</a></li>
		<li><a href="#">5</a></li>
		<li>
		  <a href="#" aria-label="Next">
			<span aria-hidden="true">&raquo;</span>
		  </a>
		</li>
	  </ul>
	</nav>
</div>