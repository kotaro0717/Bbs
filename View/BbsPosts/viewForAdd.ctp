<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/bbses/js/bbses.js', false); ?>

<div id="nc-bbs-add-<?php echo (int)$frameId; ?>"
		ng-controller="Bbses"
		ng-init="initialize(<?php echo h(json_encode($this->viewVars)); ?>)">

<!-- パンくずリスト -->
<ol class="breadcrumb">
	<li><a href="<?php echo $this->Html->url(
				'/bbses/bbses/index/' . $frameId) ?>">
		<?php echo $dataForView['bbses']['name']; ?></a>
	</li>
	<?php if (isset($dataForView['bbsPosts']['id'])) : ?>
		<li><a href="<?php echo $this->Html->url(
					'/bbses/bbsPosts/view/' . $frameId . '/' . $dataForView['bbsPosts']['id']) ?>">
			<?php echo $dataForView['bbsPosts']['title']; ?></a>
		</li>
	<?php endif; ?>
	<li class="active"><?php echo $dataForView['addStrings']; ?></li>
</ol>

<div class="panel panel-default">
	<div class="panel-body">
		<div class="form-group">
			<label class="control-label">
				<?php echo __d('bbses', 'Title'); ?>
			</label>
			<?php echo $this->element('NetCommons.required'); ?>

			<div>
				<?php echo $this->Form->input('title',
							array(
								'label' => false,
								'class' => 'form-control',
								'ng-model' => 'bbses.bbsPosts.title',
								'required' => 'required',
								'autofocus' => true,
							)) ?>
			</div>
			<div class="has-error">
				<?php if ($this->validationErrors['Bbs']): ?>
				<?php foreach ($this->validationErrors['Bbs']['title'] as $message): ?>
					<div class="help-block">
						<?php echo $message ?>
					</div>
				<?php endforeach ?>
				<?php else : ?>
					<br />
				<?php endif; ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label">
				<?php echo __d('bbses', 'Content'); ?>
			</label>
			<?php echo $this->element('NetCommons.required'); ?>

			<div class="nc-wysiwyg-alert">
				<?php echo $this->Form->textarea('content',
							array(
								'label' => false,
								'class' => 'form-control',
								'ui-tinymce' => 'tinymce.options',
								'ng-model' => 'bbses.bbsPosts.content',
								'rows' => 5,
								'required' => 'required',
							)) ?>
			</div>
			<div class="has-error">
				<?php if ($this->validationErrors['Bbs']): ?>
				<?php foreach ($this->validationErrors['Bbs']['content'] as $message): ?>
					<div class="help-block">
						<?php echo $message ?>
					</div>
				<?php endforeach ?>
				<?php else : ?>
					<br />
				<?php endif ?>
			</div>
		</div>
	</div>
	<div class="panel-footer text-center">
		<?php echo $this->element('Bbses.workflow_buttons'); ?>
	</div>
</div>

</div>