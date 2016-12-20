<?php
/**********************************************************************
  developed by Mannan
***********************************************************************/
if (!isset($path_to_root) || isset($_GET['path_to_root']) || isset($_POST['path_to_root']))
	die("Restricted access");
	
	include_once($path_to_root . '/applications/application.php');
	include_once($path_to_root . '/applications/admission.php');
	include_once($path_to_root . '/applications/exammanager.php');
	include_once($path_to_root . '/applications/resultmanager.php');
	include_once($path_to_root . '/applications/attendance.php');
	include_once($path_to_root . '/applications/payment.php');
	include_once($path_to_root . '/applications/reportmanager.php');
	include_once($path_to_root . '/applications/cpanel.php');
	include_once($path_to_root . '/applications/library.php');
	include_once($path_to_root . '/applications/sms.php');
	include_once($path_to_root . '/applications/hostel.php');
	include_once($path_to_root . '/applications/event.php');
//	include_once($path_to_root . '/applications/customers.php');
	//include_once($path_to_root . '/applications/suppliers.php');
	//include_once($path_to_root . '/applications/inventory.php');
	//include_once($path_to_root . '/applications/manufacturing.php');
	//include_once($path_to_root . '/applications/dimensions.php');
	include_once($path_to_root . '/applications/generalledger.php');
	include_once($path_to_root . '/applications/setup.php');
	include_once($path_to_root . '/installed_extensions.php');

	class front_accounting
		{
		var $user;
		var $settings;
		var $applications;
		var $selected_application;

		var $menu;

		function front_accounting()
		{
		}
		function add_application(&$app)
				{	
					if ($app->enabled) // skip inactive modules
						$this->applications[$app->id] = &$app;
				}
		function get_application($id)
				{
				 if (isset($this->applications[$id]))
					return $this->applications[$id];
				 return null;
				}
		function get_selected_application()
		{
			if (isset($this->selected_application))
				 return $this->applications[$this->selected_application];
			foreach ($this->applications as $application)
				return $application;
			return null;
		}
		function display()
		{
			global $path_to_root;
			
			include_once($path_to_root . "/themes/".user_theme()."/renderer.php");

			$this->init();
			$rend = new renderer();
			$rend->wa_header();

			$rend->display_applications($this);

			$rend->wa_footer();
			$this->renderer =& $rend;
		}
		function init()
		{

			$this->menu = new menu(_("Main  Menu"));
			$this->menu->add_item(_("Main  Menu"), "index.php");
			$this->menu->add_item(_("Logout"), "/account/access/logout.php");
			$this->applications = array();
			$this->add_application(new admission_app());
			$this->add_application(new exammanager_app());
			$this->add_application(new resultmanager_app());
			$this->add_application(new attendancemanager_app());
			$this->add_application(new paymentmanager_app());
			$this->add_application(new reportmanager_app());
			$this->add_application(new cpanel_app());
			$this->add_application(new library_app());
			$this->add_application(new sms_app());
			$this->add_application(new hostel_app());
			$this->add_application(new event_app());
//			$this->add_application(new customers_app());
			//$this->add_application(new suppliers_app());
			//$this->add_application(new inventory_app());
			//$this->add_application(new manufacturing_app());
			//$this->add_application(new dimensions_app());
			$this->add_application(new general_ledger_app());
			
			hook_invoke_all('install_tabs', $this);

			$this->add_application(new setup_app());
		}
}
?>