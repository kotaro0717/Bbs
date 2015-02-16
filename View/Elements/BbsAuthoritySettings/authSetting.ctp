<div class='form-group'>
	<?php
		echo $this->Form->label(__d('bbses', 'Post authority'));
	?>
	<br />
	<?php
		echo $this->Form->input('', array(
					'label' => __d('bbses', 'Room administrator'),
					'div' => false,
					'type' => 'checkbox',
					'checked' => true,
					'disabled' => true
			));
	?>
	&nbsp;
	<?php
		echo $this->Form->input('', array(
					'label' => __d('bbses', 'Cheif editor'),
					'div' => false,
					'type' => 'checkbox',
					'checked' => true,
					'disabled' => true
			));
	?>
	&nbsp;
	<?php
		echo $this->Form->input('', array(
					'label' => __d('bbses', 'Editor'),
					'div' => false,
					'type' => 'checkbox',
					'checked' => true,
					'disabled' => true
			));
	?>
	&nbsp;
	<?php
		echo $this->Form->input('Bbs.posts_authority', array(
					'label' => __d('bbses', 'General'),
					'div' => false,
					'type' => 'checkbox',
					'ng-model' => 'bbses.bbses.posts_authority',
					'autofocus' => true,
				)
			);
	?>
</div>