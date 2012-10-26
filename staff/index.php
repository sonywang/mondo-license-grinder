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
$sherpa_romeo=$sherpa_romeo?'<tr><th class="heading">SHERPA/RoMEO</th><td colspan="2"><a href="'.$sherpa_romeo.'" target="_blank">Link to publisher copyright policies</a></td></tr>':'';
$perpetual_access_note=htmlspecialchars($perpetual_access_note);
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
$licenseLink="<tr><th>License Document Link</th><td colspan=\"3\">$docLink</td></tr>";
$fields=array(
    'e_reserves'=>'E-reserves',
    'course_pack'=>'Print Course Packs',
    'durable_url'=>'Durable URL',
    'ill_print'=>'ILL Print',
    'ill_electronic'=>'ILL Electronic',
    'ill_ariel'=>'ILL Ariel',
    'walk_in'=>'Walk-In',
    'research_private_study'=>'Research/Private Study',
    'blackboard'=>'Learning Management Systems (e.g. WebCT/Vista/Blackboard)',
    'fulltext'=>'Full Text',
    'alumni_access'=>'Alumni Access',
    'perpetual_access'=>'Perpetual Access',
    'password_required'=>'Password',
    'handouts'=>'Class Handouts',
    'images'=>'Images'
);
$boilerplate=array();
foreach($fields as $field=>$nice){
    $boilerplate[$field]['Yes']=$db->getHTML($field.'-Yes');
    $boilerplate[$field]['No']=$db->getHTML($field.'-No');
    $boilerplate[$field]['Ask']=$db->getHTML($field.'-Ask');
	$boilerplate[$field]['Not Applicable']='';
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
				<th class="case">Research/Private Study</th>
				<td class="usage $nyaresearch_private_study">$nyaresearch_private_study</td>
				<td class="definition">
                    {$boilerplate['research_private_study'][$nyaresearch_private_study]}
                </td>
			</tr>
			<tr>
				<th class="case">Class Handouts</th>
				<td class="usage $nyahandouts">$nyahandouts</td>
				<td class="definition">
                    {$boilerplate['handouts'][$nyahandouts]}
                </td>
			</tr>
			<tr>
				<th class="case">Print Course Packs</th>
				<td class="usage $nyacourse_pack">$nyacourse_pack</td>
				<td class="definition">
                    {$boilerplate['course_pack'][$nyacourse_pack]}
                </td>
			</tr>
			<tr>
				<th class="case">Learning Management Systems (e.g. WebCT/Vista/Blackboard)</th>
				<td class="usage $nyablackboard">$nyablackboard</td>
				<td class="definition">
                    {$boilerplate['blackboard'][$nyablackboard]}
                </td>
			</tr>
			<tr>
				<th class="case">e-Reserves</th>
				<td class="usage $nyae_reserves">$nyae_reserves</td>
				<td class="definition">
                    {$boilerplate['e_reserves'][$nyae_reserves]}
                </td>
			</tr>
			<tr>
				<th class="case">Images</th>
				<td class="usage $nyaimages">$nyaimages</td>
				<td class="definition">
                    {$boilerplate['images'][$nyaimages]}
				</td>
			</tr>
			<tr>
				<th class="case">Durable URL</th>
				<td class="usage $nyadurable_url">$nyadurable_url</td>
				<td class="definition">
                    {$boilerplate['durable_url'][$nyadurable_url]}
                </td>
			</tr>
			<tr>
				<th class="case">Full Text</th>
				<td class="usage $nyafulltext">$nyafulltext</td>
				<td class="definition">
                    {$boilerplate['fulltext'][$nyafulltext]}
                </td>
			</tr>
			<tr>
				<th class="case">Password Required</th>
				<td class="usage $nyapassword_required">$nyapassword_required</td>
				<td class="definition">
                    {$boilerplate['password_required'][$nyapassword_required]}
                </td>
			</tr>
		</table>
		<h5>Interlibrary Loan</h5>
		<table class="license-table">
			<tr>
				<th class="case">ILL Print</th>
				<td class="usage $nyaill_print">$nyaill_print</td>
				<td class="definition">
                    {$boilerplate['ill_print'][$nyaill_print]}
                </td>
			</tr>
			<tr>
				<th class="case">ILL Electronic</th>
				<td class="usage $nyaill_electronic">$nyaill_electronic</td>
				<td class="definition">
                    {$boilerplate['ill_electronic'][$nyaill_electronic]}
                </td>
			</tr>
			<tr>
				<th class="case">ILL Ariel</th>
				<td class="usage $nyaill_ariel">$nyaill_ariel</td>
				<td class="definition">
                    {$boilerplate['ill_ariel'][$nyaill_ariel]}
                </td>
			</tr>
		</table>
		<h5>Access</h5>
		<table class="license-table">
			<tr>
				<th class="case">Walk-In</th>
				<td class="usage $nyawalk_in">$nyawalk_in</td>
				<td class="definition">
                    {$boilerplate['walk_in'][$nyawalk_in]}
                </td>
			</tr>
			<tr>
				<th class="case">Alumni Access</th>
				<td class="usage $nyaalumni_access">$nyaalumni_access</td>
				<td class="definition">
                    {$boilerplate['alumni_access'][$nyaalumni_access]}
                </td>
			</tr>
			<tr>
				<th class="case">Perpetual Access</th>
				<td class="usage $nyaperpetual_access">$nyaperpetual_access</td>
				<td class="definition">
                    $perpetual_access_note
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
		$licenseLink
		</table>
	</div>
</div>
END;
include('../footer.inc.php');