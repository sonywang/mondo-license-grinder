<?php
include('../db.inc.php');
$fields=array(
	'research_private_study'=>'Research/Private Study',
	'handouts'=>'Course Handouts',
	'course_pack'=>'Course Pack',
	'blackboard'=>'Learning Management Systems',
	'e_reserves'=>'E-reserves',
	'durable_url'=>'Durable URL',
	'walk_in'=>'Walk-In',
	'alumni_access'=>'Alumni Access',
	'ill_any'=>'ILL',
	'ill_print'=>'ILL Print',
	'ill_electronic'=>'ILL Electronic',
	'ill_ariel'=>'ILL Ariel',
	'fulltext'=>'Full Text',
	'images'=>'Images (in classroom materials)',
	'perpetual_access'=>'Perpetual Access',
	'password_required'=>'Password'
);
if(!empty($_POST['html'])){
	foreach($_POST['html'] as $k=>$v){
		$db->setHTML($k,$v);
	}
}else{
}
$moreinsert='<script type="text/javascript" src="'.BASE_URL.'scripts/staff.js"></script>
<script type="text/javascript" src="'.BASE_URL.'scripts/jquery.tinymce.js"></script>
';
include('../header.inc.php');
?>
<div class="optionalWrapper small fullpage">
<h1>Static Text for License Field Descriptions</h1>
<?php include('nav.inc.php'); ?>
<hr />
<form action="" method="post">
	<input type="submit" value="Update" />
	<table>
<?php
foreach($fields as $field=>$fieldname){
	$question=$db->getHTML($field.'-question');
	$yes=$db->getHTML($field.'-Yes');
	$no=$db->getHTML($field.'-No');
	$ask=$db->getHTML($field.'-Ask');
//	$na=$db->getHTML($field.'-NotApplicable');
	echo '
		<tr>
			<th>'.$fieldname.'</th>
			<td class="bp">
			    Question Text:
				<a id="'.$field.'-question"></a><br />
				<textarea class="tinymce" name="html['.$field.'-question]">'.$question.'</textarea><br />
			    &ldquo;Yes&rdquo; Text:
				<a id="'.$field.'-Yes"></a><br />
				<textarea class="tinymce" name="html['.$field.'-Yes]">'.$yes.'</textarea><br />
				&ldquo;No&rdquo; Text: 
				<a id="'.$field.'-No"></a><br />
				<textarea class="tinymce" name="html['.$field.'-No]">'.$no.'</textarea><br />
				&ldquo;Ask&rdquo; Text: 
				<a id="'.$field.'-Ask"></a><br />
				<textarea class="tinymce" name="html['.$field.'-Ask]">'.$ask.'</textarea>
			</td>
		</tr>';
}


?>
	</table>
	<input type="submit" value="Update" />
<form>
<script type="text/javascript">
$('.tinymce').tinymce({
	theme:'advanced',
	script_url:'../scripts/tinymce/jscripts/tiny_mce/tiny_mce.js',
	init_instance_callback:reposition
});
$('tr:odd').css('backgroundColor','#ddd');
var c=0;
function reposition(i){
	if(!document.location.hash) return;
	c++;
	if(c==<?php echo 3*count($fields); ?>){
		//$('.tinymce').show();
		document.location=document.location;
	}
}
</script>
<?php include('../footer.inc.php')?>