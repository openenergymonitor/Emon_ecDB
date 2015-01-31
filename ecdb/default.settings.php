<?php

  /*
 
  Database connection settings

  */

  $username = "";
  $password = "";
  $server   = "localhost";
  $database = "";

  /*

  Default router settings - in absence of stated path

  */

  // Default controller and action if none are specified and user is anonymous
  $default_controller = "user";
  $default_action = "login";

  // Default controller and action if none are specified and user is logged in
  $default_controller_auth = "ecdb";
  $default_action_auth = "component";
  $default_subaction_auth = "list";
  /*

  Other

  */
      
  // Theme location
  $theme = "basic";
  
  // Error processing
  $display_errors = true;

  // Allow user register in emoncms
  $allowusersregister = TRUE;

  // Enable remember me feature - needs more testing
  $enable_rememberme = TRUE; 

  // Skip database setup test - set to false once database has been setup.
  $dbtest = TRUE;

