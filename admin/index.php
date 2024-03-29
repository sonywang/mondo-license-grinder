<?php 
$admin='yup';
require_once('../db.inc.php');
if(isset($_POST['del'])){
	if(isset($_POST['id'])){
		$did=1*$_POST['id'];
		$db->deleteRecord($did);
		@unlink('../license-docs/'.$did);
	}
	header('Location: index.php');
	exit();
}

$data=false;
if(isset($_GET['id'])){
	$id=$_GET['id'];
	$tag=$db->getTagById($id);
	if($tag){
		header('Location: '.BASE_URL.'admin/?tag='.$tag);
	}else{
		header('Location: '.BASE_URL.'admin/');
	}
	exit();
}else if(isset($_GET['tag'])){
	$tag=$_GET['tag'];
	$data=$db->getLicenseData($tag);
	if(!$data){
		$msg="No record with the tag '$tag' exists.";
		die(var_export($db->errorInfo()));
	}else{
		$id=$data['id'];
	}
}
$moreinsert='<script type="text/javascript" src="'.BASE_URL.'scripts/staff.js"></script>';
include('../header.inc.php');?>
<div class="optionalWrapper small fullpage">
<h1>Add/Edit a License</h1>
<?php include('nav.inc.php'); ?>
<br />Find license by package title: 
<input name="find" id="find" type="text" class="Teresa" />
<?php
include('../a-z.inc.php');
if(!function_exists('pv')){
	function pv($key,$default=false){
	    if(isset($_POST[$key])){
	        return $_POST[$key];
	    }
	    return $default;
	}
}

if(!$data){
	$id=pv('id',-1);
	$date_signed_approved=date('Y-m-d');
	if(pv('date_signed_approved')){
		$date_signed_approved=implode('-',pv('date_signed_approved'));
	}
	$data=array(
		'title'=>pv('title',''),
		'tag'=>pv('tag',''),
		'vendor'=>pv('vendor',0),
		'consortium'=>pv('consortium',0),
		'e_reserves'=>pv('e_reserves',0),
		'course_pack'=>pv('course_pack',0),
		'durable_url'=>pv('durable_url',0),
		'alumni_access'=>pv('alumni_access',0),
		'ill_print'=>pv('ill_print',0),
		'ill_electronic'=>pv('ill_electronic',0),
		'ill_ariel'=>pv('ill_ariel',0),
		'walk_in'=>pv('walk_in',0),
		'handouts'=>pv('handouts',0),
		'images'=>pv('images',0),
		'research_private_study'=>pv('research_private_study',1),
		'blackboard'=>pv('blackboard',1),
		'fulltext'=>pv('walk_in',1),
		'password'=>pv('password',''),
		'perpetual_access'=>pv('perpetual_access',0),
		'perpetual_access_note'=>pv('perpetual_access_note',''),
		'notes'=>pv('notes',''),
		'sherpa_romeo'=>pv('sherpa_romeo',''),
		'notes_public'=>pv('notes_public',''),
		'date_signed_approved'=>$date_signed_approved,
		'doc_alias'=>''
	);
}
if(isset($msg)&&$msg){
	echo '<div class="message">'.$msg.'</div>';
}
foreach($data as $i=>$v){
	$data[$i]=stripslashes($v);
}
//var_export($data);
extract($data);
$title=htmlspecialchars($title);
$tag=htmlspecialchars($tag);
$notes=htmlspecialchars($notes);
$sherpa_romeo=htmlspecialchars($sherpa_romeo);
$notes_public=htmlspecialchars($notes_public);

function select($which,$selected,&$db){
	$r=$db->getAssoc($which);
	$s='
		<select id="sel_'.$which.'" name="'.$which.'">
			<option value="0">--</option>';
	foreach($r as $id=>$name){
		$s.='
			<option 
			value="'.$id.'"
		';
		if($id==$selected){
			$s.='
			selected="selected"
			';
		}
		$s.='>'.$name.'</option>';	
	}
	$s.='
		</select>';
	$s.=' or new '.$which.': <input name="new'.$which.'" id="new_'.$which.'" type="text" />';
	return $s;
}
$vendor=select('vendor',$vendor,$db);
$consortium=select('consortium',$consortium,$db);

