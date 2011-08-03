<?php

          $menu = "";
	  $menu .= "<li><a href=\"".url::site()."user/signup/\" ";
	  $menu .= ($this_page == 'signup') ? " class=\"active\"" : "";
	  $menu .= ">Sign Up/Sign In</a></li>"; //.Kohana::lang('ui_main.submit'). TODO
          echo $menu;

?>