<?php
/*
 * @version $Id: report.class.php 206 2016-03-08 17:29:15Z tsmr $
 -------------------------------------------------------------------------
 Addressing plugin for GLPI
 Copyright (C) 2003-2011 by the addressing Development Team.

 https://forge.indepnet.net/projects/addressing
 -------------------------------------------------------------------------

 LICENSE

 This file is part of addressing.

 Addressing is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Addressing is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Addressing. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginAddressingReservation extends CommonDBTM {
     //static $rightname = "plugin_addressing_reservation";
    static $rightname = "plugin_addressing";
    var $fields = array('name','begin_ip','end_ip');
     function getSearchOptions() {

      $tab = array();      
      
      $tab[1]['table']         = $this->getTable();
      $tab[1]['field']         = 'name';
      $tab[1]['name']          = __('Name');
      $tab[1]['datatype']        = 'itemlink';
                     
      $tab[10]['table']         = $this->getTable();
      $tab[10]['field']         = 'begin_ip';
      $tab[10]['name']          = __('Start IP');
      $tab[10]['datatype']      = 'text';
      
    
      
      $tab[20]['table']         = $this->getTable();
      $tab[20]['field']         = 'end_ip';
      $tab[20]['name']          = __('End Ip');
      $tab[20]['datatype']      = 'text';

/*    
      $tab[30]['table']          = 'glpi_entities';
      $tab[30]['field']          = 'completename';
      $tab[30]['name']           = __('Entity');
      $tab[30]['datatype']       = 'dropdown';
      
      $tab[40]['table']           = 'glpi_networks';
      $tab[40]['field']           = 'name';
      $tab[40]['name']            = _n('Network', 'Networks', 2);
      $tab[240]['datatype']        = 'dropdown';
*/
      return $tab;
         
   }
    
   static function getTypeName($nb=0) {
      return _n("Reserved IP", "Reserved IP", $nb, 'addressing');
   }

    static function showList($params) {
         Search::show("PluginAddressingReservation");        
    }
    
    
    function compareIP($start_ip,$end_ip) {
        $start_ip = explode(".",$start_ip); 
        $end_ip = explode(".",$end_ip);
        $major = false;
        
        
        foreach ($start_ip as $i=>$num) {
            if (intval($start_ip[$i]) < intval($end_ip[$i])) {
              $major = true;
            } else if (intval($start_ip[$i]) > intval($end_ip[$i])) {
                return -1;
            } 
        }
        if ($major == true)
            return 1;
        else 
            return 0;
    }
    
    function validateData($input) {
           
      if (!empty($_POST["name"]) && !empty($_POST["begin_ip"]) && !empty($_POST["end_ip"])) {
          if (!filter_var($_POST["begin_ip"], FILTER_VALIDATE_IP) && !filter_var($_POST["end_ip"], FILTER_VALIDATE_IP)) {
            Session::addMessageAfterRedirect(__('Invalid data inserted','addressing'), false, ERROR); 
            return false;
          }
          else {
            if ( $this->compareIP($_POST["begin_ip"],$_POST["end_ip"]) < 0 ) {
               Session::addMessageAfterRedirect(__('Invalid data inserted, start ip must be before end ip','addressing'), false, ERROR);    
               return false;
            }
          }
      } else {
          Session::addMessageAfterRedirect(__('Invalid data inserted','addressing'), false, ERROR); 
         return false;
      }
      return true;     
    }
 

    
   function showForm ($ID, $options=array()) {

      $this->initForm($ID, $options);      

      $this->showFormHeader($options);

      $PluginAddressingConfig = new PluginAddressingConfig();
      $PluginAddressingConfig->getFromDB('1');

      echo "<tr class='tab_bg_1'>";

      echo "<td>".__('Name')."</td>";
      echo "<td>";
      Html::autocompletionTextField($this,"name");
      echo "</td>";

  
      echo "</tr>";
      if (!isset($options["ip"])){
         echo "<tr class='tab_bg_1'>";
         echo "<td>".__('First IP', 'addressing')."</td>"; // Subnet
         echo "<td>";
         echo "<input type='text' id='plugaddr_ipdeb0' value='' name='_ipdeb0' size='3' ".
                "onChange='plugaddr_ChangeNumber(\"".__('Invalid data !!', 'addressing')."\");'>.";
         echo "<input type='text' id='plugaddr_ipdeb1' value='' name='_ipdeb1' size='3' ".
                "onChange='plugaddr_ChangeNumber(\"".__('Invalid data !!', 'addressing')."\");'>.";
         echo "<input type='text' id='plugaddr_ipdeb2' value='' name='_ipdeb2' size='3' ".
                "onChange='plugaddr_ChangeNumber(\"".__('Invalid data !!', 'addressing')."\");'>.";
         echo "<input type='text' id='plugaddr_ipdeb3' value='' name='_ipdeb3' size='3' ".
                "onChange='plugaddr_ChangeNumber(\"".__('Invalid data !!', 'addressing')."\");'>";
         echo "</td>";

         echo "<td>".__('Last IP', 'addressing')."</td>"; // Mask
         echo "<td>";
         echo "<input type='text' id='plugaddr_ipfin0' value='' name='_ipfin0' size='3' ".
                "onChange='plugaddr_ChangeNumber(\"".__('Invalid data !!', 'addressing')."\");'>.";
         echo "<input type='text' id='plugaddr_ipfin1' value='' name='_ipfin1' size='3' ".
                "onChange='plugaddr_ChangeNumber(\"".__('Invalid data !!', 'addressing')."\");'>.";
         echo "<input type='text' id='plugaddr_ipfin2' value='' name='_ipfin2' size='3' ".
                "onChange='plugaddr_ChangeNumber(\"".__('Invalid data !!', 'addressing')."\");'>.";
         echo "<input type='text' id='plugaddr_ipfin3' value='' name='_ipfin3' size='3' ".
                "onChange='plugaddr_ChangeNumber(\"".__('Invalid data !!', 'addressing')."\");'>";
         echo "</td>";
         echo "</tr>";

         echo "<input type='hidden' id='plugaddr_ipdeb' value='".$this->fields["begin_ip"]."' name='begin_ip'>";
         echo "<input type='hidden' id='plugaddr_ipfin' value='".$this->fields["end_ip"]."' name='end_ip'>";
         echo "<input type='hidden' id='plugaddr_range' value='' name='plugaddr_range'>";
         echo "<input type='hidden' id='plugaddr_subnet' value='' name='plugaddr_subnet'>";

         if ($ID > 0) {
            $js = "plugaddr_Init(\"".__('Invalid data !!', 'addressing')."\");";
            echo Html::scriptBlock($js);
         }
      } else {
         echo "<tr class='tab_bg_1'>";
         echo "<td>".__('IP address', 'addressing')."</td>"; 
         echo "<td>" . $options["ip"] . "</td>";
         echo "<input type='hidden' id='plugaddr_ipdeb' value='" . $options["ip"] . "' name='begin_ip'>";
         echo "<input type='hidden' id='plugaddr_ipfin' value='" . $options["ip"] . "' name='end_ip'>";
         
         echo "</tr>";
      }
      $this->showFormButtons($options);

      return true;
   }

}
?>
