<?php

namespace mod_seguimiento\task;

class alerta_automatica extends \core\task\scheduled_task {      
    public function get_name() {
        // Shown in admin screens
        return get_string('alertaautomatica', 'mod_seguimiento');
    }
                                                                     
    public function execute() {       
	    global $CFG;
	    require_once($CFG->dirroot . '/mod/seguimiento/lib.php');
	    seguimiento_cron();

    }                                                                                                                               
} 

?>