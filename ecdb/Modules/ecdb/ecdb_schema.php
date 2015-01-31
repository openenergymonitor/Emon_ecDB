<?php

$schema['data'] = array(
  'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'MUL', 'Extra'=>'auto_increment'),
  'owner' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'MUL'),
  'category' => array('type' => 'varchar(11)', 'Null'=>'NO'),
  'name' => array('type' => 'varchar(64)', 'Null'=>'NO'),
  'manufacturer'=> array('type' => 'varchar(64)', 'Null'=>'NO'),
  'package' => array('type' => 'varchar(64)', 'Null'=>'NO'),
  'smd'=> array('type' => 'varchar(3)', 'Null'=>'NO', 'default'=>'No'),
  'quantity'=> array('type' => 'varchar(11)', 'Null'=>'NO'),
  'order_quantity'=> array('type' => 'varchar(11)', 'Null'=>'NO'),
  'location'=> array('type' => 'varchar(32)', 'Null'=>'NO'),
  'scrap'=> array('type' => 'varchar(3)', 'Null'=>'NO', 'default'=>'No'),
  'price'=> array('type' => 'varchar(11)', 'Null'=>'NO'),
  'weight'=> array('type' => 'varchar(11)', 'default'=>'NULL'),
  'datasheet'=> array('type' => 'varchar(256)', 'Null'=>'NO'),
  'comment'=> array('type' => 'text', 'Null'=>'NO'),
  'width'=> array('type' => 'varchar(11)', 'default'=>'NULL'),
  'height'=> array('type' => 'varchar(11)', 'default'=>'NULL'),
  'depth'=> array('type' => 'varchar(11)', 'default'=>'NULL'),
  //'partcode' => array('type' => 'varchar(32)', 'Null'=>'NO'),

  'public'=> array('type' => 'varchar(3)', 'Null'=>'NO', 'default'=>'No'),
  'url1'=> array('type' => 'varchar(256)', 'Null'=>'NO'),
  'url2'=> array('type' => 'varchar(256)', 'Null'=>'NO'),
  'url3'=> array('type' => 'varchar(256)', 'Null'=>'NO'),
  'url4'=> array('type' => 'varchar(256)', 'Null'=>'NO')
); 

$schema['projects'] = array(
  'project_id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'project_owner' => array('type' => 'int(11)', 'Null'=>'NO','Key'=>'MUL'),
  'project_name' => array('type' => 'varchar(64)', 'Null'=>'NO'),
  'project_group'=> array('type' => 'varchar(32)', 'Null'=>'NO'),
  'project_costprice'=> array('type' => 'varchar(11)', 'Null'=>'NO'),
  'project_sellingprice'=> array('type' => 'varchar(11)', 'Null'=>'NO')
);

$schema['projects_data'] = array(
  'projects_data_id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'projects_data_owner_id' => array('type' => 'int(11)','Null'=>'NO', 'Key'=>'MUL'),
  'projects_data_project_id' => array('type' => 'int(11)','Null'=>'NO', 'Key'=>'MUL'),
  'projects_data_component_id' => array('type' => 'int(11)','Null'=>'NO', 'Key'=>'MUL'),
  'projects_data_quantity' => array('type' => 'int(11)','Null'=>'NO')
);

$schema['ecdb_log'] = array(
  'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'time' => array('type' => 'int(11)'),
  'message' => array('type' => 'text')
);

?>
