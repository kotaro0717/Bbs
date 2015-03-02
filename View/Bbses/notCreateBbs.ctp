<!-- Todo:フレーム管理に移動する -->
<?php if ($contentPublishable) : ?>
	<div class="text-right">
		<a href="<?php echo $this->Html->url(
			'/bbses/bbses/edit/' . $frameId) ?>" class="btn btn-primary">
			<span class="glyphicon glyphicon-cog"> </span>
		</a>
	</div>
<?php endif; ?>

<div class="text-left">
	<?php echo __d('bbses', 'There are not published bbs'); ?>
</div>