<?php
$binaries=array(
	'e_reserves'=>'e-Reserves',
	'handouts'=>'Class Handouts',
	'course_pack'=>'Print Course Packs',
	'durable_url'=>'Durable URL',
	'alumni_access'=>'Alumni Access',
	'perpetual_access'=>'Perpetual Access',
	'ill_print'=>'ILL Print',
	'ill_electronic'=>'ILL Electronic',
	'ill_ariel'=>'ILL Ariel',
	'walk_in'=>'Walk In',
    'research_private_study'=>'Research/Private Study',
    'blackboard'=>'LMS',
	'images'=>'Images',
    'fulltext'=>'Full Text Available'
);
include('../db.inc.php');
$vendor=array();
$consortium=array();
foreach(array('vendor'=>'Vendor','consortium'=>'Consortium') as $table => $nice){
	$$table=$db->getAssoc($table);
}

if($_POST){
	$bsql=array();
	foreach($binaries as $k=>$v){
		$pv=$_POST[$k];
		if($pv==1){
			$bsql[]="`$k`=1";
		}else if($pv==2){
			$bsql[]="`$k`=0";
		}else if($pv==3){
			$bsql[]="`$k`=2";
		}else if($pv==5){
			$bsql[]="`$k`=3";
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
    if($_POST['doc_alias']){
        $bsql[]="`doc_alias` = '".$_POST['doc_alias']."'";
    }
                                	
	$bsql=implode(' AND ',$bsql);
	
	if(!$bsql) $bsql=1;
	$sql="
		SELECT 
			`id`,
			`title`,
			`vendor`,
			`consortium`,
            `research_private_study`,
            `handouts`,
			`course_pack`,
            `blackboard`,
			`e_reserves`,
	        `durable_url`,
            `fulltext`,
            `password`,
            `ill_print`,
            `ill_electronic`,
            `ill_ariel`,
            `walk_in`,
            `alumni_access`,
            `images`,
            `perpetual_access`,
            `perpetual_access_note`,
            `notes`,
            `notes_public`,
            `sherpa_romeo`,
            `date_signed_approved`,
			`doc_alias`
		FROM `record`
		WHERE
			$bsql
		ORDER BY `title` ASC
	";
	$stmt=$db->prepare($sql);
	$stmt->execute();
	$res=$stmt->fetchAll(PDO::FETCH_ASSOC);
	$sql="SELECT COUNT(*) FROM `record`";
	$stmt=$db->prepare($sql);
	$stmt->execute();
	$count=$stmt->fetch(PDO::FETCH_NUM);
	$count=$count[0];
	if($res){
		header('Content-type: text/csv');
		header('Content-Disposition: attachment;filename="licensedata.csv"',true);
		foreach($res as $rn=>$row){
			if($rn==0){
?>"Record ID","Title","Vendor","Consortium","Research/Private Study","Handouts","Print Course Packs","LMS","e-Reserves","Durable URL","Full Text","Password","ILL Print","ILL Electronic","ILL Ariel","Walk In","Alumni Access","Images","Perpetual Access","Perpetual Access Note","Notes","Public Notes","Sherpa/RoMEO","Date Signed/Approved","License Document"
<?php
			}
			$id=$row['id'];
			unset($row['id']);
			$out=array($id);
			foreach($row as $k=>$v){
				switch($k){
					case 'vendor':
						if($v && isset($vendor[$v])){
							$out[]=$vendor[$v];
						}else{
							$out[]='';
						}
						break;
					case 'consortium':
						if($v && isset($consortium[$v])){
							$out[]=$consortium[$v];
						}else{
							$out[]='';
						}
						break;
					case 'title':
					case 'password':
					case 'perpetual_access_note':
					case 'notes':
					case 'notes_public':
					case 'sherpa_romeo':
					case 'date_signed_approved':
						$out[]=$v;
						break;
					case 'doc_alias':
						$out[]=$db->getLicenseDocName($v);
						break;
					default:
						if($v==1){
							$out[]= 'Yes';
						}else if ($v==2){
							$out[]='Ask';
						}else if ($v==3){
							$out[]='N/A';
						}
						else $out[]= 'No';
						
				}
			}
			foreach($out as $i=>$o){
				$out[$i]='"'.str_replace('"','""',$o).'"';
			}
			echo implode(',',$out)."\n";
		}
	}else{
		var_export($db->errorInfo());
	}
			
}
