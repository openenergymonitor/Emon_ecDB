<?php

  defined('EMONCMS_EXEC') or die('Restricted access');
  
  function ecdb_controller()
  {
    global $route, $mysqli, $session;
    
    // Just to be extra sure we return straight away if write access is not present.
    if ($session['write']==false) return array('content'=>false);
    
    $result = false;
 
    // The userid for ecdb is currently fixed and so any user can access and collaborate on one 
    // ecdb repository
    $userid = 4;
    
    include "Modules/ecdb/component_model.php";
    $component = new Component($mysqli);
    
    include "Modules/ecdb/project_model.php";
    $project = new Project($mysqli);

    // HTML pages and write access check
    if ($route->format == 'html' && $session['write'])
    {
      if ($route->action == 'component')
      {
        if ($route->subaction == 'list') $result = view("Modules/ecdb/Views/componentlist_view.php",array());
        if ($route->subaction == 'shopping') $result = view("Modules/ecdb/Views/shoppinglist_view.php",array());
        if ($route->subaction == 'add') $result = view("Modules/ecdb/Views/addcomponent_view.php",array());
        if ($route->subaction == 'edit') $result = view("Modules/ecdb/Views/addcomponent_view.php",array('id'=>get('id')));
        if ($route->subaction == 'log') $result = view("Modules/ecdb/Views/component_log_view.php",array());        

      }
      
      if ($route->action == 'project')
      {
        if ($route->subaction == 'list') $result = view("Modules/ecdb/Views/projectlist_view.php",array());
        if ($route->subaction == 'make') $result = view("Modules/ecdb/Views/produce_view.php",array());
        
        if ($route->subaction == 'view') $result = view("Modules/ecdb/Views/projectcomponentlist_view.php",array('id'=>get('id')));
      }
    }
    
    // JSON api and write access check
    if ($route->format == 'json' && $session['write'])
    {   
      // Component API
      if ($route->action == 'component')
      {
        // Get a list of all componenets
        if ($route->subaction == 'list') $result = $component->get_list($userid);
        
        // Get properties of a single component
        if ($route->subaction == 'get') $result = $component->get(get('id'));
        
        // Set the properties of a single component
        if ($route->subaction == 'set') $result = $component->set(post('id'),post('properties'));
        
        // Add a new component
        if ($route->subaction == 'add') $result = $component->add($userid,post('properties'));
        
        // Delete a component
        if ($route->subaction == 'delete') $result = $component->delete(get('id'));
        
        // Get a list of projects using a component
        if ($route->subaction == 'projects') $result = $component->get_projects($userid,get('id'));
        
        if ($route->subaction == 'log') $result = $component->getlog();
      }
      
      // Project API
      if ($route->action == 'project')
      {
        // Add a project
        if ($route->subaction == 'add') $result = $project->add($userid,get('name'));
        
        if ($route->subaction == 'setname') $result = $project->set_name(get('id'),get('name'));
        if ($route->subaction == 'setgroup') $result = $project->set_group(get('id'),get('group'));
        if ($route->subaction == 'setcostprice') $result = $project->set_costprice(get('id'),get('costprice'));
        if ($route->subaction == 'setsellingprice') $result = $project->set_sellingprice(get('id'),get('sellingprice'));
                        
        if ($route->subaction == 'getname') $result = $project->get_name(get('id'));
        
        // Get a list of projects
        if ($route->subaction == 'list') $result = $project->get_list($userid);
        
        // Delete a project
        if ($route->subaction == 'delete') $result = $project->delete(get('id'));
        
        // Add, update and remove component all in one method:
        if ($route->subaction == 'component') $result = $project->component(get('id'),get('component_id'),get('component_quantity'));
        
        // Get a list of the components in a project
        if ($route->subaction == 'componentlist') $result = $project->get_component_list(get('id'));
        
        if ($route->subaction == 'duplicate') $result = $project->duplicate(get('id'));

        if ($route->subaction == 'produce') $result = $project->produce(get('id'),get('quantity'));
      }
      
    }
  
    return array('content'=>$result);
  }
