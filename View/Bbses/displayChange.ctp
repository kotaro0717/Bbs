<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/bbses/js/bbses.js', false); ?>

<div id="nc-bbs-display-change-<?php echo (int)$frameId; ?>"
		ng-controller="Bbses"
		ng-init="initialize(<?php echo h(json_encode($this->viewVars)); ?>)">

<?php $formName = 'BbsForm'; ?>

<?php $this->start('titleForModal'); ?>
<?php echo __d('bbses', 'Plugin name'); ?>
<?php $this->end(); ?>

<?php $this->startIfEmpty('tabList'); ?>
<li>
	<a href="<?php echo $this->Html->url(
					'/bbses/bbses/view' . '/' . $frameId); ?>" ng-click="showSetting('edit')">
		<?php echo __d('bbses', 'Bbs edit'); ?>
	</a>
</li>
<li class="active">
	<a href="" ng-click="showSetting('displayChange')">
		<?php echo __d('bbses', 'Display change'); ?>
	</a>
</li>
<li>
	<a href="<?php echo $this->Html->url(
					'/bbses/bbsAuthoritySettings/view' . '/' . $frameId); ?>" ng-click="showSetting('authoritySetting')">
		<?php echo __d('bbses', 'Authority setting'); ?>
	</a>
</li>
<?php $this->end(); ?>

<div class="modal-header">
	<?php $titleForModal = $this->fetch('titleForModal'); ?>
	<?php if ($titleForModal) : ?>
		<?php echo $titleForModal; ?>
	<?php else : ?>
		<br />
	<?php endif; ?>
</div>

<div class="modal-body">
	<?php $tabList = $this->fetch('tabList'); ?>
	<?php if ($tabList) : ?>
		<ul class="nav nav-tabs" role="tablist">
			<?php echo $tabList; ?>
		</ul>
		<br />
		<?php $tabId = $this->fetch('tabIndex'); ?>
		<div class="tab-content" ng-init="tab.setTab(<?php echo (int)$tabId; ?>)">
	<?php endif; ?>

	<div>
	<?php echo $this->Form->create('Bbs', array(
			'name' => 'form',
			/* 'name' => $formName, */
			'novalidate' => true,
		)); ?>
		<?php echo $this->Form->hidden('id'); ?>
		<?php echo $this->Form->hidden('Frame.id', array(
			'value' => $frameId,
		)); ?>
		<?php echo $this->Form->hidden('Block.id', array(
			'value' => $blockId,
		)); ?>

		<div class="panel panel-default" >
			<div class="panel-body has-feedback">
				<?php echo $this->element('displayChange'); ?>
			</div>

			<div class="panel-footer text-center">
				<a href="<?php echo $this->Html->url(
					'/bbses/bbses/index/' . $frameId) ?>" class="btn btn-default" ng-disabled="sending">
					<span class="glyphicon glyphicon-remove"></span>
					<?php echo __d('net_commons', 'Cancel'); ?>
				</a>
				<?php echo $this->Form->button(__d('net_commons', 'OK'),
								array(
									'class' => 'btn btn-primary',
									'name' => 'save_0',
								)) ?>
			</div>
		</div>

	<?php echo $this->Form->end(); ?>
</div>

</div>
