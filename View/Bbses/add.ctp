<<<<<<< HEAD
<!-- パンくずリスト -->
<ol class="breadcrumb">
  <li><a href="/bbses/bbses/index/<?php echo $frameId; ?>/"><?php echo __d('bbses', 'Bbs name'); ?></a></li>
  <li><a href="/bbses/bbses/view/<?php echo $frameId; ?>/"><?php echo __d('bbses', 'Post title');?></a></li>
</ol>

<div class="panel panel-default">
	<div class="panel-body">
		<div class="form-group">
			<label class="control-label">
				<?php echo __d('bbses', 'Title'); ?>
			</label>
			<?php echo $this->element('NetCommons.required'); ?>

			<form type="text" name="title" class="form-control" required></form>

			<?php echo sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Title')); ?>
		</div>
		<div class="form-group">
			<label class="control-label">
				<?php echo __d('bbses', 'Content'); ?>
			</label>
			<?php echo $this->element('NetCommons.required'); ?>

			<textarea name="content" class="form-control" rows="5"
					 required>
			</textarea>

			<?php echo sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Content')); ?>
		</div>
	</div>
	<div class="panel-footer text-center">
		<?php echo $this->element('NetCommons.workflow_buttons'); ?>
	</div>
=======
<!-- パンくずリスト -->
<ol class="breadcrumb">
  <li><a href="/bbses/bbses/index/<?php echo $frameId; ?>/"><?php echo __d('bbses', 'Bbs name'); ?></a></li>
  <li><a href="/bbses/bbses/view/<?php echo $frameId; ?>/"><?php echo __d('bbses', 'Post title');?></a></li>
</ol>

<div class="panel panel-default">
	<div class="panel-body">
		<div class="form-group">
			<label class="control-label">
				<?php echo __d('bbses', 'Title'); ?>
			</label>
			<?php echo $this->element('NetCommons.required'); ?>

			<form type="text" name="title" class="form-control" required></form>

			<?php echo sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Title')); ?>
		</div>
		<div class="form-group">
			<label class="control-label">
				<?php echo __d('bbses', 'Content'); ?>
			</label>
			<?php echo $this->element('NetCommons.required'); ?>

			<textarea name="content" class="form-control" rows="5"
					 required>
			</textarea>

			<?php echo sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Content')); ?>
		</div>
	</div>
	<div class="panel-footer text-center">
		<?php echo $this->element('NetCommons.workflow_buttons'); ?>
	</div>
>>>>>>> dbcca531b4145062e2121fed45bb9e86490bea6f
</div>