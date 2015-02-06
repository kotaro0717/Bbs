<div class='form-group'>
	<?php
		echo $this->Form->label(__d('bbses', 'Visible post row'));
	?>
	&nbsp;
	<?php
		echo $this->Form->input('visiblePostRow', array(
					'label' => false,
					'div' => false,
					'type' => 'select',
					'options' => array(
						'1' => "1件",
						'5' => "5件",
						'10' => "10件",
						'20' => "20件",
						'50' => "50件",
						'100' => "100件",
					),
					'selected' => $dataForView['bbsSettings']['visiblePostRow'],
					'ng-model' => 'bbses.bbsSettings.visiblePostRow',
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
		echo $this->Form->input('visibleCommentRow', array(
					'label' => false,
					'div' => false,
					'type' => 'select',
					'options' => array(
						'1' => "1件",
						'5' => "5件",
						'10' => "10件",
						'20' => "20件",
						'50' => "50件",
						'100' => "100件",
					),
					'selected' => $dataForView['bbsSettings']['visibleCommentRow'],
					'ng-model' => 'bbses.bbsSettings.visibleCommentRow',
				)
			);
	?>
</div>
