<?php
include('../db.inc.php');
if(!isset($_GET['tag']) && !isset($_GET['id'])){
	include('./browse.php');
	exit();
}
//$db->debug=true;
$data=false;
if(isset($_GET['tag'])){
	$tag_or_id=$_GET['tag'];
}
if(isset($_GET['id'])){
	$tag_or_id=$_GET['id'];
}
$data=$db->getLicenseData($tag_or_id);
if(!$data){
	$msg="No license found for '$tag_or_id'.";
	include('browse.php');
	exit();
}
extract($data);
$title=htmlspecialchars($title);
$e_reserves=$e_reserves?'Yes':'No';
$course_pack=$course_pack?'Yes':'No';
$durable_url=$durable_url?'Yes':'No';
$ill_print=$ill_print?'Yes':'No';
$ill_electronic=$ill_electronic?'Yes':'No';
$ill_ariel=$ill_ariel?'Yes':'No';
$walk_in=$walk_in?'Yes':'No';
$alumni_access=$alumni_access?'Yes':'No';
$sherpa_romeo=$sherpa_romeo?'<tr><th class="heading">SHERPA/RoMEO</th><td colspan="2"><a href="'.$sherpa_romeo.'" target="_blank">Link to publisher copyright policies</a></td></tr>':'';
$perpetual_access=$perpetual_access?'Yes':'No';
$perpetual_access_note=htmlspecialchars($perpetual_access_note);
$password=$password?'Yes':'No';
if($notes_public){
	$notes_public='<tr><th colspan="4" class="heading">Public Notes</th>
</tr><tr><td colspan="4">'.nl2br(htmlspecialchars($notes_public)).'</td></tr>';
}
if($notes){
	$notes='<tr><th colspan="4" class="heading">Staff Notes</th>
</tr><tr><td colspan="4">'.nl2br(htmlspecialchars($notes)).'</td></tr>';
}
if($vendorName){
	$vendor='<tr><th>Vendor</th><td colspan="3">'.$vendorName.'</td></tr>';
}else{
	$vendor='';
}
if($consortium){
	$consortium='<tr><th>Consortium</th><td colspan="3">'.$consortiumName.'</td></tr>';
}else{
	$consortium='';
}
$fields=array(
    'e_reserves'=>'E-reserves',
    'course_pack'=>'Course Pack',
    'durable_url'=>'Durable URL',
    'ill_print'=>'ILL Print',
    'ill_electronic'=>'ILL Electronic',
    'ill_ariel'=>'ILL Ariel',
    'walk_in'=>'Walk-In',
    'alumni_access'=>'Alumni Access',
    'perpetual_access'=>'Perpetual Access',
    'password_required'=>'Password'
);
$boilerplate=array();
foreach($fields as $field=>$nice){
    $boilerplate[$field]['short']=$db->getHTML($field.'-short');
    $boilerplate[$field]['long']=$db->getHTML($field.'-long');
}
$moreinsert='<script type="text/javascript" src="/scripts/staff.js"></script>';
include('../header.inc.php');
echo <<<END
<div class="optionalWrapper staff small fullpage">
<a href="index.php">&laquo; Back to browse</a>
<h1>$title - <strong>Staff View</strong></h1>
	<div class="license-wrapper">
		<h3>License Terms of Use</h3>
		<h5>For Library Users</h5>
		<table class="license-table">
			<tr>
				<th class="case">e-Reserves</th>
				<td class="usage $e_reserves">$e_reserves</td>
				<td class="definition">
                    {$boilerplate['e_reserves']['long']}
                </td>
			</tr>
			<tr>
				<th class="case">Course Pack</th>
				<td class="usage $course_pack">$course_pack</td>
				<td class="definition">
                    {$boilerplate['course_pack']['long']}
                </td>
			</tr>
			<tr>
				<th class="case">Durable URL</th>
				<td class="usage $durable_url">$durable_url</td>
				<td class="definition">
                    {$boilerplate['durable_url']['long']}
                </td>
			</tr>
		</table>
		<h5>Interlibrary Loan</h5>
		<table class="license-table">
			<tr>
				<th class="case">ILL Print</th>
				<td class="usage $ill_print">$ill_print</td>
				<td class="definition">
                    {$boilerplate['ill_print']['long']}
                </td>
			</tr>
			<tr>
				<th class="case">ILL Electronic</th>
				<td class="usage $ill_electronic">$ill_electronic</td>
				<td class="definition">
                    {$boilerplate['ill_electronic']['long']}
                </td>
			</tr>
			<tr>
				<th class="case">ILL Ariel</th>
				<td class="usage $ill_ariel">$ill_ariel</td>
				<td class="definition">
                    {$boilerplate['ill_ariel']['long']}
                </td>
			</tr>
		</table>
		<h5>Access</h5>
		<table class="license-table">
			<tr>
				<th class="case">Walk-In</th>
				<td class="usage $walk_in">$walk_in</td>
				<td class="definition">
                    {$boilerplate['walk_in']['long']}
                </td>
			</tr>
			<tr>
				<th class="case">Alumni Access</th>
				<td class="usage $alumni_access">$alumni_access</td>
				<td class="definition">
                    {$boilerplate['alumni_access']['long']}
                </td>
			</tr>
			<tr>
				<th class="case">Perpetual Access</th>
				<td class="usage $perpetual_access">$perpetual_access</td>
				<td class="definition">
                    $perpetual_access_note
				</td>
			</tr>
			<tr>
				<th class="case">Password Required</th>
				<td class="usage $password">$password</td>
				<td class="definition">
                    {$boilerplate['password_required']['long']}
                </td>
			</tr>
		$sherpa_romeo 
		$notes
		$notes_public
		</table>
		<hr />
		<h3>License Information</h3>
		<table>
		$vendor
		$consortium
		</table>
	</div>
</div>
END;
include('../footer.inc.php');