<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/bbses/js/bbses.js', false); ?>

<div id="nc-bbs-add-<?php echo (int)$frameId; ?>"
		ng-controller="BbsPost"
		ng-init="initialize(<?php echo h(json_encode($bbsPosts)); ?>)">

<!-- パンくずリスト -->
<ol class="breadcrumb">
	<li><a href="<?php echo $this->Html->url(
				'/bbses/bbses/index/' . $frameId) ?>">
		<?php echo $bbses['name']; ?></a>
	</li>
	<li class="active"><?php echo __d('bbses', 'Create post'); ?></li>
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
	<?php echo $this->Form->hidden('User.id', array(
		'value' => $userId,
	)); ?>

	<div class="panel panel-default">
		<div class="panel-body has-feedback">

			<?php echo $this->element('BbsPosts/post_form'); ?>

			<hr />

			<?php echo $this->element('Comments.form'); ?>

		</div>
		<div class="panel-footer text-center">

			<?php echo $this->element('Bbses.post_workflow_buttons'); ?>

		</div>
	</div>
	<?php echo $this->element('Comments.index'); ?>

<?php echo $this->Form->end(); ?>
</div>

</div>