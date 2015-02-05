<div class="form-group">
	<label class="control-label">
		<?php echo __d('bbses', 'Bbs name'); ?>
	</label>
	<?php echo $this->element('NetCommons.required'); ?>

	<?php echo $this->Form->input('title',
				array(
					'label' => false,
					'class' => 'form-control',
					'ng-model' => 'bbses.title',
					'required' => 'required',
					'autofocus' => true,
				)) ?>

	<div class="has-error">
		<?php if ($this->validationErrors['Bbs']): ?>
		<?php foreach ($this->validationErrors['Bbs']['title'] as $message): ?>
			<div class="help-block">
				<?php echo $message ?>
			</div>
		<?php endforeach ?>
		<?php endif ?>
	</div>
</div>

<div class='form-group'>
	<?php
		echo $this->Form->input('commentFlag', array(
					'label' => __d('bbses', 'Can comments'),
					'div' => false,
					'type' => 'checkbox',
					'ng-model' => 'bbses.commentFlag',
				)
			);
	?>
</div>

<div class='form-group'>
	<?php
		echo $this->Form->input('voteFlag', array(
					'label' => __d('bbses', 'Can votes'),
					'div' => false,
					'type' => 'checkbox',
					'ng-model' => 'bbses.voteFlag',
				)
			);
	?>
</div>

↓↓↓　以降、別タブに移動予定です。　↓↓↓<br /><br />

<div class='form-group'>
	<?php
		echo $this->Form->input('visiblePostRow', array(
					'label' => __d('bbses', 'Visible post row'),
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
					//'ng-model' => 'bbses.visiblePostRow',
				)
			);
	?>
</div>

<div class='form-group'>
	<?php
		echo $this->Form->input('visibleCommentRow', array(
					'label' => __d('bbses', 'Visible comment row'),
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
					//'ng-model' => 'bbses.visibleCommentRow',
				)
			);
	?>
</div>

<div class='form-group'>
	<?php
		echo $this->Form->label(__d('bbses', 'Post authority'));
		echo $this->Form->input('', array(
					'label' => __d('bbses', 'Room administrator'),
					'div' => false,
					'type' => 'checkbox',
					'checked' => true,
					'disabled' => true
			));
		echo $this->Form->input('', array(
					'label' => __d('bbses', 'Cheif editor'),
					'div' => false,
					'type' => 'checkbox',
					'checked' => true,
					'disabled' => true
			));
		echo $this->Form->input('', array(
					'label' => __d('bbses', 'Editor'),
					'div' => false,
					'type' => 'checkbox',
					'checked' => true,
					'disabled' => true
			));
		echo $this->Form->input('visiblePostRow', array(
					'label' => __d('bbses', 'General'),
					'div' => false,
					'type' => 'checkbox',
					'options' => array(
						"1件",
						"5件",
						"10件",
						"20件",
						"50件",
						"100件",
					),
					'selected' => $dataForView['bbsSettings']['visiblePostRow'],
					'ng-model' => 'bbses.visiblePostRow',
				)
			);
	?>
</div>
