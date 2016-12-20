<?php

/* * ********************************************************************

 * ********************************************************************* */

class paymentmanager_app extends application {

    function paymentmanager_app() {
        $this->application("paymentmanager", _($this->help_context = "Payment Module"));

           $this->add_module(_("Payment Management"));              
                $this->add_lapp_function(0, _("&Applicant Payment Receive"), "paymentmanager/manage/admission_list.php", 'SS_SMS_APLCNT_PAYMNT_RCV', MENU_ENTRY);
                $this->add_lapp_function(0, _("&Promotional Student Payment Receive"), "paymentmanager/manage/student_promotion_list.php", 'SS_SMS_STDNT_PAYMNT_RCV', MENU_ENTRY);
				$this->add_lapp_function(0, _("&Student Wise Payment Report"), "paymentmanager/manage/fee_payment_list.php", 'SS_SMS_STDNT_WISE_PAYMNT_REP', MENU_ENTRY);
 
		  $this->add_module(_("Payment Report"));
		        $this->add_lapp_function(1, _("&Application Form Payment Report"), "paymentmanager/report/application_payment_report.php?", 'SS_SMS_DAY_WISE_PAYMNT_REP', MENU_ENTRY);              
                $this->add_lapp_function(1, _("&Day Wise Payment Report"), "paymentmanager/report/payment_report.php?", 'SA_SALESORDER', MENU_ENTRY);
				
		  $this->add_module(_("Setup"));
                                $this->add_lapp_function(2, _("&Payment Head Name"), "paymentmanager/setup/head_name_settings.php?", 'SA_SALESORDER', MENU_ENTRY);
				$this->add_lapp_function(2, _("&Payment Head Setting"), "paymentmanager/setup/head_settings.php?", 'SA_SALESORDER', MENU_ENTRY);

        $this->add_extensions();
        
    }

}

?>