<?php
/* What's the blurb */
include('../db.inc.php');
if(isset($_POST['blurb'])){
	$blurb=$db->setHTML('blurb',$_POST['blurb']);
}else{
	$blurb=$db->getHTML('blurb');
}
if(isset($_POST['legal'])){
	$legal=$db->setHTML('legal',$_POST['legal']);
}else{
	$legal=$db->getHTML('legal');
}
$moreinsert='<script type="text/javascript" src="'.BASE_URL.'scripts/staff.js"></script>
<script type="text/javascript" src="'.BASE_URL.'scripts/jquery.tinymce.js"></script>
';
include('../header.inc.php');
?>
<div class="optionalWrapper small fullpage">
<h1>Front Page Blurb</h1>
<?php include('nav.inc.php'); ?>
<hr />
<form action="" method="post">
<textarea name="blurb" class="tinymce blurb">
<?php echo $blurb; ?>
</textarea>
<h2>Legal Blurb</h2>
<hr />
<textarea name="legal" class="tinymce blurb">
<?php echo $legal; ?>
</textarea>
<input type="submit" value="Update" />
</form>
</div>
<script type="text/javascript">
$('.tinymce').tinymce({
	theme:'advanced',
	content_css:'../stylesheets/blurb.css',
	script_url:'../scripts/tinymce/jscripts/tiny_mce/tiny_mce.js',
	body_id: 'blurb'
});
</script>
<?php include('../footer.inc.php')?>