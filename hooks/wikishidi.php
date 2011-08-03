<?php defined('SYSPATH') or die('No direct script access.');

class wikishidi {

	public function __construct()
	{
          // Hook into the main_sidebar event and call the Hello controller
          // and the method _say_hello within the Hello controller
          Event::add('system.pre_controller', array($this, 'add'));
	}

	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{

          Event::add('ushahidi_action.nav_main_top', array($this, '_signup_button'));
	}
        public function _signup_button()
	{
          $this_page = "";
          if (Router::$current_uri == "user/signup"){
            $this_page = "signup";
          }
	  $view = View::factory('wikishidi/signup_button');
          $view->this_page = $this_page;
          $view->render(TRUE);
//          TODO also to bottom of page
	}
}

new wikishidi;