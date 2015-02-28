<div class="form-group">
	<label class="control-label">
		<?php echo __d('bbses', 'Title'); ?>
	</label>
	<?php echo $this->element('NetCommons.required'); ?>

	<div  class="nc-bbs-add-post-title-alert">
		<?php echo $this->Form->input('title',
					array(
						'label' => false,
						'class' => 'form-control',
						'ng-model' => 'bbsPosts.title',
						'required' => 'required',
						'autofocus' => true,
					)) ?>
	</div>

	<div class="has-error">
		<?php if (isset($this->validationErrors['BbsPost']['title'])): ?>
		<?php //foreach ($this->validationErrors['BbsPost']['title'] as $message): ?>
			<div class="help-block">
				<?php //echo $message ?>
			</div>
		<?php //endforeach; ?>
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
						'ng-model' => 'bbsPosts.content',
						'rows' => 5,
						'required' => 'required',
					)) ?>
	</div>

	<div class="has-error">
		<?php if (isset($this->validationErrors['BbsPost']['content'])): ?>
		<?php foreach ($this->validationErrors['BbsPost']['content'] as $message): ?>
			<div class="help-block">
				<?php echo $message; ?>
			</div>
		<?php endforeach; ?>
		<?php else : ?>
			<br />
		<?php endif; ?>
	</div>
</div>