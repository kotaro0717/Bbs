<?php //echo var_dump($currentPage); ?>
<?php //echo var_dump($hasPrevPage); ?>
<?php //echo var_dump($hasNextPage); ?>
<?php //echo var_dump($hasNextSecondPage); ?>
<?php //echo var_dump($hasFourPage); ?>
<?php //echo var_dump($hasFivePage); ?>

<nav>
	<ul class="pagination">

		<!-- [<<]の表示 -->
		<li class="<?php echo ($currentPage < 4)? 'disabled' : ''; ?>">
			<?php $prevPager = $currentPage - 1 ?>
			<a href="<?php echo ($currentPage < 4)? '' : $this->Html->url(
					'/bbses/bbses/index/' . $frameId . '/' . $prevPager . '/' . $sortParams . '/' . $currentVisiblePostRow) ; ?>"
				aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
		</li>

		<!-- 1ページの時表示しない -->
		<?php if (!($currentPage === 1) && !($currentPage === 2)) : ?>
			<?php $minPageNum = $currentPage - 2 ?>
			<li><a href="<?php echo $this->Html->url(
					'/bbses/bbses/index/' . $frameId . '/' . $minPageNum . '/' . $sortParams . '/' . $currentVisiblePostRow) ?>">
				<?php echo $minPageNum++; ?></a>
			</li>
			<li><a href="<?php echo $this->Html->url(
					'/bbses/bbses/index/' . $frameId . '/' . $minPageNum . '/' . $sortParams . '/' . $currentVisiblePostRow) ?>">
				<?php echo $minPageNum; ?></a>
			</li>

		<!-- 2ページの時1ページを表示 -->
		<?php elseif ($currentPage === 2) : ?>
			<li><a href="<?php echo $this->Html->url(
					'/bbses/bbses/index/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisiblePostRow) ?>">
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
						'/bbses/bbses/index/' . $frameId . '/' . $nextPageNum . '/' . $sortParams . '/' . $currentVisiblePostRow) ?>">
					<?php echo $nextPageNum++; ?></a>
			</li>
		<?php else : ?>
			<li class="disabled"><a href="">
					<?php echo $nextPageNum++; ?></a>
			</li>
		<?php endif; ?>

		<?php if ($hasNextSecondPage) : ?>
			<li><a href="<?php echo $this->Html->url(
						'/bbses/bbses/index/' . $frameId . '/' . $nextPageNum . '/' . $sortParams . '/' . $currentVisiblePostRow) ?>">
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
						'/bbses/bbses/index/' . $frameId . '/' . 4 . '/' . $sortParams . '/' . $currentVisiblePostRow) ?>">
					<?php echo 4; ?></a>
				</li>
			<?php else : ?>
				<li class="disabled">
					<a href=""><?php echo 4; ?></a>
				</li>
			<?php endif; ?>

			<?php if ($hasFivePage) : ?>
				<li><a href="<?php echo $this->Html->url(
						'/bbses/bbses/index/' . $frameId . '/' . 5 . '/' . $sortParams . '/' . $currentVisiblePostRow) ?>">
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
						'/bbses/bbses/index/' . $frameId . '/' . 5 . '/' . $sortParams . '/' . $currentVisiblePostRow) ?>">
					<?php echo 5; ?></a>
				</li>
			<?php else : ?>
				<li class="disabled">
					<a href=""><?php echo 5; ?></a>
				</li>
			<?php endif; ?>

		<?php endif; ?>

		<!-- [>>]の表示 -->
		<li class="<?php echo ($hasNextPage)? '' : 'disabled'; ?>">
			<?php $nextPager = $currentPage + 1 ?>
			<a href="<?php echo ($hasNextPage)? $this->Html->url(
					'/bbses/bbses/index/' . $frameId . '/' . $nextPager . '/' . $sortParams . '/' . $currentVisiblePostRow) : ''; ?>"
					aria-label="Next">
				<span aria-hidden="true">&raquo;</span>
			</a>
		</li>
	</ul>
</nav>