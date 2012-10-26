<?php
include('../db.inc.php');
$moreinsert='';
include('../header.inc.php');
echo '
<div class="optionalWrapper small fullpage">
<h1>Import License CSV</h1>';
include('nav.inc.php');
if(empty($_FILES)){
?>
<br />
<h3>Note: Do not alter the Record ID, Vendor, or Consortium fields of the CSV file. Do not add or delete columns. </h3>
<form method="post" enctype="multipart/form-data" action="import.php">
<p>Select .csv file to import: <input type="file" name="import" /><input type="submit" value="Import" /></p>
</form>
<?php
}else{
	$db->backupRecordTable();
	$fh=fopen($_FILES['import']['tmp_name'],'r');
	$headers=fgets($fh);

	$fields=array(
		'id',
	    'title',
	    'vendor',
	    'consortium',
	    'research_private_study',
	    'handouts',
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
	    'images',
	    'perpetual_access',
	    'perpetual_access_note',
	    'notes',
	    'notes_public',
	    'sherpa_romeo',
	    'date_signed_approved',
	    'doc_alias'
	);
	if(count($headers) != count($fields)){
		die('<h3>Cannot import this file (number of columns is invalid).</h3>');
	}
	$nya=array('No'=>0,'Yes'=>1,'Ask'=>2,'N/A'=>3);
	$sql="UPDATE `record` SET ";
	$s=array();
	foreach($fields as $field){
	    $s[]="`$field`=:$field";
	}
	$sql.=implode(',',$s).' WHERE `id`=:id';
	//echo $sql;
	//die();
	$stmt=$db->prepare($sql);
	$vsql="SELECT `id`,`name` from `vendor`";
	$vstmt=$db->prepare($vsql);
	$vstmt->execute();
	$vendors=array();
	while($l=$vstmt->fetch(PDO::FETCH_ASSOC)){
	    $vendors[$l['name']]=$l['id'];
	}
	$csql="SELECT `id`,`name` from `consortium`";
	$cstmt=$db->prepare($csql);
	$cstmt->execute();
	$consortia=array();
	while($l=$cstmt->fetch(PDO::FETCH_ASSOC)){
	    $consortia[$l['name']]=$l['id'];
	}
	$binds=array();
	while($line=fgetcsv($fh)){
	    //var_export($line);
    	if(count($fields)!=count($line)){
    		die('<h3>Cannot import this file, the line '.implode(', ',$line).' has an incorrect number of fields.</h3>');
    	}
	    foreach($line as $i=>$v){
        	if(isset($nya[$v])){
	            $line[$i]=$nya[$v];
    	    }
    	}
    	$bind=array_combine($fields,$line);
    	if($bind['vendor']){
    		$bind['vendor']=$vendors[$bind['vendor']];
    	}else{
    		$bind['vendor']=null;
    	}
    	if($bind['consortium']){
    		$bind['consortium']=$consortia[$bind['consortium']];
    	}else{
    		$bind['consortium']=null;
    	}
    	if($bind['doc_alias']){
    		//reverse lookup
    		if($bind['doc_alias']=='No license document'){
    			$bind['doc_alias']=null;
    		}else{
    			$bind['doc_alias']=$db->getAliasOfLicenseDoc($bind['doc_alias']);
    		}
    	}else{
    		$bind['doc_alias']=null;
    	}
//var_export($bind);
		$binds[]=$bind;
	}
	$c=0;
	foreach($binds as $bind){
    	$stmt->execute($bind);
    	if($stmt->errorCode()!='00000'){
        	echo '<h3>ERROR! Please restore database from most recent backup.</h3>';
	        echo '<h4>Please note the following message:</h4>';
	        echo '<pre>';
	        var_export($stmt->errorInfo());
	        echo '</pre>';
	        die();
    	}
/**/
    	//var_export($bind);
    	$c++;
	}
	echo '<p>Imported '.$c.' records.</p>';
}

echo '
</div>';
include('../footer.inc.php');