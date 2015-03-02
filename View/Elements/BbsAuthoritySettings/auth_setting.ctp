<div class="form-group">
	<?php
		echo $this->Form->label(__d('bbses', 'Post creatable authority'));
	?>

	<ul class="list-inline" style="margin-left:20px">
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('bbses', 'Room administrator'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('bbses', 'Cheif editor'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('bbses', 'Editor'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('Bbs.post_create_authority', array(
						'label' => __d('bbses', 'General'),
						'div' => false,
						'type' => 'checkbox',
						'ng-model' => 'bbses.post_create_authority',
						'autofocus' => true,
					)
				);
		?>
		</li>
	</ul>
</div>

<div class="form-group">
	<?php
		echo $this->Form->label(__d('bbses', 'Post publishable authority'));
	?>

	<ul class="list-inline" style="margin-left:20px">
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('bbses', 'Room administrator'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('bbses', 'Cheif editor'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('Bbs.editor_publish_authority', array(
						'label' => __d('bbses', 'Editor'),
						'div' => false,
						'type' => 'checkbox',
						'ng-model' => 'bbses.editor_publish_authority',
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('Bbs.general_publish_authority', array(
						'label' => __d('bbses', 'General'),
						'div' => false,
						'type' => 'checkbox',
						'ng-model' => 'bbses.general_publish_authority',
					)
				);
		?>
		</li>
	</ul>
</div>

<div class="form-group">
	<?php
		echo $this->Form->label(__d('bbses', 'Comment creatable authority'));
	?>

	<ul class="list-inline" style="margin-left:20px">
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('bbses', 'Room administrator'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('bbses', 'Cheif editor'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('bbses', 'Editor'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('Bbs.comment_create_authority', array(
						'label' => __d('bbses', 'General'),
						'div' => false,
						'type' => 'checkbox',
						'ng-model' => 'bbses.comment_create_authority',
					)
				);
		?>
		</li>
	</ul>
</div>