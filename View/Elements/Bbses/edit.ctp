<div class="form-group">
	<label class="control-label">
		<?php echo __d('bbses', 'Bbs name'); ?>
	</label>
	<?php echo $this->element('NetCommons.required'); ?>

	<?php echo $this->Form->input('Bbs.name',
				array(
					'label' => false,
					'class' => 'form-control',
					'ng-model' => 'bbses.name',
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
		<?php else : ?>
			<br />
		<?php endif; ?>
	</div>
</div>

<div class='form-group'>
	<?php
		echo $this->Form->input('Bbs.use_comment', array(
					'label' => __d('bbses', 'Use comment'),
					'div' => false,
					'type' => 'checkbox',
					'ng-model' => 'bbses.use_comment',
				)
			);
	?>
</div>

<div class='form-group col-sm-offset-1 col-xs-offset-1'>
	<?php
		echo $this->Form->input('Bbs.auto_approval', array(
					'label' => __d('bbses', 'Auto approval'),
					'div' => false,
					'type' => 'checkbox',
					'ng-model' => 'bbses.auto_approval',
				)
			);
	?>
</div>

<div class='form-group'>
	<?php
		echo $this->Form->input('Bbs.use_like_button', array(
					'label' => __d('bbses', 'Use like button'),
					'div' => false,
					'type' => 'checkbox',
					'ng-model' => 'bbses.use_like_button',
				)
			);
	?>
	<span class="glyphicon glyphicon-thumbs-up"></span>
</div>

<div class='form-group'>
	<?php
		echo $this->Form->input('Bbs.use_unlike_button', array(
					'label' => __d('bbses', 'Use unlike button'),
					'div' => false,
					'type' => 'checkbox',
					'ng-model' => 'bbses.use_unlike_button',
				)
			);
	?>
	<span class="glyphicon glyphicon-thumbs-down"></span>
</div>