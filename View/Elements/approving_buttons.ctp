<?php echo $this->Form->create('BbsPost', array(
		'div' => false,
		'type' => 'post',
		'url' => '/bbses/bbsComments/edit/' . $frameId . '/' . $comment['id'],
		'style' => 'float:left;'
	)); ?>

	<?php echo $this->Form->hidden('id'); ?>

	<?php echo $this->Form->hidden('Bbs.id', array(
		'value' => $bbses['id'],
	)); ?>

	<?php echo $this->Form->hidden('User.id', array(
		'value' => $userId,
	)); ?>

	<?php echo $this->Form->hidden('title', array(
		'value' => $comment['title'],
	)); ?>

	<?php echo $this->Form->hidden('content', array(
		'value' => $comment['content'],
	)); ?>

	<?php echo $this->Form->button('<span class="glyphicon glyphicon-ok"></span>', array(
		'label' => false,
		'type' => 'submit',
		'class' => 'btn btn-warning btn-xs',
		'tooltip' => __d('bbses', 'Approving'),
		'name' => 'save_' . NetCommonsBlockComponent::STATUS_PUBLISHED,
	)); ?>

<?php echo $this->Form->end();
