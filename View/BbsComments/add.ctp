<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/bbses/js/bbses.js', false); ?>

<div id="nc-bbs-add-<?php echo (int)$frameId; ?>"
		ng-controller="BbsComment"
		ng-init="initialize(<?php echo h(json_encode($bbsPosts)); ?>,
							<?php echo h(json_encode($bbsComments)); ?>,
							<?php echo h(json_encode($quotFlag)); ?>)">

<!-- パンくずリスト -->
<ol class="breadcrumb">
	<li><a href="<?php echo $this->Html->url(
				'/bbses/bbses/index/' . $frameId) ?>">
		<?php echo $bbses['name']; ?></a>
	</li>
	<li><a href="<?php echo $this->Html->url(
				'/bbses/bbsPosts/view/' . $frameId . '/' . $bbsPosts['id']) ?>">
		<?php echo $bbsPosts['title']; ?></a>
	</li>
	<li class="active"><?php echo __d('bbses', 'Create comment'); ?></li>
</ol>

<div>
<?php echo $this->Form->create('BbsPost', array(
		'name' => 'form',
		'novalidate' => true,
	)); ?>
	<?php echo $this->Form->hidden('id'); ?>
	<?php echo $this->Form->hidden('Bbs.key', array(
		'value' => $bbses['key'],
	)); ?>
	<?php echo $this->Form->hidden('BbsPost.parent_id', array(
		'value' => $bbsPosts['id'],
	)); ?>

	<div class="panel panel-default">
		<div class="panel-body has-feedback">
			<div>
				<?php echo $this->element('BbsComments/reference_posts'); ?>
			</div>
			<?php echo $this->element('BbsComments/add_comment_form'); ?>
		</div>
		<div class="panel-footer text-center">
			<?php echo $this->element('Bbses.comment_workflow_buttons'); ?>
		</div>
	</div>
<?php echo $this->Form->end(); ?>
</div>

</div>