function checkbox($which,$state){
    $s='<input type="radio" id="cb_'.$which.'y" value="1" name="'.$which.'" ';
	if($state==1){
		$s.='checked="checked" ';
	}
	$s.='/> <label for="cb_'.$which.'y">Yes</label> ';
    $s.='<input type="radio" id="cb_'.$which.'n" value="0" name="'.$which.'" ';
	if($state==0){
		$s.='checked="checked" ';
	}
	$s.='/> <label for="cb_'.$which.'n">No</label> ';
    $s.='<input type="radio" id="cb_'.$which.'a" value="2" name="'.$which.'" ';
	if($state==2){
		$s.='checked="checked" ';
	}
	$s.='/> <label for="cb_'.$which.'a">Ask</label> ';
    $s.='<input type="radio" id="cb_'.$which.'na" value="3" name="'.$which.'" ';
	if($state==3){
		$s.='checked="checked" ';
	}
	$s.='/> <label for="cb_'.$which.'na">N/A</label> ';
	return $s;
}

$e_reserves=checkbox('e_reserves',$e_reserves);
$course_pack=checkbox('course_pack',$course_pack);
$alumni_access=checkbox('alumni_access',$alumni_access);
$ill_print=checkbox('ill_print',$ill_print);
$ill_electronic=checkbox('ill_electronic',$ill_electronic);
$ill_ariel=checkbox('ill_ariel',$ill_ariel);
if($id==-1){
	$walk_in=checkbox('walk_in',1);
	$durable_url=checkbox('durable_url',1);
	$fulltext=checkbox('fulltext',1);
	$research_private_study=checkbox('research_private_study',1);
	$blackboard=checkbox('blackboard',1);
	$handouts=checkbox('handouts',2);
	$images=checkbox('images',2);
}else{
	$walk_in=checkbox('walk_in',$walk_in);
	$durable_url=checkbox('durable_url',$durable_url);
	$fulltext=checkbox('fulltext',$fulltext);
	$research_private_study=checkbox('research_private_study',$research_private_study);
	$blackboard=checkbox('blackboard',$blackboard);
	$handouts=checkbox('handouts',$handouts);
	$images=checkbox('images',$images);
}
$password=htmlspecialchars($password);
$perpetual_access=checkbox('perpetual_access',$perpetual_access);
$perpetual_access_note=htmlspecialchars($perpetual_access_note);

function selectdate($which,$when=null){
	if(is_null($when)){
		$when=date('Y-m-d');
	}
	list($yyyy,$mm,$dd)=explode('-',$when);
	$s='<select id="date_'.$which.'" name="'.$which.'[0]">';
	for($i=min(date('Y')-30,$yyyy);$i<=date('Y');$i++){
		$s.='<option value="'.$i.'"';
		if($i==$yyyy) $s.=' selected="selected"';
		$s.='>'.$i.'</option>';
	}
	$s.='</select>';
	$s.='<select name="'.$which.'[1]">';
	for($i=1;$i<13;$i++){
		$mon=date('M',strtotime(sprintf("$yyyy-%02d-01",$i)));
		$s.='<option value="'.sprintf('%02d',$i).'"';
		if($i==$mm)$s.=' selected="selected"'; 
		$s.='>'.$mon.'</option>';
	}
	$s.='</select>';
	$s.='<select name="'.$which.'[2]">';
	for($i=1;$i<32;$i++){
		$s.='<option value="'.sprintf('%02d',$i).'"';
		if($i==$dd)$s.=' selected="selected"';
		$s.='>'.sprintf('%02d',$i).'</option>';
	}
	$s.='</select>';
	return $s;
}
if($id==-1){
	$addform='<p><a onclick="$(\'#add-license\').slideToggle();">Add a New License</a></p>';
	$hide='class="hide"';
	$preview='';
	$publink='';
	$stafflink='';
}else{
	$hide='';
	$addform='';
	$preview='<span><strong>Preview:</strong></span> ';
	$publink='<a target="_blank" href="'.BASE_URL.$tag.'">Public View</a> | ';
	$stafflink='<a target="_blank" href="'.BASE_URL.$tag.'/staff">Staff View</a>';
}
$date_signed_approved=selectdate('date_signed_approved',$date_signed_approved);
$licensedocs=$db->listLicenseDocs();
$selectLicenseDoc='
	<select name="doc_alias">
	  <option value="">(None)</option>';
