<?php

class Admin_model extends CI_Model {
//update  `Entity` set EffectiveDate = replace(EffectiveDate, ',', '||')
    var $title   = '';
    var $content = '';
    var $date    = '';
    var $DocName    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_document_ref($DocName)
    {
		$this->db->select();
		$this->db->from('DocUploaded');    
		$this->db->where('title', trim($DocName));   
        	$query = $this->db->get();
	        return $query->result_array();
    }
    
    function get_document_entry($DocName)
    {
		$this->db->select('ID');
		$this->db->from('DocUploaded');    
		$this->db->where('title', trim($DocName));   
        	$query = $this->db->get();

		if ($query->num_rows() > 0)
		{
		    $row = $query->row(); 
		    return $row->ID;
		}
		return null;
    }

	function get_document_details($DocID)
    {
		        	
		$this->db->select();
		$this->db->from('DocUploaded');
		$this->db->where('ID ', $DocID);
	        $query = $this->db->get();
	        return $query->result_array();
	      
    }
    

    function insert_document($DocName, $DocType=1)
    {
    
    	$data = array (
    		'title' => trim($DocName),
    		'doc_id' => date("Ymd") .'-'.$DocName ,
    		'DocTypeID' => $DocType,
    		'data_table' => trim($DocName)
    	);
        $this->db->insert('DocUploaded', $data);
        return $this->db->insert_id();
    }
    
    function update_document($data)
    {
	$this->db->where('DocID', $data['DocID']);        
        $this->db->update('DocUploaded', $data);
    }
   
    function insert_entity_root($data)
    {
	
		$this->db->select('ID');
		$this->db->from('Entity');
	    	$this->db->where('Name', $data['Name']);
	    	//$this->db->where('UniqueInfo', $data['UniqueInfo']);
	   	$Entity = $this->db->get();
	 	
	    	if ($Entity->num_rows() > 0){

	    	 	$row = $Entity->row();
	    	 	$this->db->where('ID', $row->ID);
			$this->db->set('DocID', "CONCAT(DocID,'".$data['DocID']."',',')", FALSE);
	    		$this->db->set('EntityMap', "CONCAT(EntityMap,',')", FALSE);
	    		$this->db->update('Entity');
	    		return  $row->ID;
	    		
	    	 } else {

			$data['DocID'] = $data['DocID'] . ",";
	    		$this->db->insert('Entity', $data);
	    		$rowID = $this->db->insert_id();
	    		
	    		return $rowID;
	   	} 	
		    	
    }
    
