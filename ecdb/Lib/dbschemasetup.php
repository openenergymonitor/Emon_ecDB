<?php 

/*

    All Emoncms code is released under the GNU Affero General Public License.
    See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

*/

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

function db_schema_setup($mysqli, $schema, $apply)
{
    $operations = array();
    while ($table = key($schema))
    { 
        // if table exists:
        $result = $mysqli->query("SHOW TABLES LIKE '".$table."'");
        if (($result != null ) && ($result->num_rows==1))
        {
            // $out[] = array('Table',$table,"ok");
            //-----------------------------------------------------
            // Check table fields from schema
            //-----------------------------------------------------
            foreach ($schema[$table] as $field => $val) 
            { 
                $type = $schema[$table][$field]['type'];
                if (isset($schema[$table][$field]['Null'])) $null = $schema[$table][$field]['Null']; else $null = "YES";
                if (isset($schema[$table][$field]['default'])) $default = $schema[$table][$field]['default']; else unset($default);
                if (isset($schema[$table][$field]['Extra'])) $extra = $schema[$table][$field]['Extra']; else $extra = null;
                
                // if field exists:
                $result = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE '$field'");
                if ($result->num_rows==0)
                {
                    $query = "ALTER TABLE `$table` ADD `$field` $type";
                    if ($null) $query .= " NOT NULL";
                    if (isset($default)) $query .= " DEFAULT '$default'";
                    $operations[] = $query;
                    if ($apply) $mysqli->query($query);
                }
                else
                {
                  $result = $mysqli->query("DESCRIBE $table `$field`");
                  $array = $result->fetch_array();
                  $query = "";
                  
                  if ($array['Type']!=$type) $query .= ";";
                  
                  if (isset($default) && $array['Default']==null && $default=='NULL') {
                    // Do nothing
                  } elseif (isset($default) && $array['Default']!=$default) {
                    $query .= " Default '$default' ";
                  }
                  
                  if ($array['Null']!=$null && $null=="NO") $query .= " not null";
                  if ($array['Extra']!=$extra && $extra=="auto_increment") $query .= " auto_increment";
                 

                  if ($query) $query = "ALTER TABLE $table MODIFY `$field` $type".$query;
                  if ($query) $operations[] = $query;
                  if ($query && $apply) $mysqli->query($query);
                } 
            }
            
            foreach ($schema[$table] as $field => $val) 
            {
              $result = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE '$field'");
              if ($result->num_rows>0)
              {
                  $result = $mysqli->query("DESCRIBE $table `$field`");
                  $array = $result->fetch_array();
                  
                  if (isset($schema[$table][$field]['Key']) && $array['Key']!=$schema[$table][$field]['Key']) {
                    if ($schema[$table][$field]['Key']=='PRI') $query = "PRIMARY KEY (`$field`)";
                    if ($schema[$table][$field]['Key']=='MUL') $query = "KEY `$field` (`$field`)";     
                    
                    if ($query) $operations[] = $query;
                    if ($query && $apply) $mysqli->query($query);     
                  }
              }
            }
            
        } else {
            //-----------------------------------------------------
            // Create table from schema
            //-----------------------------------------------------
            $query = "CREATE TABLE `" . $table . "` (";
            if (!$apply) $query .= "<br>";
            
            $lines = array();
            
            foreach ($schema[$table] as $field => $val) 
            {
                $query_line = "";
                $type = $schema[$table][$field]['type'];

                if (isset($schema[$table][$field]['Null'])) $null = $schema[$table][$field]['Null']; else $null = "YES";
                if (isset($schema[$table][$field]['default'])) $default = $schema[$table][$field]['default']; else $default = null;
                if (isset($schema[$table][$field]['Extra'])) $extra = $schema[$table][$field]['Extra']; else $extra = null;

                $query_line .= '`'.$field.'`';
                $query_line .= " $type";

                if ($null=="NO") $query_line .= " NOT NULL";
                if ($default && $default!='NULL') $query_line .= " DEFAULT '$default'";
                if ($default && $default=='NULL') $query_line .= " DEFAULT NULL";
                if ($extra) $query_line .= " AUTO_INCREMENT";
                
                $lines[] = $query_line;
            }
            
            // Add index def's at the end
            
            foreach ($schema[$table] as $field => $val) 
            {
              $query_line = "";
              if (isset($schema[$table][$field]['Key'])) {
                if ($schema[$table][$field]['Key']=='PRI') $query_line .= "PRIMARY KEY (`$field`)";
                if ($schema[$table][$field]['Key']=='MUL') $query_line .= "KEY `$field` (`$field`)";
                $lines[] = $query_line;
              }
            }
            
            if (!$apply) {
              $query .= implode($lines,",<br>")."<br>";
            } else {
              $query .= implode($lines,",");
            }
            $query .= ")";
            $query .= " ENGINE=MYISAM DEFAULT CHARSET=utf8";
            if ($query) $operations[] = $query;
            if ($query && $apply) $mysqli->query($query);
        }
        next($schema);
    }
    return $operations;
}
