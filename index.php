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
$title = htmlspecialchars($title);
$e_reserves = $e_reserves ? 'Yes' : 'No';
$course_pack = $course_pack ? 'Yes' : 'No';
$durable_url = $durable_url ? 'Yes' : 'No';
$ill_print = $ill_print ? 'Yes' : 'No';
$ill_electronic = $ill_electronic ? 'Yes' : 'No';
$ill_ariel = $ill_ariel ? 'Yes' : 'No';
$walk_in = $walk_in ? 'Yes' : 'No';
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
    'course_pack'=>'Course Pack',
    'durable_url'=>'Durable URL',
    'ill_print'=>'ILL Print'
//    'ill_electronic'=>'ILL Electronic',
//    'ill_ariel'=>'ILL Ariel',
//    'walk_in'=>'Walk-In',
//    'alumni_access'=>'Alumni Access',
//    'perpetual_access'=>'Perpetual Access',
//    'password_required'=>'Password'
);
$boilerplate=array();
foreach($fields as $field=>$nice){
	$boilerplate[$field]['short']=$db->getHTML($field.'-short');
	$boilerplate[$field]['long']=$db->getHTML($field.'-long');
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
				<th class="case">Can I put it on e-reserve?</th>
				<td class="usage $e_reserves">$e_reserves</td>
				<td class="definition">
                        {$boilerplate['e_reserves']['short']}
                        <span><a class="more-info">More info</a></span>
					<div class="full-license-info">
                        {$boilerplate['e_reserves']['long']}
                    </div>
                </td>
			</tr>
			<tr>
				<th class="case">Can I put it in a course pack?</th>
				<td class="usage $course_pack">$course_pack</td>
				<td class="definition">
                        {$boilerplate['course_pack']['short']}
                        <span><a class="more-info">More info</a></span>
					<div class="full-license-info">
                        {$boilerplate['e_reserves']['long']}
                    </div>
                </td>
			</tr>
			<tr>
				<th class="case">Can I link to it?</th>
				<td class="usage $durable_url">$durable_url</td>
				<td class="definition">
                        {$boilerplate['durable_url']['short']}
                        <span><a class="more-info">More info</a></span>
					<div class="full-license-info">
                        {$boilerplate['durable_url']['long']}
                    </div>
                </td>
			</tr>
			$sherpa_romeo
		</table>
		<h5>For Libraries</h5>
		<table class="license-table">
			<tr>
				<th class="case">Is ILL allowed?</th>
				<td class="usage $ill_print">$ill_print</td>
				<td class="definition">
                        {$boilerplate['ill_print']['short']}
                        <span><a class="more-info">More info</a></span>
					<div class="full-license-info">
                        {$boilerplate['ill_print']['long']}
                    </div>
                </td>
			</tr>
		$notes_public
		</table>
	</div>
</div>
END;
include('footer.inc.php');