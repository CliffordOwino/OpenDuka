<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
       public function __construct()
       {
            parent::__construct();
            // Your own constructor code
             $this->load->helper(array('form','url'));
             $this->load->library('form_validation');
             $this->load->library(array('session'));
             $this->load->database();
             $this->load->model(array('user_model','admin_model'));
             
             date_default_timezone_set('Africa/Nairobi');
		if(($this->session->userdata('user_name')==""))
		{
			redirect('/user');
		}
       }
       
	public function index()
	{
	$verbs= $this->verb_words();
	//echo $verbs;
			$data['page_title'] = 'Admin Dashboard';
			$this->load->view('header',$data);
			$this->load->view('admin', array('verb_word'=> $verbs));
			$this->load->view('footer');
	}
	
	public function manage_users(){
	
		$data['page_title'] = 'Manage Users';
		$this->load->view('header_admin', $data);
		$this->load->view('footer', $data);
		
	}
	
	
	function entityAdd_test()
	{
		$data = array('items' => '3', 'submit' => '1', 'errors' => 'Tuko');
		    //echo validation_errors();
		    echo json_encode($data);
	}
		
	function entityAdd()
	{
	//$this->output->enable_profiler(TRUE); 			
		$items=$this->input->post('items');
		$DocName =  str_replace("'","`",$this->input->post('src0'));
		$Appointer = ucwords(strtolower(str_replace("'","`",$this->input->post('appointer0'))));
	//alert($items);
		
		$this->form_validation->set_rules('src0', 'Source file', 'trim|required');
		for ($j=0; $j<$items; $j++) {
			$this->form_validation->set_rules('entity'.$j, 'Entity'.$j, 'trim|required');
			$this->form_validation->set_rules('startdate'.$j, 'Start Date'.$j, 'trim|required');
		}

		if ($this->form_validation->run() == FALSE) {
		
		    $data = array('errors' => validation_errors(), 'items' => $items, 'submit' => '0');
		    //echo validation_errors();
		    echo json_encode($data);
		} else {
				
			
		$DocID = $this->admin_model->get_document_entry($DocName) ? : $this->admin_model->insert_document($DocName);
			//echo $DocID;exit;
			for ($i=0; $i<$items; $i++) {
			//echo $this->input->post('entity'.$i); exit;
				$data = array(
				'DocID' => $DocID,
				'EntityTypeID' => $this->input->post('type'.$i),
				'EntityPosition' => $this->input->post('position'.$i),
				'Name' => ucwords(strtolower(str_replace("'","`",$this->input->post('entity'.$i)))),
				'UniqueInfo' => str_replace("'","`",$this->input->post('address'.$i)),
				'EffectiveDate' => str_replace("'","`",$this->input->post('startdate'.$i)) . ' : ' .  str_replace("'","`",$this->input->post('enddate'.$i)),
				'Verb' => $this->input->post('verb'.$i),				
				'UserID' => $this->session->userdata('user_id')
				);
				/*echo $data;
				echo 'tuko';//exit;*/
				if ($i==0){
					$data['Appointer'] = $Appointer;
					//var_dump($data);exit;
					$rootID = $this->admin_model->insert_entity_root($data);
					//return $rootID; //exit;
				} else {
					if ($i==1){
						$belongto = $this->input->post('belong');
						$rowID = $this->admin_model->insert_entity($data,$rootID);
						if ($belongto=="on") {$rootID = $rowID;}
					} else {
						$this->admin_model->insert_entity($data,$rootID);
					}
				}
			}
		    $data = array('items' => $items, 'submit' => '1', 'errors' => '');
		    //echo validation_errors();
		    echo json_encode($data);
		}
		
    }
    
    function entityEdit()  {
    
    //$this->output->enable_profiler(TRUE); 
    		$gazID=$this->input->post('gazID');
    		
    		$content = $this->admin_model->get_gazID($gazID);
		$list="";
		
    		if (is_array($content)){
    		$list="<form id='EntityUpdate' action='' method='post'><div class='spacer'><div class='select'>Type</div><div class='textfield'>Entity</div><div class='addrfield'>Position</div><div class='addrfield'>Unique Box <br/>'P.O. Box NNN'</div><div class='datefield'>Start:End Date</div><div class='select'>Verb</div></div>";
			for($i=0;$i< count($content);$i++)
			{
			$list .= '<div class="spacer"><select class="select" name="type'.$i.'"><option value="22"';
			
			if ($content[$i]['EntityTypeID']==22){ $list .= "selected";}
			
			$list .= '>Person</option><option value="21" ';
			if ($content[$i]['EntityTypeID']==21){ $list .= "selected";}
			
			$list .= '>Organization</option></select><input type="text" id="entity'.$i.'" name="entity'.$i.'" value="'.$content[$i]['Name'].'"  class="textfield" required /><input type="text" id="position'.$i.'" name="position'.$i.'" value="'.$content[$i]['EntityPosition'].'"  class="addrfield" /><input type="text" id="address'.$i.'" name="address'.$i.'" value="'.$content[$i]['UniqueInfo'].'"  class="addrfield" /><input type="text" id="startdate'.$i.'" name="startdate'.$i.'" value="'.$content[$i]['EffectiveDate'].'" class="datefield"/><select class="select" name="verb'.$i.'"><option selected value="' . $content[$i]['Verb'] . '">' . $content[$i]['Verb'] . '</option>'. $this->verb_words() .'</select><input type="hidden" value="'.$content[$i]['ID'].'" name="ID'.$i.'"/></div>'; 
			
			}		
		$list.='<input type="hidden" value="'.$gazID.'" name="gazID"/><input type="button" class="EntityUpdate" value="Submit" onclick="EntityUpdate()" /></form>';
		}
   		
   		$list= empty($list) ? "Sorry No Data" : $list;
   		echo $list;
    }
    
    function verb_words(){
	$verb = $this->admin_model->get_verbs();
	$v = '';
	//var_dump($verb);
	for ($j=0; $j < sizeof($verb); $j++) {
		$v .= "<option value=" . $verb[$j]['Verb'] . ">" . $verb[$j]['Verb'] ."</option>";
	}
	return $v;
    }
    
    function EntityEditSearch(){
   	$SearchTerm=$this->input->post('STerm');
    	
    	$content = $this->admin_model->search_entry($SearchTerm);
	$list="";
    		if (is_array($content)){
	    		
    			$list="<div class='spacer'><div style='width: 400px;'>Entity</div><div style='width: 200px;'>Unique Box <br/>'P.O. Box NNN'</div></div>";
			for($i=0;$i< count($content);$i++)
			{
			$list .= "<div class='spacer' style='background-color: #cccccc; border:#eee 1px solid;'><a onclick='javascript:EntityUpdate(".$content[$i]['ID'].")' href='#'>Edit</a>";
			
			$list .= "<div style='width: 380px;'>" . $content[$i]['Name'] . "</div></div>"; 
			
			}		
			
		}
   		
   		$list= empty($list) ? "Sorry No Data" : $list;
   		echo $list;
    
    }
    
    function EntityUpdate() {
  //$this->output->enable_profiler(FALSE);  
    	$id=$this->input->post('ID');
    		
    	$content = $this->admin_model->get_entity($id);	
    	//var_dump($content);exit;
    		$list="<form id='EntityUpdateForm' action='' method='post'><div class='spacer'><div class='select'>Type</div><div class='textfield'>Entity</div></div>";
		
			$list .= "<div class='spacer'><select class='select' name='type'><option value='22'";
			
				if ($content[0]['EntityTypeID']==22){ $list .= "selected";}
			
			$list .= ">Person</option><option value='21'";
				if ($content[0]['EntityTypeID']==21){ $list .= "selected";}
			
			$list .= ">Organization</option></select><input type='text' id='entity' name='entity' value='".$content[0]['Name']."'  class='textfield' required /><input type='hidden' value='".$content[0]['ID']."' name='ID'/>"; 
				
			$list.="<input type='button' class='EntityUpdate' value='Submit' onclick='EntityUpdater()'/></div>	</form>";	
		
   		
   		$list = empty($list) ? "Sorry No Data" : $list;
   		echo $list;
    
    }
    
    function EntityUpdater() {
 // $this->output->enable_profiler(TRUE);  
    	
	$data['ID'] = $this->input->post('ID');
	//$data['EntityPosition'] = $this->input->post('position');
	$data['EntityTypeID'] = $this->input->post('type');
	$data['Name'] = trim(str_replace("'","`",$this->input->post('entity')));
	//$data['UniqueInfo'] = trim(str_replace("'","`",$this->input->post('address')));
	//$data['EffectiveDate'] = trim(str_replace("'","`",$this->input->post('startdate')));
	//$data['Verb'] = $this->input->post('verb');
	
	$this->admin_model->update_entity($data);

    }
	
	
    function EntityMergeSearch(){
    //$this->output->enable_profiler(TRUE);
   	$SearchTerm=$this->input->post('STerm');
    	
    	$content = $this->admin_model->search_entry($SearchTerm);
    	//var_dump($content); exit;
	$list="";
    		if (is_array($content)){
	    		
    			$list="<form id='EntityMerge' action='' method='post'><div class='spacer'><div style='width: 400px;'>Entity</div><div style='width: 200px;'>Merge To</div></div>";
			for($i=0;$i< count($content);$i++)
			{
			$list .= "<div class='spacer' style='background-color: #cccccc; border:#eee 1px solid;'><input style='width: 20px;' type='checkbox' name='Merge[]' value='". $content[$i]['ID'] . "'>";
			
			$list .= "<div style='width: 380px;'>" . $content[$i]['Name'] . "</div><div style='width: 200px;'><input style='width: 20px;' type='radio' class='radioEnt' name='radioEnt' value='". $content[$i]['ID'] . "'></div></div>"; 
			
			}		
			$list.='<input type="hidden" value="" name="EntityIDS"/><input type="button" class="EntityUpdate" value="Submit" onclick="EntityMerge()"/></form>';
		}
   		
   		$list= empty($list) ? "Sorry No Data" : $list;
   		echo $list;
    
    }
    
     function EntityMerger(){
   //  $this->output->enable_profiler(TRUE); 
   
   	$valz = $this->input->post('MergeEnt');
     	$RootID = $this->input->post('MergeTo');
   	$MergeIds= explode(',',$valz);
   	if(!in_array($RootID, $MergeIds)){
   	$RootID='';
   	}
    	$j=0;
    	for($i=0; $i<sizeof($MergeIds); $i++){
	    	if($RootID==''){
	    	  $RootID = $MergeIds[$i];
	    	} else {
	    		if ($MergeIds[$i] != $RootID){		
	    	     	  $this->admin_model->merge_entity($MergeIds[$i], $RootID);
	    	     	  $this->admin_model->reference_entity($MergeIds[$i], $RootID);
	    	     	}
    	     	}
    	 ++$j;
    	}
    		
   		//$list= empty($list) ? "Sorry No Data" : $list;
   		echo "<h3>". $j . " Merged</h3>";
    }
    
     function ListDocCat(){
  //   $this->output->enable_profiler(TRUE); 
    	$doctype = $this->admin_model->get_doctype();
    	$list = "<option value='' selected>Select Category</option>";
// var_dump($doctype);
	for($i=0;$i< count($doctype);$i++){		
     	  $list .= "<option value='" . $doctype[$i]['ID'] ."'>" . $doctype[$i]['DocTypeName'] . "</option>";
     	}
    	$list = empty($list) ? "Sorry No Data" : $list;
	echo $list;
    }
    
    function ListTable(){
     //$this->output->enable_profiler(TRUE); 
   	$meza = $this->admin_model->get_tables();
    	if (is_array($meza)){
    	$sys_tables = $this->admin_model->get_sys_tables();
    	
    	for($j=0;$j< count($sys_tables);$j++){ $systable[] = $sys_tables[$j]['TableName'];}
    	
    	
    	$meza = array_merge(array_diff($meza, $systable));
    	//var_dump($meza);
    		$list = "<option value='' selected>Select Table</option>";
		for($i=0;$i< count($meza);$i++){		
    	     	  $list .= "<option value='" . $meza[$i] ."'>" . $meza[$i] . "</option>";
    	     	}
    	}
    		
	$list = empty($list) ? "Sorry No Data" : $list;
	echo $list;
    }
    
    
    
    function ListField(){
     //$this->output->enable_profiler(TRUE); 
     	$stabs = $this->input->post('STab');
     	$dtype = $this->input->post('DocType');
   	$list = "<form id='DatasetInsert' action='' method='post'>";
	
	if ($stabs=='NewTable'){
   	$list .="<div class='spacer'>Document Name <input type='text' value='' name='DocName'/> { e.g. PublicAwardeds}</div>";
   	}
   	
   	$doctype = $this->admin_model->get_doctype();
   	//var_dump($doctype);
   	$list .= "<div class='spacer'>Document Type  <select name='DocumentType'";
   	for($j=0;$j< count($doctype);$j++){
		$list .= "<option value='". $doctype[$j]['ID']."'>". $doctype[$j]['DocTypeName'] ."</option>";
	}
   	$list .= "</select></div>";
   	$list .= "<div class='spacer'></div>";
   	$list .= "<div class='spacer'><div style='width: 300px;'>Select field to Extract Entity</div></div>";
   	$viwanja = $this->admin_model->get_fields($stabs);
    	foreach ($viwanja as $kiwanja) {
		if ($kiwanja->type == "text" || $kiwanja->type == "varchar") {
		   $iko = ($this->admin_model->field_iko($kiwanja->name.'_E_', $stabs)==1) ? "checked" : null;
			$list .= "<div class='spacer parent'><input style='width: 20px;' class='selectfield' type='checkbox' name='Extract[]' ".$iko." value='".$kiwanja->name."'>";
	   		$list .= $kiwanja->name ."  <div class='selectverb'></div></div>";
	   	}
	}	
	//  echo $kiwanja->type;
	//  echo $kiwanja->max_length;
	//  echo $kiwanja->primary_key;
	 $list .= '<div class="spacer">
	 <input type="button" class="EntityExtract" value="Submit" onclick="EntityExtract()"/></div>';
	// $list .= '<div id="verbs" style="visibility:hidden;"><select name="Verb[]"><option value="" selected>No verb</option> '. $this->verb_words() .'</select></div>';
	 $list .= '</form>';
	
	$list = empty($list) ? "Sorry No Data" : $list;
	
	echo $list;
    }
    
    function ListFieldEdit(){
     //$this->output->enable_profiler(TRUE); 
     	$stabs = $this->input->post('STab');
	$doc_info = $this->admin_model->get_document_ref($stabs);
	$representation = explode(",",(str_replace(" ","",(str_replace("`","",$doc_info[0]['representation'])))));
//echo($stabs);
	//var_dump($doc_info); exit;
   	$list = "<form id='DatasetEntityEditForm' action='' method='post'>";
   	//$list .="<div class='spacer'>Document Name <input type='text' value='' name='DocName'/> {2007_PublicAwardedTenders}</div>";
   	
   	$list .= "<div class='spacer'><div style='width: 300px;'>Select field to show Entity</div></div>";
   	$viwanja = $this->admin_model->get_fields($stabs);
    	foreach ($viwanja as $kiwanja) {
		if (substr($kiwanja->name, -3) != '_E_') {
			$name = $kiwanja->name;
			$list .= "<div class='spacer parent'>";
			if (in_array($name, $representation)) {
			$list .= "<input style='width: 20px;' type='checkbox' name='DataEntityField[]' class='fild' checked value='".$kiwanja->name."'>";
			} else {
			$list .= "<input style='width: 20px;' type='checkbox' name='DataEntityField[]' class='fild' value='".$kiwanja->name."'>";			
			}
	   		$list .= $kiwanja->name ."</div>";
	   	}
	}	
	
	 $list .= '<div class="spacer">
			 <input type="hidden" value="'. $stabs .'" name="Entitytablename"/>
			 <input type="button" class="DatasetEditBT" value="Submit" id="DatasetEditBT"/>
		   </div>';
	 $list .= '</form>';
	
	$list = empty($list) ? "Sorry No Data" : $list;
	
	echo $list;
    }
    
    
    function ListFieldEntityEdit(){
     //$this->output->enable_profiler(TRUE); 
    $stabs = $this->input->post('STab');
	$doc_info = $this->admin_model->get_document_ref($stabs);
	//$representation = explode(",",(str_replace(" ","",(str_replace("`","",$doc_info[0]['representation'])))));
//echo($stabs);
	//var_dump($doc_info); exit;
   	$list = "<form id='DatasetEditForm' action='' method='post'>";
   	//$list .="<div class='spacer'>Document Name <input type='text' value='' name='DocName'/> {2007_PublicAwardedTenders}</div>";
   	
   	$list .= "<div class='spacer'><div style='width: 300px;'>Select at least 2 fields to extract Entity from. Please note that the content in the field will be taken as array having a comma as a delimeter.</div></div>";
   	$viwanja = $this->admin_model->get_fields($stabs);
   	
   	foreach ($viwanja as $kiwanja) {$name[] = $kiwanja->name;}
   	
   	//var_dump($name);
    	foreach ($viwanja as $kiwanja) {
		if (substr($kiwanja->name, -3) != '_E_') {
			$n = $kiwanja->name . '_E_';
			$list .= "<div class='spacer parent'>";
			if (in_array($n, $name)) {
			$list .= "<input style='width: 20px;' type='checkbox' name='Extract[]' class='fild' checked value='".$kiwanja->name."'>";
			} else {
			$list .= "<input style='width: 20px;' type='checkbox' name='Extract[]' class='fild' value='".$kiwanja->name."'>";			
			}
	   		$list .= $kiwanja->name ."</div>";
	   	}
	}	
	
	 $list .= '<div class="spacer">
	 		 <input type="hidden" value="'.$doc_info[0]['DocTypeID'].'" name="DocumentType"/>
			 <input type="hidden" value="'. $stabs .'" name="tablename"/>
			 <input type="button" class="DatasetEditBT" value="Submit" id="DatasetEditBT"/>
		   </div>';
	 $list .= '</form>';
	
	$list = empty($list) ? "Sorry No Data" : $list;
	
	echo $list;
    }
    
    
    function EntityExtract(){
	   //$this->output->enable_profiler(TRUE); 
	   
	   	$table_name = $this->input->post('tablename');
	   	$DocumentType = $this->input->post('DocumentType');
	   	//$Verb = $this->input->post('Verb');
	   	$DocName = (strlen(trim($this->input->post('DocName'))) >= 4) ? str_replace(' ', '_', $this->input->post('DocName')) : '';
	   	$viwanja = $this->input->post('Extract');
	   	
	   	if (strlen($DocName) >= 4) { $this->admin_model->table_name_change('NewTable', $DocName); } 
	   	else { $DocName = $table_name; }
	   	
	   	$DocID = $this->admin_model->get_document_entry($DocName) ? : $this->admin_model->insert_document($DocName, $DocumentType);
	   	$DocDetails = $this->admin_model->get_document_details($DocID);
	   	$CountryID = $DocDetails[0]['CountryID'];
	
		//var_dump($viwanja); exit;
		//var_dump($Verb);
		//echo $DocumentType; exit;

    	$list ="Records Submitted ";
    	$l=0;
    	for($i=0; $i<sizeof($viwanja); $i++){
//echo $viwanja[$i]; exit;
		$this->admin_model->fieldcheck($viwanja[$i], $DocName);

    	$l += $this->admin_model->extract_entity($viwanja[$i], $DocName, $DocID, $this->session->userdata('user_id'), $DocumentType, $CountryID);

    	}
    		
	$list = empty($list) ? "Sorry No Records Submitted" : $list .$l ;
	echo $list;
    }
    


   function DatasetEdit(){
  //  $this->output->enable_profiler(TRUE); 
   
   	$tbl = $this->input->post('Entitytablename');
   	$DataField = $this->input->post('DataEntityField');
  
   	$flds = (count($DataField) > 0) ? "'".implode(',', $DataField) ."'" : '*';
       	$list ="Records Submitted ";
 	//echo $flds; exit;
    	$this->admin_model->dataset_edit($tbl, $flds);

    }
    
   
    function DatasetAdd(){
   // $this->output->enable_profiler(TRUE);
    
   // $this->load->library('upload');
    
    //$allowed = "/[^a-z0-9\\040\\.\\-\\_\\\\]/i";

    $TblName =  'NewTable';//$this->input->post('TblName');  
    $DocumentType = 1;//$this->input->post('DocumentType');
    
    $error = "";
    $msg = "";
	//$this->upload->do_upload('fileToUpload') ;
	//	$msg .=  $TblName . "no tab name"; //exit;
	/*$table_check = $this->admin_model->get_tables();
	if(in_array($TblName, $table_check)){
		$msg .= "Sorry the table ".$TblName." already exists. Please insert another name.";
	}
	if($DocumentType==0){
		$msg .= "Please select a category.";
	}
	*/
	$fileElementName = 'fileToUpload';
	//$msg .=$_FILES[$fileElementName]['error'];
	//echo $_FILES[$fileElementName]['error']; exit;
	if(!empty($_FILES[$fileElementName]['error'])){
		
		switch($_FILES[$fileElementName]['error'])
		{

			case '1':
				$msg .= 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
				break;
			case '2':
				$msg .= 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
				break;
			case '3':
				$msg .= 'The uploaded file was only partially uploaded';
				break;
			case '4':
				$msg .= 'No file was uploaded here.';
				break;

			case '6':
				$msg .= 'Missing a temporary folder';
				break;
			case '7':
				$msg .= 'Failed to write file to disk';
				break;
			case '8':
				$msg .= 'File upload stopped by extension';
				break;
			case '999':
			default:
				$msg = '';//'No error code avaiable';
		}
	}
	
	//$msg .= $_FILES[$fileElementName]['tmp_name'];
	if(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
	{
		$msg .= 'No file was uploaded..';
	} 
	//else {
	
	//		$msg .= " File Name: " . $_FILES['fileToUpload']['name'] . ", ";
	//		$msg .= " File Size: " . @filesize($_FILES['fileToUpload']['tmp_name']);	
	//for security reason, we force to remove all uploaded file
	
	if ($msg==''){
		
		/********************************/
		/* Would you like to add an ampty field at the beginning of these records?
		/* This is useful if you have a table with the first field being an auto_increment integer
		/* and the csv file does not have such as empty field before the records.
		/* Set 1 for yes and 0 for no. ATTENTION: don't set to 1 if you are not sure.
		/* This can dump data in the wrong fields if this extra field does not exist in the table
		/********************************/
		$addauto = 1;

		$filename = $_FILES[$fileElementName]['tmp_name'];
	  	$size = filesize($filename);
	  	if (($handle = fopen($filename, "r")) !== FALSE) {
	  	$data = fgetcsv($handle, 1000, ",");
		    $i=0;
		    $flds="";
		    $num = count($data);
	
			for ($c=0; $c < $num; $c++) {

			    $columnnames[]= "`". str_replace(".","",str_replace("/","_", str_replace(" ", "_", trim($data[$c])))) ."` varchar(255)";

			}
   		fclose($handle);
		   // echo $i;
		}
		
		$columnames = implode(", ", $columnnames);
		$columnames ="id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),".$columnames;
		//echo $columnames; exit;
		$NewTable = $this->admin_model->create_table($TblName, $columnames);
		//$NewTable=1;
		if (!$NewTable){
		$msg .= "error creating table";
		}
		else 
		{
		$lines = 0;
		$queries = "";
		$linearray = array();

		//create table, columns

		//$columnnames = array();

		$row = 1;
		$fieldseparator = ",";
		$lineseparator = "\n";
		
		$handle = fopen($filename, "r");
		$csvcontent = fread($handle,$size);

		//var_dump(explode($lineseparator,$csvcontent));

				foreach(explode($lineseparator,$csvcontent) as $line) {

					$lines++;
					$skipped=0;
					if($lines>1){
						$line = trim($line," \t");

						$line = str_replace("\r","",$line);

						/************************************
						This line escapes the special character. remove it if entries are already escaped in the csv file
						************************************/
						$line = str_replace("'","\'",$line);
						/*************************************/
				
						$linearray = explode($fieldseparator,$line);
						$linearray = str_replace(",", "\,", $linearray);
						$linearray = str_replace("'\," ,"'," ,$linearray);
						//$linearray = preg_replace( "#[^a-zA-Z0-9,.]#", "", $linearray);
						$linemysql = implode("','",$linearray);
						if (strlen($linemysql)>=1){
							if($addauto){
								$query = "insert into $TblName values('','$linemysql');";
								}
							else
							{
								$query = "insert into $TblName values('$linemysql');";
								}
						}

				//$queries .= $query . "\n";

			//echo $queries; exit;
						$insert = $this->admin_model->populate_table($query);

						if(!$insert){ $skipped++; $msg .= " $skipped were not inserted";}

					}
				}
		
    		//$q =  "insert into DocUploaded (title, doc_id, DocTypeID,data_table) values('$TblName', '".date('Ymd')."-$TblName',$DocumentType,'$TblName');";
    		//$this->admin_model->populate_table($q);
    		
		 $msg .= "Successfully Imported";
		 
		//	var_dump($_FILES['fileToUpload']['tmp_name']);	
		@unlink($_FILES[$fileElementName]);	
		}
	}
	
		
	echo "{";
	//echo	"error: '" . $error . "',\n";
	echo	"msg: '" . $msg . "'\n";
	echo "}";
	
   //	exit;
   	//}
    }

}