   function insert_entity($data,$rootID)
    {
    	//Benjamin,22
    	
    	$this->db->select();
	$this->db->from('Entity');
    	$this->db->where('ID', $rootID);
   	$EntityRoot = $this->db->get();

    	$EntityRow = $EntityRoot->row();
   	$ItemArray = explode(',',$EntityRow->DocID);
	$key = array_search($data['DocID'], $ItemArray);

	//$EntityMapArray = explode(',',$EntityRow->EntityMap);
    	
    	
	
		
		$this->db->select('ID');
		$this->db->from('Entity');
	    	$this->db->where('Name', $data['Name']);
	    	$this->db->where('UniqueInfo', $data['UniqueInfo']);
	   	$Entity = $this->db->get();
	 
	    	if ($Entity->num_rows() > 0){
	    	
	    	 	$row = $Entity->row();
	    	 	$data['DocID'] = $data['DocID'] . ",";
		    	$data['EntityMap'] =  $rootID.",";
		    	
		    	$this->db->where('ID', $row->ID);
	    		$this->db->update('Entity',$data);
	    		$rowID = $row->ID;
	    		
	    		$EntityMapArray[$key] = $EntityMapArray[$key] . $rowID .'||';
	    		
	    		$this->db->where('ID', $rootID);
	    		$this->db->set('EntityMap', implode(',',$EntityMapArray));
	    		$this->db->update('Entity');
	    		
	    		return $rowID;
	    		
	    	 } else {
			$data['DocID'] = $data['DocID'] . ",";
	    		$data['DocTypeID'] = $data['DocTypeID'] . ",";
		    	$data['EntityMap'] =  $rootID.",";
	    		$this->db->insert('Entity',$data);
	    		$rowID = $this->db->insert_id();
	    		
	    		$EntityMapArray[$key] = $EntityMapArray[$key] . $rowID .'||';
	    		
	    		$this->db->where('ID', $rootID);
	    		$this->db->set('EntityMap', implode(',',$EntityMapArray));
	    		$this->db->update('Entity');
	    		
	    		return $rowID;
	    	}
    	
    }
    
    
    function get_gazID($gazID)
    {
		$this->db->select();
		$this->db->from('DocUploaded');    
		$this->db->where('title', trim($gazID));   
        	$query = $this->db->get();

		if ($query->num_rows() > 0)
		{
		    	$row = $query->row();
		    	$this->db->select();
			$this->db->from('Entity');    
			$this->db->like('DocID ' ,  ','. $row->ID .',');
			$this->db->or_like('DocID ' ,  $row->ID.',' , 'after');
			$this->db->order_by("ID", "desc"); 
			$query1 = $this->db->get();
			
		    return $query1->result_array();
		}

		return null;
    }

    
    function get_entity($ID)
    {
		        	
		$this->db->select();
		$this->db->from('Entity');
		$this->db->where('ID ', $ID);
	        $query = $this->db->get();
	        return $query->result_array();
	      
    }
    
    
    function update_entity($data)
    {
		$this->db->where('ID', $data['ID']);        
        $this->db->update('Entity', $data);
    }
    
    
    function get_entries($tag, $var)
    {
		$this->db->select();
		$this->db->from($tag);
		$this->db->like($tag,$var);  
		$this->db->limit(6);   
		//if($this->db->count_all_results()>0){  
	        $query = $this->db->get();
	        return $query->result_array();
	      //} else {return '';}
    }
    
    
    function search_entry($STerm)
    {
		$this->db->select();
		$this->db->from('Entity');
		$this->db->like('Name', $STerm);
		$this->db->where('Merged',0);
		$this->db->limit(25);
		$this->db->order_by("Name", "desc");
        	$query = $this->db->get();
        	return $query->result_array();
    }
    
