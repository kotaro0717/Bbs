<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/bbses/js/bbses.js', false); ?>

<div id="nc-bbs-auth-setting-<?php echo (int)$frameId; ?>"
		ng-controller="BbsAuthoritySettings"
		ng-init="initialize(<?php echo h(json_encode($this->viewVars)); ?>)">

<?php $formName = 'BbsForm'; ?>

<?php $this->start('titleForModal'); ?>
<?php echo __d('bbses', 'Plugin name'); ?>
<?php $this->end(); ?>

<?php $this->startIfEmpty('tabList'); ?>
<li>
	<a href="<?php echo $this->Html->url(
					'/bbses/bbses/edit' . '/' . $frameId); ?>">
		<?php echo __d('bbses', 'Bbs edit'); ?>
	</a>
</li>
<li>
	<a href="<?php echo $this->Html->url(
					'/bbses/bbsFrameSettings/edit' . '/' . $frameId); ?>">
		<?php echo __d('bbses', 'Display change'); ?>
	</a>
</li>
<li class="active">
	<a href="">
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
				<?php echo $this->element('BbsAuthoritySettings/authSetting'); ?>
			</div>

			<div class="panel-footer text-center">
				<button type="button" class="btn btn-default" ng-click="cancel()" ng-disabled="sending">
					<span class="glyphicon glyphicon-remove"></span>
					<?php echo __d('net_commons', 'Cancel'); ?>
				</button>
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
