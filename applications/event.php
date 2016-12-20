<?php

/* * ********************************************************************

 * ********************************************************************* */

class event_app extends application {

    function event_app() {
        $this->application("event", _($this->help_context = "Event Module"));
            	$this->add_lapp_function(0, _("&Add Lecture by day"),"#", 'SS_SMS_EVNT_ADDLEC', MENU_ENTRY);
                $this->add_lapp_function(0, _("&Add Artical"), "#", 'SS_SMS_EVNT_ADDART', MENU_ENTRY);
                $this->add_rapp_function(0, _("&Add Notice "), "#", 'SS_SMS_EVNT_ADDNOT', MENU_ENTRY);
                $this->add_rapp_function(0, _("&Add Assignment "), "#", 'SS_SMS_EVNT_ADDASS', MENU_ENTRY);
                $this->add_rapp_function(0, _("&Assignment List "), "#", 'SS_SMS_EVNT_ADDLIST', MENU_ENTRY);
                $this->add_rapp_function(0, _("&Assignment Evaluation "), "#", 'SS_SMS_EVNT_ADDEVA', MENU_ENTRY);
             
        $this->add_extensions();
        
    }

}

?>