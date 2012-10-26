<?php
include('db.inc.php');
if (!isset($_GET['tag'])) {
    include('browse.php');
    exit();
}
$tag = $_GET['tag'];
if (preg_match('~^([^/]*)/admin$~', $tag, $m)) {
    header('Location: ' . BASE_URL . 'admin/?tag=' . urlencode($m[1]));
}
if (preg_match('~^([^/]*)/staff$~', $tag, $m)) {
    header('Location: ' . BASE_URL . 'staff/?tag=' . urlencode($m[1]));
}
if (is_numeric($tag)) {
    $tag = $db->getTagById($tag);
    if($tag){
    	header('Location: ' . BASE_URL . urlencode($tag));
    }else{
    	header('Location: ' . BASE_URL);
    }
    exit();
}
$data=$db->getLicenseData($tag);
if (!$data) {
    $msg = "No license found for '$tag'.";
    include('browse.php');
    exit();
}
extract($data);
$ill_any=max($ill_print,$ill_ariel,$ill_electronic);
$nyaill_any='No';
if($ill_any==1)$nyaill_any='Yes';
if($ill_any==2)$nyaill_any='Ask';
if($ill_any==3)$nyaill_any='Not Applicable';

$title = htmlspecialchars($title);
$sherpa_romeo=$sherpa_romeo?'<tr><th class="heading">SHERPA/RoMEO</th><td colspan="2"><a href="'.$sherpa_romeo.'" target="_blank">Link to publisher copyright policies</a></td></tr>':'';
if ($notes_public) {
    $notes_public = '<tr><th colspan="4" class="heading">Notes</th>
</tr><tr><td colspan="4">' . nl2br(htmlspecialchars($notes_public)) . '</td></tr>';
}
if ($vendorName) {
    $vendor = '<tr><th>Vendor</th><td colspan="3">' . $vendorName . '</td></tr>';
} else {
    $vendor = '';
}
if ($consortiumName) {
    $consortium = '<tr><th>Consortium</th><td colspan="3">' . $consortiumName . '</td></tr>';
} else {
    $consortium = '';
}
$moreinsert = '';
$base_url = BASE_URL;
$fields=array(
    'e_reserves'=>'E-reserves',
    'course_pack'=>'Print Course Pack',
    'handouts'=>'Class Handouts',
    'images'=>'Images',
    'durable_url'=>'Durable URL',
    'ill_any'=>'ILL',
    'research_private_study'=>'Research/Private Study',
    'blackboard'=>'Blackboard'
//    'walk_in'=>'Walk-In',
//    'alumni_access'=>'Alumni Access',
//    'perpetual_access'=>'Perpetual Access',
//    'password_required'=>'Password'
);
$boilerplate=array();
foreach($fields as $field=>$nice){
	$boilerplate[$field]['Yes']=$db->getHTML($field.'-Yes');
	$boilerplate[$field]['No']=$db->getHTML($field.'-No');
	$boilerplate[$field]['Ask']=$db->getHTML($field.'-Ask');
	$boilerplate[$field]['Not Applicable']='';
	$boilerplate[$field]['question']=$db->getHTML($field.'-question');
}
include('header.inc.php');
echo <<<END
<div class="optionalWrapper small fullpage">
	<a href="$base_url">&laquo; Licenses Home</a>
	<h1>$title</h1>
	<div class="license-wrapper">
		<h3>License Terms of Use</h3>
		<h5>For UBC Library users</h5>
		<table class="license-table">
			<tr>
				<th class="case">
					{$boilerplate['research_private_study']['question']}
				</th>
				<td class="usage $nyaresearch_private_study">$nyaresearch_private_study</td>
				<td class="definition">
                        {$boilerplate['research_private_study'][$nyaresearch_private_study]}
                </td>
			</tr>
			<tr>
				<th class="case">
					{$boilerplate['handouts']['question']}
				</th>
				<td class="usage $nyahandouts">$nyahandouts</td>
				<td class="definition">
                        {$boilerplate['handouts'][$nyahandouts]}
                </td>
			</tr>
			<tr>
				<th class="case">
					{$boilerplate['course_pack']['question']}
				</th>
				<td class="usage $nyacourse_pack">$nyacourse_pack</td>
				<td class="definition">
                        {$boilerplate['course_pack'][$nyacourse_pack]}
                </td>
			</tr>
			<tr>
				<th class="case">
					{$boilerplate['blackboard']['question']}
				</th>
				<td class="usage $nyablackboard">$nyablackboard</td>
				<td class="definition">
                        {$boilerplate['blackboard'][$nyablackboard]}
                </td>
			</tr>
			<tr>
				<th class="case">
					{$boilerplate['e_reserves']['question']}
				</th>
				<td class="usage $nyae_reserves">$nyae_reserves</td>
				<td class="definition">
                        {$boilerplate['e_reserves'][$nyae_reserves]}
                </td>
			</tr>
			<tr>
				<th class="case">
					{$boilerplate['images']['question']}
				</th>
				<td class="usage $nyaimages">$nyaimages</td>
				<td class="definition">
                        {$boilerplate['images'][$nyaimages]}
                </td>
			</tr>
			<tr>
				<th class="case">
					{$boilerplate['durable_url']['question']}
				</th>
				<td class="usage $nyadurable_url">$nyadurable_url</td>
				<td class="definition">
                        {$boilerplate['durable_url'][$nyadurable_url]}
                </td>
			</tr>
			$sherpa_romeo
		</table>
		<div class="legal">
END;
echo $db->getHTML('legal');
$sv='<p><a href="/staff/?id='.$data['id'].'">Staff View</a>';
echo <<<END
		</div>
		<h5>For Libraries</h5>
		<table class="license-table">
			<tr>
				<th class="case">
					{$boilerplate['ill_any']['question']}
				</th>
				<td class="usage $nyaill_any">$nyaill_any</td>
				<td class="definition">
                        {$boilerplate['ill_any'][$nyaill_any]}
                </td>
			</tr>
		$notes_public
		</table>
	</div>
	
	<p>If your intended use is not covered here or you have additional questions about license permissions, please contact
	<a href="mailto:lib-license@interchange.ubc.ca">lib-license@interchange.ubc.ca</a>.</p>
	
	$sv
</div>
END;
include('footer.inc.php');