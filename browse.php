<?php
require_once('db.inc.php');
$moreinsert='';
include('header.inc.php');
$findid='find';
?>
<div class="grid small">
<div class="twelve columns">
<h1><a href="/">License Information</a></h1>
<?php
if(!empty($msg)){
    echo '<div class="message">'.$msg.'</div>';
}
if(isset($_GET['initial'])){
	$initial=$_GET['initial'];
	$res=$db->findByInitial($initial);
	if($res){
	 include('a-z.inc.php');?>
		<form id="license-form">
			<div>
				<label for="<?php echo $findid; ?>">Find license by package title</label>
				<input name="find" id="<?php echo $findid; ?>" type="text" />	
			</div>
		</form>
		<hr/>
		<?php
		echo '<ul id="byinitial">';
		foreach($res as $row){
			echo '<li><a href="'.BASE_URL.$row['tag'].'">'.$row['title'].'</a></li>';
		}
		echo '</ul>';
	}

}else{
	$blurb=$db->getHTML('blurb').$db->getHTML('legal');
	if(trim($blurb)){
		echo '
		<div class="center">
		<a href="http://search.library.ubc.ca/#journals" class="large button">Search for Journal Titles and Their Permitted Uses</a>
		</div>
		<div id="blurb">'.$blurb.'</div>
		<h3>Looking for an Electronic Resource License?</h3>
		<p>License permissions can also be searched by the name of an entire <em>collection</em> or <em>package</em> of electronic journals or e-books, for example: Elsevier ScienceDirect. For permissions related to a specific journal article, please use the <a href="http://search.library.ubc.ca/#journals">Journals</a> search instead.</p>
		';
		include('a-z.inc.php');?>
		<form id="license-form">
			<div>
				<label for="<?php echo $findid; ?>">Find license by package title</label>
				<input name="find" id="<?php echo $findid; ?>" type="text" />	
			</div>
		</form>
		<?php
	}
	echo '</div>';
}
?>

</div>
<?php include('footer.inc.php');?>