foreach($licensedocs as $doc){
	$selected='';
	if($doc['alias']==$doc_alias) $selected=' selected="selected"';
	$selectLicenseDoc.='
	  <option value="'.$doc['alias'].'"'.$selected.'>'.$doc['filename'].'</option>
	';
}
$selectLicenseDoc.='</select>';
$licenseDocLink='(No license associated with this record)';
if($doc_alias){
	$licenseDocLink='<a href="/admin/getdoc.php?'.$doc_alias.'">'.BASE_URL.'admin/getdoc.php?'.$doc_alias.'</a>';
}
$del='';
if($id>-1)$del='<input type="submit" name="del" value="Delete License" />';
$baseurl=BASE_URL;
echo <<<END
$addform
<hr />
	<form id="add-license" action="update.php" method="post" enctype="multipart/form-data" $hide>
		<fieldset>
		<input type="hidden" name="id" value="$id" />
		<label for="tag" class="desc">License URL</label>
		<label for="tag">$baseurl</label>
		<input name="tag" id="tag" class="field" type="text" size="100" value="$tag" title="short name for use in a URL" />
		<br />
		$preview $publink $stafflink
		</fieldset>
		<fieldset>
			<label for="date_date_signed_approved" class="desc">Date Signed/Approved</label>
			$date_signed_approved
		</fieldset>
		<fieldset>
			<label for="title" class="desc">License Information</label>
			<span style="float:left;"class="fullwidth">
				<input type="text" class="fullwidth field" id="title" name="title" value="$title" />
				<label for="title" class="fullwidth sub">Title</label>
			</span>
			<span style="float:left; width:100%;">
				$vendor
				<label for="sel_vendor" class="fullwidth sub">Vendor</label>
			</span>
			<span style="float:left; width:100%">
				$consortium
				<label for="sel_consortium" class="fullwidth sub">Consortium</label>
			</span>
			<span style="float:left; width:100%">
				<input type="text" name="password" class="fullwidth field" value="$password" />
				<label for="text_pass" class="fullwidth sub">Password</label>
			</span>
		</fieldset>
		<fieldset>
			<label class="desc">Permitted Use</label>
			<table>
			<tr><td>Research/Private Study: </td><td>$research_private_study</td></tr>
			<tr><td>Class Handouts: </td><td>$handouts</td></tr>
			<tr><td>Print Course Packs: </td><td>$course_pack</td></tr>
			<tr><td>Learning Management Systems (e.g. WebCT/Vista/Blackboard): </td><td>$blackboard</td></tr>
			<tr><td>e-Reserves: </td><td>$e_reserves</td></tr>
			<tr><td>Durable URL: </td><td>$durable_url</td></tr>
			<tr><td>Walk-In: </td><td>$walk_in</td></tr>
			<tr><td>Alumni Access: </td><td>$alumni_access</td></tr>
			<tr><td>ILL Print: </td><td>$ill_print</td></tr>
			<tr><td>ILL Electronic: </td><td>$ill_electronic</td></tr>
			<tr><td>ILL Ariel: </td><td>$ill_ariel</td></tr>
			<tr><td>Full Text Available: </td><td>$fulltext</td></tr>
			<tr><td>Images (in classroom materials): </td><td>$images</td></tr>
			<tr><td>Perpetual Access: </td><td>$perpetual_access</td></tr>
			</table>
			<br class="clearing" />
			<label for="text_pa" class="desc">Note on Perpetual Access:</label>
			<textarea name="perpetual_access_note" id="text_pa" class="field">$perpetual_access_note</textarea>
		</fieldset>
		<fieldset>
			<label for="int_sr" class="desc">SHERPA/RoMEO URL</label>
			<input type="text" id="int_sr" name="sherpa_romeo" class="fullwidth field" value="$sherpa_romeo" />
		</fieldset>
		<fieldset>
			<label for="int_notes" class="desc">Internal Notes</label>
			<textarea id="int_notes" name="notes" class="field">$notes</textarea>
		</fieldset>
		<fieldset>
			<label for="ext_notes" class="desc">External Notes</label>
			<textarea name="notes_public" id="ext_notes" class="field">$notes_public</textarea>
		</fieldset>
		<fieldset>
		    <label for="licensedoc" class="desc">License Document</label>
			<p>
				$licenseDocLink<br />
				$selectLicenseDoc
			</p>
		</fieldset>
		<input type="submit" value="Submit" />
		<input type="reset" value="Clear" onclick="return clearform();" />
		$del
	</form>
</div>
END;
?>
<script>
$('tr:odd').css('backgroundColor','#ddd');
</script>
<?php include('../footer.inc.php');
