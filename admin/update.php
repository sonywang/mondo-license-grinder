<?php
//exit();
include('../db.inc.php');
if(!$_POST){
	header('Location: '.BASE_URL.'admin/');
	exit();
}
function pv($key,$default=false){
	if(isset($_POST[$key])){
		return $_POST[$key];
	}
	return $default;
}
$id=$_POST['id'];
$vendor=false;
$consortium=false;
if(isset($_POST['newvendor']) && $vendorname=trim($_POST['newvendor'])){
	$vendor=$db->cvUpdate('vendor',-1,$vendorname);
}else{
	$vendor=$_POST['vendor'];
}
if(isset($_POST['newconsortium']) && $consortiumname=trim($_POST['newconsortium'])){
	$consortium-$db->cvUpdate('consortium',-1,$consortiumname);
}else{
	$consortium=$_POST['consortium'];
}
$title=trim(pv('title'));
$tag=trim(pv('tag'));
$e_reserves=pv('e_reserves',0);
$handouts=pv('handouts',2);
$images=pv('images',2);
$course_pack=pv('course_pack',0);
$durable_url=pv('durable_url',0);
$alumni_access=pv('alumni_access',0);
$ill_print=pv('ill_print',0);
$ill_electronic=pv('ill_electronic',0);
$ill_ariel=pv('ill_ariel',0);
$walk_in=pv('walk_in',0);
$research_private_study=pv('research_private_study',0);
$blackboard=pv('blackboard',0);
$perpetual_access=pv('perpetual_access',0);
$perpetual_access_note=pv('perpetual_access_note','');
$sherpa_romeo=pv('sherpa_romeo','');
$password=pv('password','');
$notes=pv('notes','');
$notes_public=pv('notes_public','');
$doc_alias=pv('doc_alias','');
$errormsg=array();
if(!$title){
	$errormsg[]="A title is required.";
}
if(!$tag){
	$errormsg[]='Please supply a tag for this record.';
}else{
	if(!preg_match('/^[a-z][a-z0-9_]+$/i',$tag)){
		$errormsg[]='Tags may only contain letters, numbers, and the underscore ("_") character';
	}else if(file_exists('../'.$tag)){
		$errormsg[]='The tag "'.$tag.'" is not available.';
	}else{
		if(($tid=$db->tagExists($tag)) && $tid!=$id){
			$errormsg[]='The tag "'.$tag.'" is not available.';
		}
	}
}
$vendor=max($vendor,0);
$consortium=max($consortium,0);
if($vendor == 0 && $consortium == 0){
	$errormsg[]="A vendor and/or a consortium is required.";
}
$date_signed_approved=implode('-',$_POST['date_signed_approved']);
if(!strtotime($date_signed_approved)){
	$errormsg[]=$date_signed_approved.' does not seem to be a valid date.';
}

if($id<=0){
	if($db->licenseExistsWithTitle($title)){
		$errormsg[]="There is already a license record for this title/vendor/consortium combination.";
	}
}
if($errormsg){
	$msg='<p>There are problems with your submission.<ul><li>';
	$msg.=implode('</li><li>',$errormsg);
	$msg.='</li></ul>';
	include('index.php');
	exit();
}

$cols=compact(
		'title',
		'tag',
		'vendor',
		'consortium',
		'e_reserves',
		'course_pack',
		'handouts',
		'durable_url',
		'alumni_access',
		'sherpa_romeo',
		'ill_print',
		'ill_electronic',
		'ill_ariel',
		'walk_in',
		'research_private_study',
		'blackboard',
		'perpetual_access',
		'perpetual_access_note',
		'images',
		'password',
		'notes',
		'notes_public',
		'date_signed_approved',
		'doc_alias'
);
$id=$db->updateRecord($id,$cols);
$_GET['id']=$id;

if($_FILES && $_FILES['licensedoc']){
    //var_export($_FILES);
	//die('file!');
	$db->setLicenseDoc($id,$_FILES['licensedoc']);
}

if(!$msg)$msg='Accepted! <input type="button" value="Clear form" onclick="return clearform();" />';
include('index.php');