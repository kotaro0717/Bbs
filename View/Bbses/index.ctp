<h4><?php echo $dataForView['Bbs']['name']; ?></h4>
<div class="text-left">
	<!-- 右に来るやつ -->
	<div class="text-right" style="float:right;">

		<!-- 記事件数の表示 -->
		<div class="glyphicon glyphicon-duplicate"><?php echo "30"; ?></div>

		<!-- ソート用のプルダウン -->
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
	<form method="post" action="/bbses/bbses/add/<?php echo $frameId; ?>/">
		<button class="btn btn-success"
			tooltip="<?php echo __d('bbses', 'Add post'); ?>"
			type="submit">

					<span class="glyphicon glyphicon-plus"></span>
		</button>
	</form>
	<?php endif; ?>
</div><br />

<?php $posts = array(
		1 => "12:12",
		2 => "09:12",
		3 => "01/28",
		4 => "01/25",
		5 => "01/24",
		6 => "2014/12/28",
		7 => "2014/12/26",
		8 => "2014/12/22",
		9 => "2014/11/12",
		10 => "2014/08/11"
	); ?>

<?php $comments_num = array(21,52,0,67,4,75,37,8,55,123,43); ?>


<table class="table table-striped table-hover table-condensed">
	<?php foreach ($posts as $post => $datetime) { ?>
		<tr>
			<td class="col-md-offset-1 col-md-9">
				<a href="/bbses/bbses/view/<?php echo $frameId; ?>/">サンプル記事<?php echo $post; ?>、サンプル記事<?php echo $post; ?>、サンプル記事<?php echo $post; ?></a>
				<span class="glyphicon glyphicon-comment"></span><?php echo $comments_num[$post];?>
			</td>
			<td class="text-right col-md-2">
				<div><?php echo $datetime; ?>
			</td>
		</tr>
	<?php } ?>
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