<?php 
$is_closable = !isset($close) || (isset($close) && $close == true); ?>
<div class="alert <?php if ($is_closable) echo 'alert-dismissable '; ?>alert-<?php echo $class ?>">
<?php if ($is_closable): ?>
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<?php endif ?>
	<?php echo $message ?>
</div>
