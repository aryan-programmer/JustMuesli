<?php require_once "php/common.php";
require_once "php/templates.php";
basicSetup(true);
$editUserDetails = $pages[PageIndex::EditUserDetails];
$mixer           = $pages[PageIndex::Mixer];
$mixes           = $pages[PageIndex::Mixes];
$order           = $pages[PageIndex::Order];
show_html_start_block(PageIndex::Home);
show_messages();?>
	<div class="text-center w-fit-content mx-auto">
		<h1 class="text-center">Just&nbsp;Muesli</h1>
		<a
			href="<?= $editUserDetails->path ?>" class="button w-100 my-btn">
			<?= $editUserDetails->name ?>
		</a>
		<a href="<?= $mixer->path ?>" class="button w-100 my-btn">
			<?= $mixer->name ?>
		</a>
		<a href="<?= $mixes->path ?>" class="button w-100 my-btn">
			<?= $mixes->name ?>
		</a>
		<a href="<?= $order->path ?>" class="button w-100 my-btn">
			<?= $order->name ?>
		</a>
	</div>
<?php show_html_end_block();
