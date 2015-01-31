<?php

/*

  Project Class

  Class / Library API (Public methods)

  Add a project
  $project->add();
  
  Get a list of projects
  $project->get_list();
  
  Delete a project
  $project->delete();
  
  Add, update and remove component all in one method:
  $project->component();
  
  Get a list of the components in a project
  $project->get_component_list();
  

*/

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class Project
{

  private $mysqli;

  public function __construct($mysqli)
  {
      $this->mysqli = $mysqli;
  }
  
  public function add($userid,$name)
  {
    $userid = (int) $userid;
    $name = preg_replace('/[^\w\s-]/','',$name);
    
    $this->mysqli->query("INSERT INTO projects (project_owner,project_name) VALUES ('$userid','$name')");
    $project_id = $this->mysqli->insert_id;
     
    return $project_id;
  }
  
  public function get_list($userid)
  {
    $userid = (int) $userid;
    
    $projects = array();
    $result = $this->mysqli->query("SELECT * FROM projects WHERE `project_owner` = '$userid'");
    while ($row = $result->fetch_object())
    {
      $row->id = $row->project_id;
      $projects[] = $row;
    }
    return $projects;
  }
  
  public function set_name($id,$name)
  {
    $id = (int) $id;
    $name = preg_replace('/[^\w\s-]/','',$name);
  
    $this->mysqli->query("UPDATE projects SET `project_name` = '$name' WHERE `project_id` = '$id'");
  }
  
  public function set_group($id,$group)
  {
    $id = (int) $id;
    $group = preg_replace('/[^\w\s-]/','',$group);
  
    $this->mysqli->query("UPDATE projects SET `project_group` = '$group' WHERE `project_id` = '$id'");
  }
  
  public function set_costprice($id,$costprice)
  {
    $id = (int) $id;
    $costprice = preg_replace('/[^\w\s-.]/','',$costprice);
  
    $this->mysqli->query("UPDATE projects SET `project_costprice` = '$costprice' WHERE `project_id` = '$id'");
  }
  
  public function set_sellingprice($id,$sellingprice)
  {
    $id = (int) $id;
    $sellingprice = preg_replace('/[^\w\s-.]/','',$sellingprice);
  
    $this->mysqli->query("UPDATE projects SET `project_sellingprice` = '$sellingprice' WHERE `project_id` = '$id'");
  }
  
  public function get_name($id)
  {
    $id = (int) $id;
    $result = $this->mysqli->query("SELECT `project_name` FROM projects WHERE `project_id` = '$id'");
    $row = $result->fetch_array();
    return $row['project_name'];
  }
  
  public function delete($id)
  {
    $id = (int) $id;
    $this->mysqli->query("DELETE FROM projects WHERE `project_id` = '$id'");
  }
  
  // Component related
  
  public function component($project_id,$component_id,$component_quantity)
  {
    $project_id = (int) $project_id;
    $component_id = (int) $component_id;
    $component_quantity = (int) $component_quantity;
    
    // Check if component already exists
    $result = $this->mysqli->query("SELECT * FROM projects_data WHERE `projects_data_project_id` = '$project_id' AND `projects_data_component_id`='$component_id'");
    
    if (!$result->num_rows)
    {
      // If it does not exist then add
      $this->mysqli->query("INSERT INTO projects_data (projects_data_project_id,projects_data_component_id,projects_data_quantity) VALUES ('$project_id','$component_id','$component_quantity')");
      
      return array("success"=>true, "message"=>"component added to project");
    }
    else 
    {
    
      if ($component_quantity!=0) {
        $this->mysqli->query("UPDATE projects_data SET `projects_data_quantity` = '$component_quantity' WHERE `projects_data_project_id` = '$project_id' AND `projects_data_component_id`='$component_id'");
        
        return array("success"=>true, "message"=>"component quantity updated");
      } else {
        $this->mysqli->query("DELETE FROM projects_data WHERE `projects_data_project_id` = '$project_id' AND `projects_data_component_id`='$component_id'");
        
        return array("success"=>true, "message"=>"component removed from project");
      }
      
    }
  }
  
  
  public function get_component_list($project_id)
  {
    $project_id = (int) $project_id;
    
    $components = array();
    $result = $this->mysqli->query("SELECT * FROM projects_data WHERE `projects_data_project_id` = '$project_id'");
    
    while ($row = $result->fetch_object())
    {
      $component_id = $row->projects_data_component_id;
      $component_quantity = $row->projects_data_quantity;
      $component_result = $this->mysqli->query("SELECT * FROM data WHERE `id` = '$component_id'");
      $component_row = $component_result->fetch_object();
      if ($component_row) 
      {
        if ($component_quantity>0) {
          $makes = (int) ($component_row->quantity*1 / $component_quantity*1);
        } else {
          $makes = "ERROR";
        }
        
        $components[] = array(
          'id'=>$component_row->id,
          'name'=>$component_row->name,
          'category'=>$component_row->category,
          'price'=>$component_row->price,
          'quantity'=>$component_quantity,
          'instock'=>$component_row->quantity,
          'makes'=>$makes
        );
      }
      else
      {
        // component no longer exists in component list
        $this->mysqli->query("DELETE FROM projects_data WHERE `projects_data_component_id` = '$component_id'");
        // echo "deleted component id: $component_id\n";
      }
    }
    
    return $components;
  }
  
  public function produce($project_id, $production_quantity)
  {
    $project_id = (int) $project_id;
    $production_quantity = (int) $production_quantity;
    
    $components = array();
    $result = $this->mysqli->query("SELECT * FROM projects_data WHERE `projects_data_project_id` = '$project_id'");
    
    while ($row = $result->fetch_object())
    {
      $component_id = $row->projects_data_component_id;
      $component_quantity = $row->projects_data_quantity * 1;
      
      $component_result = $this->mysqli->query("SELECT * FROM data WHERE `id` = '$component_id'");
      $component_row = $component_result->fetch_object();
      $current_quantity = $component_row->quantity * 1;
      
      $used = $component_quantity * $production_quantity;
      $new_quantity = $current_quantity - $used;
      $component_result = $this->mysqli->query("UPDATE data SET `quantity` = '$new_quantity' WHERE `id` = '$component_id'");
      
      
      $time = time();
      $message = "Producing ".$production_quantity."x of project $project_id used ".$used."x ".$component_row->name." (id:$component_id) Stock down: ".intval($current_quantity)."->".intval($new_quantity);
      $this->mysqli->query("INSERT INTO ecdb_log (time,message) VALUES ('$time','$message')");
    }
    
    return $components;
  }
  
  public function duplicate($project_id)
  {
    $project_id = (int) $project_id;
    $result = $this->mysqli->query("SELECT * FROM projects WHERE `project_id` = '$project_id'");
    $row = $result->fetch_object();
    
    $name = $row->project_name." (copy)";
    $userid = $row->project_owner;
    
    $this->mysqli->query("INSERT INTO projects (project_owner,project_name) VALUES ('$userid','$name')");
    $new_project_id = $this->mysqli->insert_id;
    
    $result = $this->mysqli->query("SELECT * FROM projects_data WHERE `projects_data_project_id` = '$project_id'");
    
    while ($row = $result->fetch_object())
    {
      $component_id = $row->projects_data_component_id;
      $component_quantity = $row->projects_data_quantity;
      $this->mysqli->query("INSERT INTO projects_data (projects_data_project_id,projects_data_component_id,projects_data_quantity) VALUES ('$new_project_id','$component_id','$component_quantity')");
    }
    
    return "Component duplicated, <a href='http://217.9.195.227/ecdb/ecdb/project/list' >return to component list</a>";
    
  }
  
}
