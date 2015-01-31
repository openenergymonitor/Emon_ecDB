<?php

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class Component
{

  private $mysqli;

  public function __construct($mysqli)
  {
      $this->mysqli = $mysqli;
  }

  public function get_list($userid)
  {
    $userid = (int) $userid; 
    
    $components = array();
    $result = $this->mysqli->query("SELECT * FROM data WHERE `owner` = '$userid'");
    while ($row = $result->fetch_object())
    {
      $components[] = $row;
    }
    return $components;
  }
  
  public function get($id)
  {
    $id = (int) $id;
    
    $components = array();
    $result = $this->mysqli->query("SELECT * FROM data WHERE `id` = '$id'");
    
    return $result->fetch_object();
  }
  
  public function set($id,$fields)
  {
    $id = (int) $id;
    $fields = json_decode($fields);

    $array = array();

    // Repeat this line changing the field name to add fields that can be updated:
    if (isset($fields->name)) $array[] = "`name` = '".preg_replace('/[^\w\s-.%]/','',$fields->name)."'";
    if (isset($fields->category)) $array[] = "`category` = '".preg_replace('/[^\w\s-]/','',$fields->category)."'";
    if (isset($fields->manufacturer)) $array[] = "`manufacturer` = '".preg_replace('/[^\w\s-]/','',$fields->manufacturer)."'";
    if (isset($fields->package)) $array[] = "`package` = '".preg_replace('/[^\w\s-]/','',$fields->package)."'";
    if (isset($fields->smd)) $array[] = "`smd` = '".preg_replace('/[^\w\s-]/','',$fields->smd)."'";
    
    if (isset($fields->quantity)) { 
      $array[] = "`quantity` = '".intval($fields->quantity)."'";
      $this->log("Quantity changed for component, id: $id, new quantity: ".intval($fields->quantity));
    }
    
    if (isset($fields->order_quantity)) $array[] = "`order_quantity` = '".intval($fields->order_quantity)."'";   

    if (isset($fields->location)) $array[] = "`location` = '".preg_replace('/[^\w\s-]/','',$fields->location)."'";
    if (isset($fields->price)) $array[] = "`price` = '".floatval($fields->price)."'"; 
    if (isset($fields->weight)) $array[] = "`weight` = '".floatval($fields->weight)."'";

    // WARNING UNSANITISED!
    if (isset($fields->datasheet)) $array[] = "`datasheet` = '".$fields->datasheet."'";
    if (isset($fields->comment)) $array[] = "`comment` = '".$fields->comment."'";
    
    if (isset($fields->width)) $array[] = "`width` = '".floatval($fields->width)."'";
    if (isset($fields->height)) $array[] = "`height` = '".floatval($fields->height)."'";
    if (isset($fields->depth)) $array[] = "`depth` = '".floatval($fields->depth)."'";

    if (isset($fields->partcode)) $array[] = "`partcode` = '".preg_replace('/[^\w\s-]/','',$fields->partcode)."'";
    
    // Convert to a comma seperated string for the mysql query
    $fieldstr = implode(",",$array);
    $this->mysqli->query("UPDATE data SET ".$fieldstr." WHERE `id` = '$id'"); 

    if ($this->mysqli->affected_rows>0){
      return array('success'=>true, 'message'=>'Properties updated');
    } else {
      return array('success'=>false, 'message'=>'Properties could not be updated');
    }
  }

  public function add($owner,$properties)
  {
    $owner = (int) $owner;
    $properties = json_decode($properties);

    // Repeat this line changing the field name to add fields that can be updated:
    if (isset($properties->name)) $name = preg_replace('/[^\w\s-.%]/','',$properties->name); else $name = "";
    if (isset($properties->category)) $category = preg_replace('/[^\w\s-]/','',$properties->category); else $category = "";
    if (isset($properties->manufacturer)) $manufacturer = preg_replace('/[^\w\s-]/','',$properties->manufacturer); else $manufacturer = "";
    if (isset($properties->package)) $package = preg_replace('/[^\w\s-]/','',$properties->package); else $package = "";
    if (isset($properties->smd)) $smd = preg_replace('/[^\w\s-]/','',$properties->smd); else $smd = "";
    
    if (isset($properties->quantity)) {
      $quantity = (int) $properties->quantity;
      $this->log("New component created, name: $name, starting quantity: $quantity");
    } else $quantity = 0;
    
    if (isset($properties->order_quantity)) $order_quantity = (int) $properties->order_quantity; else $order_quantity = 0;

    if (isset($properties->location)) $location = preg_replace('/[^\w\s-]/','',$properties->location); else $location = '';
    if (isset($properties->price)) $price = floatval($properties->price);  else $price = 0;
    if (isset($properties->weight)) $weight = floatval($properties->weight); else $weight = 0;

    // WARNING UNSANITISED!
    if (isset($properties->datasheet)) $datasheet = $properties->datasheet; else $datasheet = '';
    if (isset($properties->comment)) $comment = $properties->comment; else $comment = '';
    
    if (isset($properties->width)) $width = floatval($properties->width); else $width = 0;
    if (isset($properties->height)) $height = floatval($properties->height); else $height = 0;
    if (isset($properties->depth)) $depth = floatval($properties->depth); else $depth = 0;

    if (isset($properties->partcode)) $partcode = preg_replace('/[^\w\s-]/','',$properties->partcode); else $partcode = '';

    $query = "INSERT INTO data (owner,category,name,manufacturer,package,smd,quantity,order_quantity,location,price,weight,datasheet,comment,width,height,depth,partcode) VALUES ('$owner','$category','$name','$manufacturer','$package','$smd','$quantity','$order_quantity','$location','$price','$weight','$datasheet','$comment','$width','$height','$depth','$partcode')";
    $this->mysqli->query($query);

    $componentid = $this->mysqli->insert_id;
    
    if ($componentid>0) {
      return array('success'=>true, 'feedid'=>$componentid);										
    } else return array('success'=>false, 'query'=>$query);     
  }
  
  public function delete($id)
  {
     $id = (int) $id;
     $result = $this->mysqli->query("DELETE FROM data WHERE `id`='$id'");
  }
  
  // Used in component add/edit view to show projects that use the selected component
  
  public function get_projects($userid,$componentid)
  {
    $userid = (int) $userid;
    $componentid = (int) $componentid;
    
    $projects = array();
    $result = $this->mysqli->query("SELECT * FROM projects_data WHERE `projects_data_owner_id` = '$userid' AND `projects_data_component_id`='$componentid'");
    while ($row = $result->fetch_object())
    {
      $project_id = $row->projects_data_project_id;
      
      $project_result = $this->mysqli->query("SELECT * FROM projects WHERE `project_id` ='$project_id'");
      $project_row = $project_result->fetch_object();
      $project_name = $project_row->project_name;
      
      $projects[] = array('project_id'=>$project_id, 'project_name'=>$project_name, 'quantity'=>$row->projects_data_quantity);
    }
    return $projects;
  }


  private function log($message)
  {
      $time = time();
      $this->mysqli->query("INSERT INTO ecdb_log (time,message) VALUES ('$time','$message')");
  }
  
  public function getlog()
  {
    $log = array();
    $result = $this->mysqli->query("SELECT * FROM ecdb_log ORDER BY `time` Desc");
    while ($row = $result->fetch_object())
    {
      $log[] = $row;
    }
    return $log;
  }
}
 
