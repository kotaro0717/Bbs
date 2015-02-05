<div><strong><?php echo $dataForView['bbses']['name']; ?></strong></div>

<!-- 記事作成ボタン -->
<?php if ($contentCreatable) : ?>
	<div class="text-left">
		<span class="nc-tooltip" tooltip="<?php echo __d('bbses', 'Create post'); ?>">
			<a href="<?php echo $this->Html->url(
					'/bbses/bbsPosts/add' . '/' . $frameId); ?>" class="btn btn-success">
				<span class="glyphicon glyphicon-plus"> </span></a>
		</span>
	</div>
<?php endif; ?>

<hr />
<!-- メッセージの表示 -->
<div class="text-left">
	<?php echo __d('bbses', 'There are not posts'); ?>
</div>