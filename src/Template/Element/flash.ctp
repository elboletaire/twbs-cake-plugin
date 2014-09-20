<?php
$is_closable = !isset($close) || (isset($close) && $close == true); ?>
<div class="alert <?php if ($is_closable) echo 'alert-dismissable '; ?>alert-<?php echo $class ?>" role="alert">
<?php if ($is_closable): ?>
	<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
<?php endif ?>
	<?php echo $message ?>
</div>
