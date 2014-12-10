<div class="bbs index">
	<h2><?php echo __('Bbs'); ?></h2>
		<?php var_dump($bbs_post_list); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Bbs'), array('action' => 'add')); ?></li>
	</ul>
</div>
