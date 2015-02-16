<div class="form-group">
	<label class="control-label">
		<?php echo __d('bbses', 'Bbs name'); ?>
	</label>
	<?php echo $this->element('NetCommons.required'); ?>

	<?php echo $this->Form->input('Bbs.name',
				array(
					'label' => false,
					'class' => 'form-control',
					'ng-model' => 'bbses.bbses.name',
					'required' => 'required',
					'autofocus' => true,
				)) ?>

	<div class="has-error">
		<?php if ($this->validationErrors['Bbs']): ?>
		<?php foreach ($this->validationErrors['Bbs']['name'] as $message): ?>
			<div class="help-block">
				<?php echo $message; ?>
			</div>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>

<div class='form-group'>
	<?php
		echo $this->Form->input('Bbs.comment_flag', array(
					'label' => __d('bbses', 'Can comments'),
					'div' => false,
					'type' => 'checkbox',
					'ng-model' => 'bbses.bbses.comment_flag',
				)
			);
	?>
</div>

<div class='form-group'>
	<?php
		echo $this->Form->input('Bbs.vote_flag', array(
					'label' => __d('bbses', 'Can votes'),
					'div' => false,
					'type' => 'checkbox',
					'ng-model' => 'bbses.bbses.vote_flag',
				)
			);
	?>
</div>