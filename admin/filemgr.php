<?php
$admin='yup';
$moreinsert='';
require_once('../db.inc.php');
include('../header.inc.php');
?>
<div class="optionalWrapper small fullpage">
  <h1>Manage License Documents</h1>
<?php
include('nav.inc.php');
echo '<hr />';
$message='';
if(isset($_POST['delete'])){
	foreach($_POST['delete'] as $alias=>$v){
		$res=$db->deleteLicenseDoc($alias);
		if($res){
			$message="Error deleting document: $res";
		}
	}
}

if(!empty($_FILES)){
	$db->saveLicenseDoc($_FILES['licensedoc']);
}

if($message){
	echo '<p class="error">'.$message.'</p>';
}

$docs=$db->listLicenseDocs();
//var_dump($docs);

echo'
    <form action="/admin/filemgr.php" enctype="multipart/form-data" method="post">';
if($docs){
	echo '
	  <table id="filemgr">
	    <thead>
	  	  <tr>
	  	    <th>Filename</th><th>Link</th><th>Used By / Delete</th>
	  	  </tr>
	  	</thead>
	  	<tbody>';
	  	
	foreach($docs as $row){
		echo '
		  <tr>
		    <td>'.htmlspecialchars($row['filename']).'</td>
		    <td>
		    	<a target="_blank" href="'.BASE_URL.'admin/getdoc.php?'.$row['alias'].'">'
		    		.BASE_URL.'admin/getdoc.php?'.$row['alias']
		    	.'</a>
		    </td>
		    <td>';
		    if(!$row['usedBy']){
		    	echo '
		    	<input type="submit" name="delete['.$row['alias'].']" value="Delete" />';
		    }else{
		    	echo '<ul>';
		    	foreach($row['usedBy'] as $tag=>$titlea){
		    		echo '<li><a href="'.BASE_URL.'admin/?tag='.$tag.'">'.$titlea[0].'</a></li>';
		    	}
		    	echo '</ul>';
		    }
		    echo '
		    </td>
		  </tr>';
	}
	echo '
		</tbody>
	  </table>';
}
echo '
      <hr />
      <p>
        Upload or replace document: 
        <input type="file" name="licensedoc" value="Upload new document" />
        <input type="submit" value="Upload" />
      </p>
    </form>
</div>';
include('../footer.inc.php');
?>