<?php
/*
Plugin Name: ToolTip
Plugin URI: http://www.osclass.org/
Description: Adds a jQuery tooltip to any element.
Version: 1.0
Author: mmcsus
Author URI: http://www.osclass.org/
Short Name: ToolTip
Plugin update URI:  tooltip
*/
/*
$scheme = ['e_color'];
switch ($scheme)
{
    case 'blue':
        echo "jqToolTip_blue.css";
        break;

    case 'green':
        echo "Yum yum\n";
        break;

    case 'red':
        echo "Not bad\n";
        break;

    case 'purple':
        echo "Yuck\n";
        break;

     case 'black':
        echo "Yuck\n";
        break;

    default:
        echo "I've never tried them before\n";
        break;
}
*/

function tooltip_call_after_install() {
    // Insert here the code you want to execute after the plugin's install
    // for example you might want to create a table or modify some values

    // In this case we'll create a table to store the color scheme attributes
    $connection = DBConnectionClass::newInstance() ;
    $var = $connection->getOsclassDb();
    $conn       = new DBCommandClass( $var ) ;

    $path = osc_plugin_resource('ToolTip/struct.sql');
    $sql = file_get_contents($path);
    
    if(! $conn->importSQL($sql) ){
        throw new Exception( $conn->getErrorLevel().' - '.$conn->getErrorDesc() ) ;
    }
}

function tooltip_call_after_uninstall() {
    // Insert here the code you want to execute after the plugin's uninstall
    // for example you might want to drop/remove a table or modify some values
	
    // In this case we'll remove the table we created to store color scheme attributes
    $connection = DBConnectionClass::newInstance() ;
    $var = $connection->getOsclassDb();
    $conn       = new DBCommandClass( $var ) ;

    $conn->query('DROP TABLE '.DB_TABLE_PREFIX.'t_tooltip') ;
    
    $error_num = $conn->getErrorLevel() ;
    
    if( $error_num > 0 ) {
        throw new Exception($conn->getErrorLevel().' - '.$conn->getErrorDesc());
    }
}

function tooltip_form_post() {
                // Insert the data in our plugin's table
            $connection = DBConnectionClass::newInstance() ;
            $var = $connection->getOsclassDb();
            $conn       = new DBCommandClass( $var ) ;

            $sql = sprintf("INSERT INTO %st_tooltip (e_color) VALUES (%d, '%s', '%s', '%s')", 
                            DB_TABLE_PREFIX, Params::getParam('color'));
            $conn->query($sql) ;

            $error_num = $conn->getErrorLevel() ;
            if( $error_num > 0 ) {
                throw new Exception($conn->getErrorLevel().' - '.$conn->getErrorDesc());
            }
        }

function jqToolTip(){
osc_enqueue_script('jquery');
osc_enqueue_script('jquery-ui');
osc_register_script('jqToolTip', osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'jqToolTip.js', array('jquery', 'jquery-ui'));
osc_enqueue_script('jqToolTip');
//osc_register_script('jqToolTip', osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'jqToolTip.js', array('jquery', 'jquery-ui'));
//echo '<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>';
//echo '<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />';
//echo '<script type="text/javascript" src="'.osc_base_url().'oc-content/plugins/ToolTip/jqToolTip.js"></script>';
//echo '<link href="'.osc_base_url().'oc-content/plugins/ToolTip/jqToolTip.css" rel="stylesheet" type="text/css" />';
osc_enqueue_style('custom-jquery-tooltip', osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'jqToolTip.css');

}

 function jqToolTip_help() {

                osc_admin_render_plugin(osc_plugin_path(dirname(__FILE__)) . '/help.php') ;
 }


// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), 'tooltip_call_after_install');

osc_add_hook('init','jqToolTip');

// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'tooltip_call_after_uninstall');

// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
//osc_add_hook('admin_header', 'jqToolTip_help');
osc_add_hook(osc_plugin_path(__FILE__) . '_configure', 'jqToolTip_help');

?>