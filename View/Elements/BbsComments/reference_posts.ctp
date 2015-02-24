<div class="panel-group" id="nc-bbs-comment-accordion-<?php echo 1; ?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">
				<div class="text-left">
					<a
					   data-toggle="collapse"
					   data-parent="#nc-bbs-comment-accordion-<?php echo 1; ?>"
					   href="#nc-bbs-parent-post-<?php echo 1; ?>"
					   aria-expanded="false"
					   aria-controls="nc-bbs-post-<?php echo 1; ?>">
						<?php echo $bbsPosts['title']; ?>
					</a>
				</div>
			</div>
		</div>
		<div id="nc-bbs-parent-post-<?php echo 1; ?>"
			 class="panel-collapse collapse">
			<div class="panel-body">
				<p>
					<?php echo $bbsPosts['content']; ?>
				</p>
			</div>
		</div>
	</div>
</div>