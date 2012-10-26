<?php
$binaries=array(
	'research_private_study'=>'Research/Private Study',
	'course_pack'=>'Print Course Packs',
	'blackboard'=>'Learning Management Systems',
	'e_reserves'=>'E-reserves',
	'durable_url'=>'Durable URL',
	'fulltext'=>'Full Text Available',
	'password'=>'Password',
	'ill_print'=>'ILL Print',
	'ill_electronic'=>'ILL Electronic',
	'ill_ariel'=>'ILL Ariel',
	'walk_in'=>'Walk In',
	'alumni_access'=>'Alumni Access',
	'perpetual_access'=>'Perpetual Access'
);
//require_once('../config.php');
include('../db.inc.php');
$moreinsert='<script type="text/javascript" src="'.BASE_URL.'scripts/staff.js"></script>';
include('../header.inc.php');
?>
<div class="optionalWrapper small fullpage">
<h1>Generate Report</h1>
<?php if(strpos($_SERVER['SCRIPT_FILENAME'],'/staff/' )): ?>
<div class="nav">
	<a href="index.php">Back to Browse</a> 
</div>
<?php else: ?>
<div class="nav">
	<a href="index.php">Add/Edit a License</a> |
	<a href="report.php">Generate Report</a> |
	<a href="managecv.php?v=vendor">Manage Vendors</a> |
	<a href="managecv.php?v=consortium">Manage Consortium</a>
</div>
<?php endif; ?>
<hr />
<form method="post" action="gonk">
<table id="reporttable">
<?php
echo '<tr><th class="heading"></th>';
foreach($binaries as $name=>$pretty){	
	echo '<th class="heading">'.$pretty.'</th>';
}
echo '</tr>';
foreach(array('1'=>'Yes','2'=>'No','3'=>'Ask','4'=>'Don\'t Care') as $k=>$v){
	echo '<tr><th><a class="set-radio" onclick="setAllRadios('.$k.');">'.$v.'</a></th>';
	foreach($binaries as $name=>$pretty){
		echo '<td class="c">';
		if($_POST && $_POST[$name]){
			if($_POST[$name]==$k){
				echo '<input type="radio" name="'.$name.'" value="'.$k.'" checked="checked" />';
			}else{
				echo '<input type="radio" name="'.$name.'" value="'.$k.'" />';
			}
		}else{
			if($k==4){
				echo '<input type="radio" name="'.$name.'" value="'.$k.'" checked="checked" />';
			}else{
				echo '<input type="radio" name="'.$name.'" value="'.$k.'" />';
			}
		}
		echo '</td>';
	}
	echo '</tr>';
}
?>
</table>
<?php
$vendor=array();
$consortium=array();
foreach(array('vendor'=>'Vendor','consortium'=>'Consortium') as $table => $nice){
	$$table=$db->getAssoc($table);
	echo '<label for="'.$table.'" class="desc">'.$nice.'</label> <select name="'.$table.'" id="'.$table.'">';
	echo '<option value="0">--</option>';
	foreach($$table as $id=>$name){
		if($_POST[$table]==$id){
			$s=' selected="selected"';
		}else{
			$s='';
		}
		echo '<option value="'.$id.'"'.$s.'>'.htmlspecialchars($name).'</option>';
	}
	echo '</select>';
}
?>
<div class="button-wrapper">
<input type="submit" value="Filter" onclick="fiddleaction('');" />
<input type="submit" value="Download CSV" onclick="fiddleaction('csv.php');" />
<input type="reset" value="Clear" onclick="return paulJosephStyleClear();" />
</div>
</form>
<hr />
<?php
if($_POST){
	$bsql=array();
	unset($binaries['password']);
	foreach($binaries as $k=>$v){
		$pv=$_POST[$k];
		if($pv==1){
			$bsql[]="`$k`=1";
		}else if($pv==2){
			$bsql[]="`$k`=0";
		}else if($pv==3){
			$bsql[]="`$k`=2";
		}else{
			//eh
		}
	}
	if($_POST['vendor']){
		$bsql[]='`vendor`='.$_POST['vendor'];
	}
	if($_POST['consortium']){
		$bsql[]='`consortium`='.$_POST['consortium'];
	}
	if($_POST['password']=='1'){
		$bsql[]='`password` != \'\'';
	}
	if($_POST['password']=='2'){
		$bsql[]='`password` = \'\' ';
	}
	$bsql=implode(') AND (',$bsql);
	if(!$bsql) $bsql=1;
	$sql="
		SELECT 
            `id`
		FROM `record`
		WHERE
			($bsql)
		ORDER BY `title` ASC
	";
	$res=$db->query($sql);
//var_export($db->errorInfo());
	$res=$res->fetchAll(PDO::FETCH_ASSOC);
	$sql="SELECT COUNT(*) FROM `record`";
	$count=$db->getOneValue($sql);
	if($res){
		echo '<p>'.number_format(count($res)).' of '.$count.' results ('.(round(count($res)*10000/$count)/100).'%)</p>';
		echo '<div style="max-height:768px;overflow:auto">';
		echo '<table class="report-table">';
?>
<tr>
<th class="heading">Title</th>
<th class="heading">Vendor</th>
<th class="heading">Consortium</th>
<th class="heading">Research/Private Study</th>
<th class="heading">Print Course Packs</th>
<th class="heading">LMS</th>
<th class="heading">e-Reserves</th>
<th class="heading">Durable URL</th>
<th class="heading">Full Text</th>
<th class="heading">Password</th>
<th class="heading">ILL Print</th>
<th class="heading">ILL Electronic</th>
<th class="heading">ILL Ariel</th>
<th class="heading">Walk In</th>
<th class="heading">Alumni Access</th>
<th class="heading">Perpetual Access</th>
</tr>
<?php
		foreach($res as $row){
			$id=$row['id'];
			echo '<tr>';
			$data=$db->getLicenseData($id);
			foreach(
				array(
					'title',
					'vendorName',
					'consortiumName',
					'research_private_study',
					'course_pack',
					'blackboard',
					'e_reserves',
					'durable_url',
					'fulltext',
					'password',
					'ill_print',
					'ill_electronic',
					'ill_ariel',
					'walk_in',
					'alumni_access',
					'perpetual_access'
				) as $k){
				
				$v=$data[$k];
				echo '<td>';
				
				switch($k){
					case 'vendorName':
					case 'consortiumName':
					case 'password':
						echo $v;
						break;
					case 'title':
						echo '<a href="index.php?id='.$id.'">'.htmlspecialchars($v).'</a>';
						break;
					default:
						if($v==2){ echo 'Ask';}
						else if($v==1) {echo 'Yes';}
						else{ echo 'No';}
						
				}
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</table></div>';
	}else{
		echo '<p>No results.</p>';
	}
			
}
?>
</div>
<script>
$('tr:odd').css('backgroundColor','#ddd');
</script>
<?php include('../footer.inc.php')?>