    function merge_entity($MID,$ID){
		
		$this->db->select();
		$this->db->from('Entity');
		$this->db->where('ID ', $MID);
	        $EntityRow = $this->db->get();

		$Entity = $EntityRow->row();

		$this->db->set('DocID', "CONCAT(DocID,'".$Entity->DocID."')", FALSE);
	    	/*$this->db->set('Appointer', "CONCAT(Appointer,',".$Entity->Appointer."')", FALSE);
	    	//$this->db->set('EffectiveDate', "CONCAT(EffectiveDate,'".$Entity->EffectiveDate."')", FALSE);
	    	//$this->db->set('EntityPosition', "CONCAT(EntityPosition,'".$Entity->EntityPosition."')", FALSE);
	    	//$this->db->set('Verb', "CONCAT(Verb,'".$Entity->Verb."')", FALSE);*/
	    	$this->db->set('DocTypeID', "CONCAT(DocTypeID,'".$Entity->DocTypeID."')", FALSE);
	    	$this->db->set('EntityMap', "CONCAT(EntityMap,',".$Entity->EntityMap."')", FALSE);
	    		
		$this->db->where('ID', $ID);
	    	$this->db->update('Entity');
	    	
	    /*--------reference to new field id */
	    	
	    	$this->db->query("UPDATE Entity SET EntityMap = REPLACE(EntityMap, ',".$MID.",' , ',".$ID.",'), EntityMap = REPLACE(EntityMap, ',".$MID."||' , ',".$ID."||') ,EntityMap = REPLACE(EntityMap, '||".$MID."||' , '||".$ID."||'), EntityMap = REPLACE(EntityMap, '".$MID."||' , '".$ID."||'), EntityMap = REPLACE(EntityMap, '".$MID.",' , '".$ID.",')"); 
	    	$this->db->query("UPDATE Entity SET EntityMap = REPLACE(EntityMap, ',,' , ',')"); 
	      /*--------delete to old row id */
	    	
	    	$this->db->query("UPDATE Entity SET Merged = 1, MergedTo= $ID WHERE ID = $MID"); 
    }

	
   function reference_entity($MID,$ID){
		
		$this->db->select();
		$this->db->from('Entity');
		$this->db->where('ID ', $MID);
	        $EntityRow = $this->db->get();

		$Entity = $EntityRow->result_array();
		
		$docs = explode(',', $Entity[0]['DocID']);
		
		for($i=0;$i<count($docs); $i++){
		  if ($docs[$i] != ""){
	      		$this->db->select();
			$this->db->from('DocUploaded');
			$this->db->where('ID ', $docs[$i]);
			$DocRow = $this->db->get();
			$Doc = $DocRow->result_array();
			//var_dump($Doc);
			$dtable = $Doc[0]['data_table'];
			
			if ($dtable != ""){
			  $viwanja = $this->db->field_data($dtable);
			 // var_dump($viwanja);
			  foreach ($viwanja as $kiwanja) {
			    $fld = $kiwanja->name.'_E_';
			    if($this->db->field_exists($fld, $dtable)){
			   //  $fieldname = $flds[$j].'_E_';
			     $this->db->query("UPDATE $dtable SET $fld = $ID WHERE $fld = $MID");	
			    }

			  }
			}
		  }
	      	}
    }
    
   function get_verbs()
    {
		        	
		$this->db->select();
		$this->db->from('Verbs');
		$this->db->where('Viewed', 1);
	        $query = $this->db->get();
	        return $query->result_array();
	      
    }
    
     function get_sys_tables()
    {
		        	
		$this->db->select();
		$this->db->from('SysTables');
		$this->db->where('Viewed', '1');
	        $query = $this->db->get();
	        return $query->result_array();
	
    }
    
    function get_tables()
    {
		return  $this->db->list_tables();
	      
    }
    
    function field_iko($fild, $tab) 
    {
    		return $this->db->field_exists($fild, $tab);
    		 
    }
    
    function get_fields($tab)
    {
		return  $this->db->field_data($tab);
	      
    }
    

    function get_doctype()
    {        	
		$this->db->select('ID,DocTypeName');
		$this->db->from('DocumentType');
		$this->db->where('Viewed', '1');
	        $query = $this->db->get();
	        return $query->result_array();
    }
     
    function fieldcheck($fil,$tab)
    {
    	$fieldname = $fil .'_E_';
		if(!$this->db->field_exists($fieldname,$tab)){
			$this->db->query("ALTER TABLE $tab  ADD COLUMN `$fieldname` VARCHAR(250) NOT NULL DEFAULT  '0,'");	
		}
    }
    
    function create_table($tbl, $flds)
    {	
    	$this->db->query("DROP TABLE IF EXISTS NewTable");  
    	return $this->db->query("CREATE TABLE $tbl ($flds)");    
    }
    
    function alter_table($fild,$tbl)
    {
	if(!$this->db->field_exists($fild,$tbl)){
		$this->db->query("ALTER TABLE $tbl  ADD COLUMN `$fild`  VARCHAR(250)");	
	}
	      
    }
    
    function table_name_change($orig_name,$new_name)
    {
	$results = $this->db->query("SHOW TABLES LIKE '$orig_name'"); 
//echo $results->num_rows; exit;
		if($results->num_rows()!=0)
		{
		 $this->db->query("ALTER TABLE $orig_name RENAME $new_name;");	
		}
    }
    
    
    function populate_table($query)
    {

		return $this->db->query($query);
    }
 
