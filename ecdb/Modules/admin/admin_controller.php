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

function admin_controller()
{
    global $mysqli,$session,$route;

    $sessionadmin = false;
    if ($session['admin']) $sessionadmin = true;

    if ($sessionadmin)
    {
      if ($route->action == 'view') $result = view("Modules/admin/admin_main_view.php", array());

      if ($route->action == 'db')
      {
          $applychanges = get('apply');
          if (!$applychanges) $applychanges = false; else $applychanges = true;

          require "Modules/admin/update_class.php";
          require_once "Lib/dbschemasetup.php";

          $update = new Update($mysqli);

          $updates = array();
          $updates[] = array(
            'title'=>"Database schema", 
            'description'=>"", 
            'operations'=>db_schema_setup($mysqli,load_db_schema(),$applychanges)
          );

          if (!$updates[0]['operations']) {

            // No set updates yet
 
          }

          $result = view("Modules/admin/update_view.php", array('applychanges'=>$applychanges, 'updates'=>$updates));
      }
    }

    return array('content'=>$result);
}
