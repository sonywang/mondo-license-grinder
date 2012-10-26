<?php
require_once('../db.inc.php');
require_once('../config.php');
$moreinsert='<script type="text/javascript" src="'.BASE_URL.'scripts/staff.js"></script>';
include('../header.inc.php');
?>
<div class="optionalWrapper small fullpage">
<h1>License Information - Staff View</h1>

<!--<p class="nav"><a href="report.php">Generate Report</a></p>-->
	<?php include('../a-z.inc.php'); ?>
	<form id="license-form">
		<div>
			<label for="find">Find license by package title</label>
			<input name="find" id="find" type="text" />	
		</div>
	</form>
<?php
if(isset($_GET['initial'])){
	$initial=$_GET['initial'];
	$res=$db->findByInitial($initial);
	if($res){
		echo '<ul id="byinitial">';
		foreach($res as $row){
			echo '<li><a href="'.BASE_URL.$row['tag'].'/staff">'.$row['title'].'</a></li>';
		}
		echo '</ul>';
	}

}
if(!empty($msg)){
	echo '<div class="message">'.$msg.'</div>';
}
echo '</div>';
include('../footer.inc.php');
