<?php

class Home extends CI_Model {

    var $title   = '';
    var $content = '';
    var $date    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_number_entity_group($var)
    {
    	
		$this->db->select();
		$this->db->from('Entity');
		$this->db->where('CleanEntity',1); 
		$this->db->where('EntityTypeID', $var);
	       // $query = $this->db->get();
	        return $this->db->count_all_results();
	      //} else {return '';}
    }
    
    function get_dataset_count()
    {
    $ar=array();
    	$this->db->select();
	$this->db->from('DocumentType');
	$this->db->where('Viewed', '1');
	$query = $this->db->get();
    	$cats = $query->result_array();
    	//var_dump($cats);
	    for($f=0; $f<count($cats); $f++) {
	    
	    $this->db->select();
		$this->db->from('Entity');
		$this->db->where('CleanEntity',1); 
		$this->db->like('DocTypeID', $cats[$f]['ID'].',');
		$catCount =  $this->db->count_all_results();
		
		$ar[] = array('DocType'=>$cats[$f]['DocTypeName'], 'CatTot' => $catCount, 'DocTypeID' => $cats[$f]['ID']);
	    	
    	    }
    	return $ar;
    }
    
    
    function get_latest_entry()
    {
		$this->db->select();
		$this->db->from('Entity');  
		$this->db->order_by('ID','desc');
		$this->db->where('CleanEntity',1); 
		$this->db->where('Merged', '0'); 
		$this->db->limit(15);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_popular_entry()
    {
		$this->db->select();
		$this->db->from('Entity');
		$this->db->where('CleanEntity',1); 
		$this->db->order_by('MostVisited','desc'); 
		$this->db->limit(15);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_last_ten_entries($Tag,$Var)
    {
		$this->db->select();
		$this->db->from('DocUploaded');  
		$this->db->order_by('date_added','desc');  
		$this->db->limit('DocUploaded');     
        $query = $this->db->get(10,0);
        return $query->result_array();
    }

    function insert_document($data)
    {
        $this->db->insert('DocUploaded', $data);
        return $this->db->insert_id();
    }
    
    function update_document($data)
    {
	 $this->db->where('DocID', $data['DocID']);        
        $this->db->update('DocUploaded', $data);
    }
   
    function insert_entity($tag, $data,$docid)
    {
    	

    	$this->db->where($tag, $data);
    	$this->db->set('DocID', 'CONCAT(DocID,",",'.$docid.')', FALSE);
    	$this->db->update($tag);
    	if ($this->db->affected_rows()==0){
    	 $entity_data = array($tag => $data, 'DocID' => ','.$docid);
    	 $this->db->insert($tag, $entity_data);	
    	}
    	

    	//$entity_data = array($tag => $data, 'DocID' => $docid);
      //  $this->db->insert($tag, $entity_data);
       // return $this->db->insert_id();
    }

    function get_entries($field,$var)
    {
   // var_dump($var); exit;
    	is_array($var) ? $this->db->where_in($field,$var) : $this->db->where($field,$var); 
		$this->db->select();
		$this->db->from('Entity');
		$this->db->where('CleanEntity',1); 
		$this->db->where('Merged', 0);
		//$this->db->limit(10);   
		//if($this->db->count_all_results()>0){  
	        $query = $this->db->get();
	       // $this->db->_error_message(); 
	        return $query->result_array();
	      //} else {return '';}
    }
    
     function get_mapped_entries($var)
    	{
    	is_array($var) ? $this->db->where_in('Entity.ID',$var) : $this->db->where('Entity.ID',$var); 
		$this->db->select('EntityType.EntityTypeID, EntityType.EntityType');
		$this->db->distinct();
		$this->db->from('Entity');
		$this->db->join('EntityType','Entity.EntityTypeID = EntityType.EntityTypeID');
	        $query = $this->db->get();
	        return $query->result_array();

    }
    
    function get_node($nodeid)
    {
    
    	is_array($nodeid) ? $this->db->where_in('ID',$nodeid) : $this->db->where('ID',$nodeid);
    	$this->db->select();
    	
		$this->db->from('Entity'); 
		//if (is_array($nid)){ echo 'true';} else { echo 'false';}
		
		/*if (is_array($nid)){
		$this->db->select();
		$this->db->from('Entity');
			foreach($nid as $nodeid ) {
			$where = "EntityMap like '$nodeid|%' AND Merged = 0";
			$where .= " OR EntityMap like '$nodeid,%' AND Merged = 0";
			$where .= " OR EntityMap like '%,$nodeid,%' AND Merged = 0";
			$where .= " OR EntityMap like '%|$nodeid,%' AND Merged = 0";
			$where .= " OR EntityMap like '%|$nodeid|%' AND Merged = 0";
			$where .= " OR EntityMap like '%,$nodeid|%' AND Merged = 0";
			$where .= " OR EntityMap like '%|$nodeid' AND Merged = 0";
			}	
		$this->db->where($where);
		$query = $this->db->get();
	        return $query->result_array();
		} else {
		
		$this->db->select();
		$this->db->from('Entity');
			$where = "EntityMap like '$nid|%' AND Merged = 0";
			$where .= " OR EntityMap like '$nid,%' AND Merged = 0";
			$where .= " OR EntityMap like '%,$nid,%' AND Merged = 0";
			$where .= " OR EntityMap like '%|$nid,%' AND Merged = 0";
			$where .= " OR EntityMap like '%|$nid|%' AND Merged = 0";
			$where .= " OR EntityMap like '%,$nid|%' AND Merged = 0";
			$where .= " OR EntityMap like '%|$nid' AND Merged = 0";
			echo $where;
		$this->db->where($where);
		$query = $this->db->get();
	        return $query->result_array();
		//}
*/
		$query = $this->db->get();
	        return $query->result_array();
		//if($this->db->count_all_results()>0){  
	        
	      //} else {return '';}
	      
	   //   "SELECT `ID`,EntityMap, Verb FROM `Entity` where `EntityMap`  like '2717,%' or `EntityMap`  like '%,2717,%' or `EntityMap`  like ',2717|%' or `EntityMap`  like '%|2717|%' or `EntityMap`  like  '%|2717' or `EntityMap`  like '2717|%'"
    }

    function get_entry_cont($tag,$entityname, $countryid,$page_num=1, $results_per_page=15,$sortment)
    {
    	if ($page_num < 1)  {  $page_num = 1; }
        
        if ($sortment=="") {$sortment="";} else {$sortment = " AND Name like '$sortment%'";}
        
        if ($countryid ==0) { $cid = ""; } else { $cid = " AND countryid = ". $countryid; }
        
		if ($entityname=="") {
        $result = $this->db->query("SELECT * FROM Entity WHERE CleanEntity=1 AND Merged=0 $cid $sortment ORDER BY Name LIMIT ". ($page_num - 1) * $results_per_page .", $results_per_page");
        } else {
         $result = $this->db->query("SELECT * FROM Entity WHERE $tag LIKE '$entityname%' AND CleanEntity=1 AND Merged=0 $cid $sortment ORDER BY Name LIMIT ". ($page_num - 1) * $results_per_page .", $results_per_page");
        }
	return $query = $result->result_array();
	
    }

    function get_entry_cont2($tag,$entityname,$page_num=1, $results_per_page=15, $sortment)
    {
    	if ($page_num < 1)
        {
            $page_num = 1;
        }
        $result = $this->db->query("SELECT * FROM Entity WHERE $tag like '$entityname,%'  AND Name like '$sortment%' OR $tag like '%,$entityname,%' AND CleanEntity=1 AND Name like '$sortment%'  ORDER BY Name LIMIT ". ($page_num - 1) * $results_per_page .", $results_per_page");
	return $query = $result->result_array();
	
    }
    
    function get_entry_cont3($tag,$entityname,$page_num=1, $results_per_page=15, $sortment)
    {    //    echo($page_num);
    	if ($page_num < 1)
        {
            $page_num = 1;
        }

        $result = $this->db->query("SELECT * FROM Entity WHERE $tag = $entityname AND CleanEntity=1 AND Name like '$sortment%' ORDER BY Name LIMIT ". ($page_num - 1) * $results_per_page .", $results_per_page");
	return $query = $result->result_array();
	
    }


    function get_entry_count($tag,$entityname){
    	$this->db->select();
		$this->db->from('Entity');
		$this->db->where('CleanEntity',1); 
		if ($entityname!=""){
		$this->db->like($tag,$entityname,'after'); 
		}
		$query = $this->db->get();
     		return $query->num_rows();
   }
   
   function get_entry_count_b($tag,$entityname,$tag2, $sortment){
    	$this->db->select();
		$this->db->from('Entity');
		$this->db->where($tag,$entityname);
		$this->db->where('CleanEntity',1); 
		$this->db->like($tag2,$sortment,'after');
		$query = $this->db->get();
     		return $query->num_rows();
   }
    
    function get_entry2($tag,$docid)
    {
		$this->db->select();
		$this->db->from($tag);
		$this->db->where('DocID',$docid);  
		
		$this->db->limit(6);     
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_doc($docid)
    {
    
    	is_array($docid) ? $this->db->where_in('ID',$docid) : $this->db->where('ID', $docid); 
		$this->db->select();
		$this->db->from('DocUploaded');
		//$this->db->where('ID', $docid);
		//if($this->db->count_all_results()>0){  
	        $query = $this->db->get();
	        return $query->result_array();
	      //} else {return '';}
    }
    
    function get_dataset($tbl,$q,$id)
    {
    $ar=array();
    $ra=array();
    $c=0;
    $qid = (int)$id;
    $flds = $this->db->field_data($tbl);
	    foreach($flds as $f ) {
	    	if (substr($f->name,-3,3)=='_E_') {
	   		 $Entity_id_E_[]= $f->name;
	   	 
		   		if ($c==0) {
					$l = "WHERE  `". $f->name . "` LIKE '%,".$qid.",%'"; 
				} else {
					$l .= " OR `". $f->name . "` LIKE '%,".$qid.",%'";  
				}
				$c++;
	  		} 
	  	/*else {
	  	 $Entity_field[]= $f->name;
	  	}*/
	  	$Entity_field[]= $f->name;
    	}
    	//echo $l;
    	$qu = ($q=='*') ? implode(',',$Entity_field) : $q .','. implode(',', $Entity_id_E_) ;
    	$data ="SELECT $qu FROM $tbl $l ";
    	//echo $data; exit;
     $result = $this->db->query( $data );
    // var_dump($result);
     return  $result->result_array();
     
		/*$this->db->select($qu);
		$this->db->from($tbl);
		$this->db->limit(50); 
			foreach($Entity_id as $r){	
				if ($c==0) {
				$this->db->like("$r", "$qid"); 
				} else {
				$this->db->or_like($r, $qid, 'before'); 
				}
				$c++;
			}     
        $query = $this->db->get();
        return $query->result_array();*/
    }
    
    function get_dataset_map($tbl, $id)
    {
    $Entity_id=array();
    $Entity_name= "";//array();
    $c=0;
    $flds = $this->db->field_data($tbl);
	    foreach($flds as $f ) {
	    	if (substr($f->name,-3,3)=='_E_') {
	    	$Entity_name .= "`". $f->name ."`,";
				if ($c==0) {
					$l = "where  `".$f->name."` LIKE '%," . $id . ",%'"; 
				} else {
					$l .= " or `". $f->name. "` LIKE '%," . $id . ",%'";  
				}
				$c++;
	  		}
    	}
    	    
    	$Entity_name = substr($Entity_name,0,-1);
    	  //  echo "SELECT $Entity_name FROM $tbl $l " ; exit;
		$result = $this->db->query("SELECT $Entity_name FROM $tbl $l ");
     	return $query = $result->result_array();
    }
    
    function mostvisited($id)
    {
	$this->db->where('ID', $id);
	$this->db->set('MostVisited' , 'MostVisited+1',FALSE); 
        $this->db->update('Entity');
    }
    
    function get_docType($id)
    {
    
    	//is_array($var) ? $this->db->where_in($field,$var) : $this->db->where($field,$var); 
		$this->db->select();
		$this->db->from('DocumentType');
		$this->db->where('ID', $id);
		//if($this->db->count_all_results()>0){  
	        $query = $this->db->get();
	        return $query->result_array();
	      //} else {return '';}
    }
    
    function get_country($cid)
    {
    
    	is_array($cid) ? $this->db->where_in('CountryID',$cid) : $this->db->where('CountryID',$cid);
    	$this->db->select();
    	
		$this->db->from('Country'); 

		$query = $this->db->get();
	        return $query->result_array();

    }
    
    function disable_entity($id) {
    	$this->db->where('ID', $id);
		$this->db->set('CleanEntity', '0',FALSE); 
        $this->db->update('Entity');
    }
    
    function fieldcheck($fil,$tab)
    {
		return $this->db->field_exists($fil,$tab);
			
    }
}
