<!-- パンくずリスト -->
<ol class="breadcrumb">
	<li><a href="<?php echo $this->Html->url(
				'/bbses/bbses/index/' . $frameId) ?>">
		<?php echo $dataForView['bbses']['name']; ?></a>
	</li>
	<li class="active"><?php echo $dataForView['bbsPosts']['title']; ?></li>
</ol>

<!-- 記事タイトル -->
	<h3><?php echo $dataForView['bbsPosts']['title']; ?></h3>

<div class="text-right">
<!-- コメント数 -->
	<span class="glyphicon glyphicon-comment"><?php echo $dataForView['bbsPosts']['commentNum']; ?>&nbsp;</span>
<!-- ソート用プルダウン -->
	<div class="btn-group">
		<button type="button" class="btn btn-default"><?php echo __d('bbses', 'Latest comment order'); ?></button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<li><a href="#"><?php echo __d('bbses', 'Latest comment order'); ?></a></li>
			<li><a href="#"><?php echo __d('bbses', 'Older comment order'); ?></a></li>
			<?php if ($contentCreatable) : ?>
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
	<div><?php echo $dataForView['bbsPosts']['content']; ?></div>
	<!-- いいね！ -->
	<div class="text-left">
		<div class="text-left" style="float:right;">
			<?php if ($contentCreatable && $dataForView['bbses']['commentFlag'] /*&& $dataForView['bbsPosts']['status']*/) : ?>
				<a href="#" tooltip="<?php echo __d('bbses', 'Write comment'); ?>"><span class="glyphicon glyphicon-comment"></span></a>
			<?php endif; ?>
			<a href="#" tooltip="<?php echo __d('bbses', 'Edit'); ?>"><span class="glyphicon glyphicon-edit"></span></a>
			<a href="#" tooltip="<?php echo __d('bbses', 'Delete'); ?>"><span class="glyphicon glyphicon-trash"></span></a>
		</div>
		<span class="glyphicon glyphicon-thumbs-up"><?php echo $dataForView['bbsPosts']['upVoteNum']; ?></span>
		<span class="glyphicon glyphicon-thumbs-down"><?php echo $dataForView['bbsPosts']['downVoteNum']; ?></span>
	</div>
<hr />


<!-- ページャーの表示 -->
<div class="text-center">
	<?php echo $this->element('pager'); ?>
</div>