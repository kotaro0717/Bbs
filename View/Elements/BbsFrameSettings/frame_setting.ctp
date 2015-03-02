<div class='form-group'>
	<?php
		echo $this->Form->label(__d('bbses', 'Visible post row'));
	?>
	&nbsp;
	<?php
		echo $this->Form->input('BbsFrameSetting.visible_post_row', array(
					'label' => false,
					'type' => 'select',
					'class' => 'form-control',
					'options' => BbsFrameSetting::getDisplayNumberOptions(),
					'selected' => $bbsSettings['visible_post_row'],
					'ng-model' => 'bbsSettings.visible_post_row',
					'autofocus' => true,
				)
			);
	?>
</div>

<div class='form-group'>
	<?php
		echo $this->Form->label(__d('bbses', 'Visible comment row'));
	?>
	&nbsp;
	<?php
		echo $this->Form->input('BbsFrameSetting.visible_comment_row', array(
					'label' => false,
					'type' => 'select',
					'class' => 'form-control',
					'options' => BbsFrameSetting::getDisplayNumberOptions(),
					'selected' => $bbsSettings['visible_comment_row'],
					'ng-model' => 'bbsSettings.visible_comment_row',
				)
			);
	?>
</div>
