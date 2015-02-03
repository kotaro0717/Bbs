<!-- パンくずリスト -->
<ol class="breadcrumb">
  <li><a href="/bbses/bbses/index/<?php echo $frameId; ?>/"><?php echo __d('bbses', 'Bbs name'); ?></a></li>
  <li><a href="/bbses/bbses/view/<?php echo $frameId; ?>/"><?php echo __d('bbses', 'Post title'); ?></a></li>
  <li class="active">#<?php echo $comment_number="11"; ?></li>
</ol>

<!-- 記事タイトル -->
	<h3><a href="/bbses/bbses/view/<?php echo $frameId; ?>/"><?php echo $title="サンプル記事1"; ?></a>に戻る</h3>

<div class="text-right">
<!-- コメント数 -->
	<span class="glyphicon glyphicon-comment"><?php echo $comments_num="30"; ?></span>
<!-- ソート用プルダウン -->
	<div class="btn-group">
		<button type="button" class="btn btn-default"><?php echo __d('bbses', 'Latest post order'); ?></button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<li><a href="#"><?php echo __d('bbses', 'Latest post order'); ?></a></li>
			<li><a href="#"><?php echo __d('bbses', 'Older post order'); ?></a></li>
			<?php if ($contentCreatable = true) : ?>
				<li><a href="#"><?php echo __d('bbses', 'Status order'); ?></a></li>
			<?php endif; ?>
		</ul>
	</div>
<!-- 表示件数 -->
	<div class="btn-group">
		<button type="button" class="btn btn-default"><?php echo '10' . "件"; ?></button>
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
<hr />

<!-- 親記事 -->
	<!-- id -->
	1.<span><?php echo $this->Html->image('/bbses/img/avatar.PNG', array('alt'=>'アバターが設定されていません')); ?></span>
	<!-- ユーザ情報 -->
	<span><?php echo $username="userA"; ?></span>
	<!-- 作成時間 -->
	<span><?php echo "12:12"; ?></span>
	<!-- 本文 -->
	<div><?php echo $content="サンプル記事1の内容。サンプル記事1の内容。サンプル記事1の内容。サンプル記事1の内容。サンプル記事1の内容。サンプル記事1の内容。サンプル記事1の内容。サンプル記事1の内容。サンプル記事1の内容。サンプル記事1の内容。サンプル記事1の内容。サンプル記事1の内容。サンプル記事1の内容。サンプル記事1の内容。"; ?></div>
	<!-- いいね！ -->
	<div class="text-left">
		<div class="text-left" style="float:right;">
			<a href="#" tooltip="<?php echo __d('bbses', 'Write comment'); ?>"><span class="glyphicon glyphicon-comment"></span></a>
			<a href="#" tooltip="<?php echo __d('bbses', 'Edit'); ?>"><span class="glyphicon glyphicon-edit"></span></a>
			<a href="#" tooltip="<?php echo __d('bbses', 'Delete'); ?>"><span class="glyphicon glyphicon-trash"></span></a>
		</div>
		<span class="glyphicon glyphicon-thumbs-up"><?php echo $like_num="12"; ?></span>
		<span class="glyphicon glyphicon-thumbs-down"><?php echo $unlike_num="2"; ?></span>
	</div>
<hr />

<?php $comments = array(
		11 => "コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。",
		10 => "コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。",
		9 => "コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。",
		8 => "コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。",
		7 => "コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。",
		6 => "コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。",
		5 => "コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。",
		4 => "コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。",
		3 => "コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。",
		2 => "コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。コメントの本文です。"
	); ?>


<!-- 全体の段落下げ -->
<?php foreach ($comments as $id => $comment) { ?>
<div class="col-md-offset-1 col-md-offset-1 col-xs-offset-1">
<div class="col-sm-13 col-sm-13 col-xs-13">
	<!-- id -->
	<?php echo $id; ?>.<span><?php echo $this->Html->image('/bbses/img/avatar.PNG', array('alt'=>'アバターが設定されていません')); ?></span>
	<!-- ユーザ情報 -->
	<span><?php echo $username="userB"; ?></span>
	<!-- タイトル -->
	<a href="#"><?php echo "コメント" . $id; ?></a>
	<!-- 時間 -->
	<span><?php echo "12:12"; ?></span>
	<!-- 本文 -->
	<div><?php echo $comment; ?></div>
	<!-- いいね！ -->
	<div class="text-left">
		<div class="text-left" style="float:right;">
			<a href="#" tooltip="<?php echo __d('bbses', 'Write comment'); ?>"><span class="glyphicon glyphicon-comment"></span></a>
			<a href="#" tooltip="<?php echo __d('bbses', 'Edit'); ?>"><span class="glyphicon glyphicon-edit"></span></a>
			<a href="#" tooltip="<?php echo __d('bbses', 'Delete'); ?>"><span class="glyphicon glyphicon-trash"></span></a>
		</div>
		<span class="glyphicon glyphicon-thumbs-up"><?php echo $like_num="12"; ?></span>
		<span class="glyphicon glyphicon-thumbs-down"><?php echo $unlike_num="2"; ?></span>
	</div>
	<hr />
</div>
</div>
<?php } ?>
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