    function dataset_edit($tbl, $rep)
    {        	
		$this->db->where('data_table', $tbl);
	    $this->db->set('representation', $rep, FALSE);
	    $this->db->update('DocUploaded');

    }
    

    function extract_entity($fild,$tab,$docid, $UID, $DocTypeID,$CountryID)
    {
    
    $fieldname = $fild .'_E_';
   // echo "SELECT * FROM $tab WHERE `$fieldname` LIKE '0,'"; exit;
    $query = $this->db->query("SELECT DISTINCT * FROM $tab WHERE `$fieldname` = '0,'");  
	//$this->db->select();
	//$this->db->distinct();
	//$this->db->from($tab); 
	//$this->db->where($fieldname,);
	//$query = $this->db->get();
	$k=0;
	
		if ($query->num_rows() > 0)
		{
		$q=$query->result_array();
//var_dump($q); exit;
		   for($i=0;$i<sizeof($q); $i++)
		   {
			//echo $q[$i][$fild]; exit;   
		  	$EID = '';
				foreach(explode(',',$q[$i][$fild]) as $entity) {
		    		if (trim($entity) != '') {
			    	$this->db->select();
					$this->db->from('Entity'); 
					$this->db->where('Name', trim($entity));
					$this->db->where('Merged', 0);
					$this->db->limit(1);
					$query = $this->db->get();
						if ($query->num_rows() > 0)	{
						$Entity = $query->result_array();
						$EID .=	$Entity[0]['ID'] .',';
						//$docs = explode(',', $Entity[0]['DocID'];
						$docid = $Entity[0]['DocID'] . $docid;
						$docids= $this->clean_array(explode(',', $docid));
						
						$doctypeid = $Entity[0]['DocTypeID'] . $DocTypeID;
						$doctypeids= $this->clean_array(explode(',', $doctypeid));
						
						$countryid = $Entity[0]['CountryID'] . $CountryID;
						$countryids= $this->clean_array(explode(',', $countryid));
						
								$this->db->set('DocTypeID', implode(',', $doctypeids) .',');
								$this->db->set('DocID', implode(',', $docids) .',');
								$this->db->set('CountryID', implode(',', $countryids) .',');
								$this->db->set('CleanEntity', '1');
								$this->db->where('ID', $Entity[0]['ID']);
								$this->db->update('Entity');
						} else {
						
						$data['DocID'] =  $docid .",";
						$data['DocTypeID'] =  $DocTypeID .",";
						$data['CountryID'] =  $CountryID .",";
						$data['CleanEntity'] = '1';
						$data['Name'] =  trim($entity);
							$this->db->insert('Entity', $data);
							$EID .= $this->db->insert_id() .',';
						}
		    		}
		    		$k++;
		    	}
		    	
		    	 $this->db->query("UPDATE $tab SET `$fieldname` = CONCAT(`". $fieldname."`,'". $EID ."') WHERE id =". $q[$i]['id'] ); 
		    	//exit;
		    }
		  
	    /*
	      
	     // echo($entity[2][$fild]); exit;
		$data['DocID'] = $docid . ",";
		$data['Name'] = $entity[$i][$fild];
	    //	$data['Verb'] = $verb ."||";
		$data['EntityTypeID'] = 21;
		$data['UserID'] = $UID;
		$data['DocTypeID'] = $DocTypeID .',';
		
	    	$this->db->insert('Entity',$data);
	    	$rowID = $this->db->insert_id();

	    	$dta = array($fieldname => $rowID);
	    //	$this->db->set($fieldname, 11);
	    	$this->db->where($fild, $entity[$i][$fild]);
	    	$this->db->update($tab, $dta);
	      $k++;
	      } */
		}

	return $k;
	      
    }
    
    
    function clean_array($arr){
	//var_dump($arr);
	$new_arr = array();
		if(is_array($arr)){
			$arr = array_unique($arr);
			$arr = array_filter($arr);
			
			 foreach ($arr as $val){
			 	$new_arr[] = $val;
			 }	
		}
		return $new_arr;
	}
    
}
