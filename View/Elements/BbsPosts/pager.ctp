<?php //echo debug($currentPage); ?>
<?php //echo debug($hasPrevPage); ?>
<?php //echo debug($hasNextPage); ?>
<?php //echo debug($hasNextSecondPage); ?>
<?php //echo debug($hasFourPage); ?>
<?php //echo debug($hasFivePage); ?>
<?php //echo debug($baseUrl); ?>

<nav>
	<ul class="pagination">

		<!-- [<<]の表示 -->
		<li class="<?php echo ($currentPage < 4)? 'disabled' : ''; ?>">
			<?php $prevPager = $currentPage - 1 ?>
			<a href="<?php echo ($currentPage < 4)? '' : $this->Html->url(
					'/' . $baseUrl . '/' . $frameId . '/' . $prevPager . '/' . $sortParams . '/' . $currentVisibleRow) ; ?>"
				aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
		</li>

		<!-- 1ページの時表示しない -->
		<?php if (!($currentPage === 1) && !($currentPage === 2)) : ?>
			<?php $minPageNum = $currentPage - 2 ?>
			<li><a href="<?php echo $this->Html->url(
					'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $minPageNum . '/' . $sortParams . '/' . $currentVisibleRow) ?>">
				<?php echo $minPageNum++; ?></a>
			</li>
			<li><a href="<?php echo $this->Html->url(
					'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $minPageNum . '/' . $sortParams . '/' . $currentVisibleRow) ?>">
				<?php echo $minPageNum; ?></a>
			</li>

		<!-- 2ページの時1ページを表示 -->
		<?php elseif ($currentPage === 2) : ?>
			<li><a href="<?php echo $this->Html->url(
					'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow) ?>">
				<?php echo 1; ?></a>
			</li>

		<?php endif; ?>

		<!-- 今のページ -->
		<li class="active"><a href="#">
			<?php echo $currentPage; ?><span class="sr-only">(current)</span></a>
		</li>

		<?php $nextPageNum = $currentPage + 1; ?>
		<?php if ($hasNextPage) : ?>
			<li><a href="<?php echo $this->Html->url(
						'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $nextPageNum . '/' . $sortParams . '/' . $currentVisibleRow) ?>">
					<?php echo $nextPageNum++; ?></a>
			</li>
		<?php else : ?>
			<li class="disabled"><a href="">
					<?php echo $nextPageNum++; ?></a>
			</li>
		<?php endif; ?>

		<?php if ($hasNextSecondPage) : ?>
			<li><a href="<?php echo $this->Html->url(
						'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $nextPageNum . '/' . $sortParams . '/' . $currentVisibleRow) ?>">
					<?php echo $nextPageNum++; ?></a>
			</li>
		<?php else : ?>
			<li class="disabled"><a href="">
					<?php echo $nextPageNum++; ?></a>
			</li>
		<?php endif; ?>

		<!-- 1ページの時+2表示 -->
		<?php if ($currentPage === 1) : ?>
			<?php if ($hasFourPage) : ?>
				<li><a href="<?php echo $this->Html->url(
						'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 4 . '/' . $sortParams . '/' . $currentVisibleRow) ?>">
					<?php echo 4; ?></a>
				</li>
			<?php else : ?>
				<li class="disabled">
					<a href=""><?php echo 4; ?></a>
				</li>
			<?php endif; ?>

			<?php if ($hasFivePage) : ?>
				<li><a href="<?php echo $this->Html->url(
						'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 5 . '/' . $sortParams . '/' . $currentVisibleRow) ?>">
					<?php echo 5; ?></a>
				</li>
			<?php else : ?>
				<li class="disabled">
					<a href=""><?php echo 5; ?></a>
				</li>
			<?php endif; ?>

		<!-- 2ページの時+1表示 -->
		<?php elseif ($currentPage === 2) : ?>

			<?php if ($hasFivePage) : ?>
				<li><a href="<?php echo $this->Html->url(
						'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . 5 . '/' . $sortParams . '/' . $currentVisibleRow) ?>">
					<?php echo 5; ?></a>
				</li>
			<?php else : ?>
				<li class="disabled">
					<a href=""><?php echo 5; ?></a>
				</li>
			<?php endif; ?>

		<?php endif; ?>

		<!-- [>>]の表示 -->
		<li class="<?php echo ($hasNextPage &&
								$hasFourPage && $hasFivePage &&
								$hasNextPage && $hasNextSecondPage)? '' : 'disabled'; ?>">
			<?php $nextPager = $currentPage + 1 ?>
			<a href="<?php echo ($hasNextPage && $hasFourPage && $hasFivePage && $hasNextPage && $hasNextSecondPage)?
					$this->Html->url(
						'/' . $baseUrl . '/' . $frameId . '/' . $bbsPosts['id'] . '/' . $nextPager . '/' . $sortParams . '/' . $currentVisibleRow) : ''; ?>"
					aria-label="Next">
				<span aria-hidden="true">&raquo;</span>
			</a>
		</li>
	</ul>
</nav>