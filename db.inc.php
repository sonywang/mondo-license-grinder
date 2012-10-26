<?php
include('config.php');

class LicensePDO extends PDO{
	private $preparedStatements=array();
	
	public function getAssoc($table){
		$stmt=&$this->preparedStatement(
			$table.'All',
			"SELECT `id`,`name` FROM `$table` ORDER BY `name` ASC"
		);
		$stmt->execute();
		if($stmt->rowCount()==0){
			return array();
		}
		$result=array();
		while($data=$stmt->fetch(PDO::FETCH_NUM)){
			$result[$data[0]]=$data[1];
		}
		$stmt->closeCursor();
		return $result;
	}
	
	public function getOneValue($sql){
		$res=$this->query($sql);
		if($res->rowCount()==0) return false;
		while($row=$res->fetch(PDO::FETCH_NUM)){
			return $row[0];
		}
	}
	
	public function tagExists($tagname){
		$stmt=&$this->preparedStatement(
			'tagExists',
			"
				SELECT
					`id`
				FROM
					`record`
				WHERE
					`tag`=:tag
			"
		);
		$stmt->execute(array(':tag'=>$tagname));
		if($stmt->rowCount()==0){
			return false;
		}
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		return $row['id'];
	}
	
	public function getTagById($id){
		$stmt=&$this->preparedStatement(
			'getTagById',
			"
				SELECT
					`tag`
				FROM
					`record`
				WHERE
					`id`=:id
			"
		);
		$stmt->execute(array(':id'=>$id));
		if($stmt->rowCount()==0){
			return false;
		}
		$tag=false;
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$tag=$row['tag']; //obviously there's only one result
			break;
		}
		$stmt->closeCursor();
		return $tag;
	}

	public function deleteRecord($id){
		$sql="
			DELETE FROM `record` WHERE `id`=$id
		";
		$this->exec($sql);
	}
	
	public function licenseExistsWithTitle($title){
		$stmt=$this->preparedStatement(
			'licenseExistsWithTitle',
			"SELECT `id` FROM `record` WHERE `title`=:title"
		);
		$stmt->execute(array(':title'=>$title));
		if($stmt->rowCount() > 0){
			return true;
		}
		return false;
	}
	
	public function getLicenseData($tag_or_id){
		if(is_numeric($tag_or_id)){
			$stmt=&$this->preparedStatement(
				'getLicenseDataById',
				"
			    SELECT *
    			FROM
        			`record`
    			WHERE
        			`id`=:id
        		"
        	);
			$param=':id';
        }else{
			$stmt=&$this->preparedStatement(
				'getLicenseDataByTag',
				"
			    SELECT *
    			FROM
        			`record`
    			WHERE
        			`tag`=:tag
        		"
        	);
			//id
			$param=':tag';
		}
		$stmt->execute(array($param=>$tag_or_id));
		if($stmt->rowCount()==0){
			return false;
		}
		$data=false;
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$data=$row; 
			break;
		}
		$stmt->closeCursor();
		if($data){
			if($data['vendor']){
				$data['vendorName']=$this->getVendorNameById($data['vendor']);
			}else{
				$data['vendorName']='';
			}
			if($data['consortium']){
				$data['consortiumName']=$this->getConsortiumNameById($data['consortium']);
			}else{
				$data['consortiumName']='';
			}
			if($data['doc_alias']){
				$data['docLink']=$this->getLicenseDocLink($data['doc_alias']);
				$data['docName']=$this->getLicenseDocName($data['doc_alias']);
			}else{
				$data['docLink']='No license document';
				$data['docName']='No license document';
			}
			$nya=array('No','Yes','Ask','Not Applicable');
			foreach($data as $k=>$v){
				if(preg_match('/^[0123]$/',$v)){
					$v=(int)$v;
					$data["nya$k"]=$nya[$v];
				}
			}
		}
		if(!empty($data['password'])){
			$data['password_required']=1;
			$data['nyapassword_required']='Yes';
		}else{
			$data['password_required']=0;
			$data['nyapassword_required']='No';
		}
		return $data;
	}
	
	public function updateRecord($id, $data){
		$bind=array();
		$cols=array();
		foreach($data as $k=>$v){
			$bind[$k]=$v;
			$cols[]="`".stripslashes($k)."`=:$k";
		}
		if($id<=0){ // new, so insert
			$sql="INSERT INTO `record` SET "
				.implode(',',$cols);
			$stmt=$this->preparedStatement(
				'insertRecord'.md5($sql),
				$sql
			);
			$stmt->execute($bind);
			$id=$this->lastInsertId();
		}else{
			$sql="UPDATE `record` SET "
				.implode(',',$cols)
				.' WHERE `id`=:id';
			$bind['id']=$id;
			$stmt=$this->preparedStatement(
				'updateRecord'.md5($sql),
				$sql
			);
			$stmt->execute($bind);
		}
		return $id;
	}
	
	public function getHTML($key){
		global $data;
		global $defaults;
		$stmt=$this->preparedStatement(
			'getHTML',
			"SELECT `content` FROM `html` WHERE `tag`=:key"
		);
		$stmt->execute(array(':key'=>$key));
		if($stmt->rowCount()=='0'){
			if(isset($defaults[$key])){
				return $defaults[$key];
			}else{
				return '<p>Missing HTML for key <em><a href="/admin/boilerplate.php#'.$key.'">'.$key.'</a></em>.</p>';
			}
		}
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		$content=$row['content'];
		if(!empty($data['title'])){
			//$data=$this->getLicenseData($_GET['tag']);
			$content=preg_replace(
				'/(mailto:.*?)"/',
				'\1?subject='.rawurlencode("License terms of use inquiry regarding ".$data['title']).'"',
				$content
			);
		}
		return $content;
	}
	
	public function setHTML($key, $html=false){
		$stmt=$this->preparedStatement(
			'delHTML',
			"DELETE FROM `html` WHERE `tag`=:key"
		);
		$stmt->execute(array(':key'=>$key));
		if($html!==false){
			$stmt=$this->preparedStatement(
				'setHTML',
				"INSERT INTO `html` SET `content`=:html, `tag`=:key"
			);
			$stmt->execute(array(':key'=>$key,':html'=>$html));
			return $html;
		}
		return '';
	}


	public function getVendorNameById($id){
		$stmt=&$this->preparedStatement(
			'getVendorName',
			"SELECT `name` FROM `vendor` WHERE `id`=:id"
		);
		$stmt->execute(array(':id'=>$id));
		if($stmt->rowCount()==0){
			return false;
		}
		$data=false;
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$data=$row['name']; 
			break;
		}
		$stmt->closeCursor();
		return $data;
	}
	
	public function getConsortiumNameById($id){
		$stmt=&$this->preparedStatement(
				'getConsortiumName',
				"SELECT `name` FROM `consortium` WHERE `id`=:id"
			);
		$stmt->execute(array(':id'=>$id));
		if($stmt->rowCount()==0){
			return false;
		}
		$data=false;
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$data=$row['name']; 
			break;
		}
		$stmt->closeCursor();
		return $data;
	}

	public function getInitialsForNavigation(){
		$stmt=&$this->preparedStatement(
				'getInitials',
				"SELECT DISTINCT UPPER(SUBSTR(`title`,1,1)) as initial FROM `record` ORDER BY initial"
			);
		$stmt->execute();
		if($stmt->rowCount()==0){
			return false;
		}
		$data=$stmt->fetchAll(PDO::FETCH_COLUMN,0);
		$stmt->closeCursor();
		return $data;
	}
	
	public function findAsYouType($fragment){
		$stmt=&$this->preparedStatement(
				'findAsYouType',
				"SELECT `id` as value, `title` as label FROM `record` WHERE `title` LIKE :like ORDER BY `title` LIMIT 12"
			);
		$stmt->execute(array(':like'=>"%$fragment%"));
		$data=$stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $data;
	}

	public function findByInitial($initial){
		$stmt=$this->preparedStatement(
				'findByInitial',
				"SELECT `tag`,`title` FROM `record` WHERE `title` LIKE :initial order by `title`"
			);
		$stmt->execute(array(':initial'=>"$initial%"));
		$data=$stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $data;
	}
		
	private function preparedStatement($label,$sql){
		if(empty($this->preparedStatements[$label])){
			$this->preparedStatements[$label]=$this->prepare($sql);
		}
		return $this->preparedStatements[$label];
	}

	public function cvUpdate($vocab,$id,$term){
		$term=trim($term);
		if(!$term){
			$stmt=$this->preparedStatement(
				'cvDelete_'.$vocab,
				"DELETE FROM `$vocab` WHERE `id`=:id"
			);
			$stmt->execute(array(':id'=>$id));
			return true;
		}
		if($existing_id=$this->cvTermExists($vocab,$term)){
			return  $existing_id;
		}
		if($id>=0){
			$stmt=$this->preparedStatement(
				'cvUpdate_'.$vocab,
				"UPDATE `$vocab` SET `name`=:name WHERE `id`=:id"
			);
			$stmt->execute(array(':name'=>$term,':id'=>$id));
			return $id;
		}else{
			$stmt=$this->preparedStatement(
				'cvInsert_'.$vocab,
				"INSERT INTO `$vocab` SET `name`=:name"
			);
			$stmt->execute(array(':name'=>$term));
			return $this->lastInsertId();
		}
	}
	
	public function cvTermExists($vocab, $term){
		$stmt=$this->preparedStatement(
			'cvTermExists'.$vocab,
			"SELECT `id` FROM `$vocab` WHERE `name`=:name"
		);
		$stmt->execute(array(':name'=>$term));
		if($stmt){
			$row=$stmt->fetch(PDO::FETCH_ASSOC);
			return $row['id'];
		}
		return false;
	}
	
	public function cvMerge($vocab,$ids=false,$term=''){
		$term=trim($term);
		if(!$term) return false;
		if(!$ids) return false;
		$ids=implode(',',$ids);
		$this->exec("DELETE FROM `$vocab` WHERE `id` IN($ids)");
		$this->cvUpdate($vocab,-1,$term);
		$nid=$this->lastInsertId('id');
		$this->exec("UPDATE `record` SET `$vocab`=$nid WHERE `$vocab` IN($ids)");
		return true;
	}
	
	public function getAliasOfLicenseDoc($filename){
		$sql="SELECT `alias` FROM `doc` WHERE `filename`=:filename";
		$stmt=$this->preparedStatement('gald',$sql);
		$stmt->execute(array('filename'=>$filename));
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		$filename=$row['alias'];
		if(!$filename){
			return '';
		}
		return $row['alias'];
	}
	
	public function getLicenseDocName($alias){
		$sql="SELECT `filename` FROM `doc` WHERE `alias`=:alias";
		$stmt=$this->preparedStatement('gldl',$sql);
		$stmt->execute(array('alias'=>$alias));
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		$filename=$row['filename'];
		if(!$filename){
			return 'No License document';
		}
		return htmlspecialchars($row['filename']);
	}
	
	public function getLicenseDocLink($alias){
		$sql="SELECT `filename` FROM `doc` WHERE `alias`=:alias";
		$stmt=$this->preparedStatement('gldl',$sql);
		$stmt->execute(array('alias'=>$alias));
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		$filename=$row['filename'];
		if(!$filename){
			return 'No License document.';
		}
		return '<a target="_blank" href="/admin/getdoc.php?'.$alias.'">'.htmlspecialchars($row['filename']).'</a>';
	}
	
	public function listLicenseDocs(){
		$sql="
			SELECT
			`filename`,
			`alias`,
			COUNT(`id`) AS usedBy
			FROM `doc` LEFT JOIN `record` ON `doc_alias`=`alias`
			GROUP BY `alias`
			ORDER BY `filename`
		";
		$stmt=$this->preparedStatement('lld',$sql);
		$stmt->execute();
		$res=$stmt->fetchAll(PDO::FETCH_ASSOC);
		$sql="SELECT `tag`,`title` FROM `record` WHERE `doc_alias`=:alias";
		$stmt=$this->preparedStatement('dub',$sql);
		foreach($res as $k=>$row){
			if($row['usedBy']){
				$stmt->execute(array('alias'=>$row['alias']));
				$u=$stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_COLUMN);
				$res[$k]['usedBy']=$u;
			}
		}
		return $res;
	}

	public function saveLicenseDoc($filedata){
		if($filedata['error']) return;
		$filename=$filedata['name'];
        $hash=substr(hash('whirlpool',$filename,true),0,6);
        $chars='bcdfghjkmnpqrstvxzBCDFGHJKLMNPQRSTVXZ23456789';
        $alias='';
        for($i=0;$i<6;$i++){
          $alias.=$chars[ord($hash[$i])%strlen($chars)];
       	}
		$hash=md5($filename.DOCKEY);
		$loc=DOCSTORE.'/'.$hash;
		if(file_exists($loc)){
			@unlink($loc);
		};
		$tmp=$loc.'.tmp';
		move_uploaded_file($filedata['tmp_name'],$tmp);
		$content=file_get_contents($tmp);
		$iv_size=mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv=mcrypt_create_iv($iv_size,MCRYPT_RAND);
		file_put_contents(
			$loc,
			mcrypt_encrypt(MCRYPT_RIJNDAEL_256,DOCKEY,$content,MCRYPT_MODE_ECB,$iv)
		);
		@unlink($tmp);

		$sql="DELETE FROM `doc` WHERE `alias`=:alias";
		$stmt=$this->preparedStatement('ddl',$sql);
		$stmt->execute(array('alias'=>$alias));
		$sql="INSERT INTO `doc` SET `filename`=:filename, `alias`=:alias, `mime`=:mime";
		$stmt=$this->preparedStatement('cdl',$sql);
		$stmt->execute(array('filename'=>$filename,'alias'=>$alias,'mime'=>$filedata['type']));
		//var_export($stmt->errorInfo());
		return;
	}
	
	public function getLicenseDoc($alias){
		$sql="SELECT `filename`,`mime` FROM `doc` WHERE `alias`=:alias";
		$stmt=$this->preparedStatement('gld',$sql);
		$stmt->execute(array('alias'=>$alias));
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		if(!$row) exit('<h1>Error</h1><p>There is no document corresponding to the alias <q>'.htmlspecialchars($alias).'</q></p>');
		$filename=$row['filename'];
		$hash=md5($filename.DOCKEY);
		$loc=DOCSTORE.'/'.$hash;
		$encrypted=file_get_contents($loc);
		$iv_size=mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv=mcrypt_create_iv($iv_size,MCRYPT_RAND);
		$decrypted=mcrypt_decrypt(MCRYPT_RIJNDAEL_256,DOCKEY,$encrypted,MCRYPT_MODE_ECB,$iv);
		header('Content-type: '.$row['mime']);
		header('Content-disposition: inline;filename="'.$row['filename'].'"');
		echo $decrypted;
		exit();
	}

    public function deleteLicenseDoc($alias){
    	$sql="SELECT `tag`, `title` FROM `record` WHERE `doc_alias`=:alias";
		$stmt=$this->preparedStatement('cl',$sql);
		$stmt->execute(array('alias'=>$alias));
		$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
    	if(count($rows)>0){
    		if(count($rows)==1){
    			$s='';
    		}else{
    			$s='s';
    		}
    		$ret="This document is referenced by ".count($rows)." record$s, cannot delete.<br/>Document is referenced by:<ul>";
    		foreach($rows as $row){
    			$ret.='<li><a href="/admin/?tag='.$row['tag'].'">'.$row['title'].'</a></li>';
    		}
    		$ret.='</ul>';
    		return $ret;
    	}
		$sql="SELECT `filename`,`mime` FROM `doc` WHERE `alias`=:alias";
		$stmt=$this->preparedStatement('gld',$sql);
		$stmt->execute(array('alias'=>$alias));
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		if(!$row) return;
		$filename=$row['filename'];
		$hash=md5($filename.DOCKEY);
		$loc=DOCSTORE.'/'.$hash;
		@unlink($loc);
		$sql="DELETE FROM `doc` WHERE `alias`=:alias";
		$stmt=$this->preparedStatement('dld',$sql);
		$stmt->execute(array('alias'=>$alias));
		return;
    }
    	
	public function backupRecordTable(){
		$backup=dirname(__FILE__).'/backups/record-'.date('Ymd-His').'.sql';
		$cmd='/usr/bin/mysqldump -h '.DBHOST.' -u '.DBUSER.' --password='.DBPASS.' '.DBNAME.' record > '.$backup;
		exec($cmd,$out);
		return;
	}
}


try{
	$dsn = DBTYPE.':dbname='.DBNAME.';host='.DBHOST;
	$db = new LicensePDO($dsn,DBUSER,DBPASS);
}catch(PDOException $e){
	die('DB Connect fail: '.$e->getMessage());
}

