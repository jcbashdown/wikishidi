<?php
class User_Controller extends Main_Controller {
        
        function __construct()
	{
		parent::__construct();
        }

//	public function _signup_button()
//	{
//
//	}

        public function signup($user_id = false, $saved = false )
	{
//      TODO - rid of edit through existing user id
        $this->session->create();
        $this->template->content = new View('wikishidi/signup');
        $this->template->header->header_block = $this->themes->header_block();
        $this->template->content->this_page = "signup";

//        if ($user_id)
//        {
//            $user_exists = ORM::factory('user')->find($user_id);
//            if ( ! $user_exists->loaded)
//            {
//                // Redirect
//                url::redirect(url::site().'admin/users/');
//            }
//        }

        // setup and initialize form field names
        $form = array
        (
            'username'  => '',
            'password'  => '',
            'password_again'  => '',
            'name'      => '',
            'email'     => '',
            'notify'    => '',
            'role'      => ''
        );

        //copy the form as errors, so the errors will be stored with keys corresponding to the form field names
        $errors = $form;
        $form_error = FALSE;
        $form_saved = FALSE;
        $form_action = "";
        $user = "";

        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            $post = new Validation($_POST);
//
//            //  Add some filters
            $post->pre_filter('trim', TRUE);
//
            $post->add_rules('username','required','length[3,16]', 'alpha');
//
//            //only validate password as required when user_id has value.
            $user_id == '' ? $post->add_rules('password','required',
                'length[5,16]','alpha_numeric'):'';
            $post->add_rules('name','required','length[3,100]');
//
            $post->add_rules('email','required','email','length[4,64]');
//
            $user_id == '' ? $post->add_callbacks('username',
                array($this,'username_exists_chk')) : '';
//
            $user_id == '' ? $post->add_callbacks('email',
                array($this,'email_exists_chk')) : '';
//
            // If Password field is not blank
            if (!empty($post->password))
            {
                $post->add_rules('password','required','length[5,16]'
                    ,'alpha_numeric','matches[password_again]');
            }

            $post->add_rules('role','required','length[3,30]', 'alpha_numeric');

            $post->add_rules('notify','between[0,1]');

            if ($post->validate())
            {
                $user = ORM::factory('user',$user_id);
                $user->name = $post->name;
                $user->email = $post->email;
                $user->notify = $post->notify;
                $user->username = $post->username;
                $user->password = $post->password;

                // Add New Roles
                $user->add(ORM::factory('role', 'login'));
                $user->add(ORM::factory('role', $post->role));
                
                $user->save();

                // Create wikishidi user TODO Do this in email send?
                $wikishidi_user = ORM::factory('wikishidi_user',$user_id);
//                do with add like role
                $wikishidi_user->user_id=$user->id;
                $wikishidi_user->confirm_code=text::random('alnum', 20);;
                $wikishidi_user->confirmed=FALSE;
                $wikishidi_user->reputation_people=0;
                $wikishidi_user->reputation_auto=0;
                $wikishidi_user->reputation_manual=0;
                $wikishidi_user->save();
                // TODO - if email fails then redirect with errors and don't save record
                if(!($this->_send_email($user->email, $wikishidi_user->confirm_code))){
                  $wikishidi_user->delete();
                  $user->delete();
//                  TODO redirect
                }

                // Redirect - do this here as should be part of session. TODO some session ness?
                // Is this correct? Should we just be going to that view or to confirm action (in which
                // case change name... Or are are going to view here in which case should all be one page
                $this->session->set('alert_email', $user->email);
                $this->template->content = new View('wikishidi/confirm');
                $this->template->content->this_page = "confirm";
                $user = ORM::factory('user')->find($user->id);
                $this->template->content->user = $user;
                return;
            }
            else
            {
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('auth'));
                $form_error = TRUE;
            }
        }
        else
        {
            if ( $user_id )
            {
                // Retrieve Current Incident
                $user = ORM::factory('user', $user_id);
                if ($user->loaded == true)
                {
                    foreach ($user->roles as $user_role)
                    {
                         $role = $user_role->name;
                    }

                    $form = array
                    (
                        'user_id'   => $user->id,
                        'username'  => $user->username,
                        'password'  => '',
                        'password_again'  => '',
                        'name'      => $user->name,
                        'email'     => $user->email,
                        'notify'    => $user->notify,
                        'role'      => $role
                    );
                }
            }
        }

        $roles = ORM::factory('role')
            ->where('id != 1')
            ->orderby('name', 'asc')
            ->find_all();

        $role_array = array("login" => "NONE");
        foreach ($roles as $role)
        {
            $role_array[$role->name] = strtoupper($role->name);
        }

        $this->template->content->user = $user;
        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->yesno_array = array('1'=>strtoupper(Kohana::lang('ui_main.yes')),'0'=>strtoupper(Kohana::lang('ui_main.no')));
        $this->template->content->role_array = $role_array;
	}

        private function _send_email($email, $code)
        {
          // Email Alerts, Confirmation Code
//          $code = text::random('alnum', 20);

          $settings = kohana::config('settings');

          $to = $email;
	  $from = array();
          $from[] = $settings['site_email'];
	  $from[] = $settings['site_name'];
          $subject = $settings['site_name']." sign up confirmation email";//Kohana::lang('alerts.verification_email_subject');TODO
          $message = "To confirm your accout please go to ".url::site().'wikishidi/verify/?c='.$code."&e=".$email;//TODO Kohana::lang('alerts.confirm_request')

          if (email::send($to, $from, $subject, $message, TRUE) == 1)
          {
            return TRUE;
          }

          return FALSE;
        }
        public function confirm_user(){

        $this->template->header->this_page = 'confirm_user';
        $this->template->content = new View('confirm_user');

//        TODO might be useful for mobile linking
//        $this->template->content->alert_mobile =
//            (isset($_SESSION['alert_mobile']) AND ! empty($_SESSION['alert_mobile'])) ?
//                $_SESSION['alert_mobile'] : "";
//Here - set session on confirm page and put confirm box/link to
        $this->template->content->email =
            (isset($_SESSION['alert_email']) AND ! empty($_SESSION['alert_email'])) ?
                $_SESSION['alert_email'] : "";
//TODO
       	// Display Mobile Option?
//        $this->template->content->show_mobile = TRUE;
//        $settings = ORM::factory('settings', 1);
//        if ( ! Kohana::config("settings.sms_provider"))
//        {
//            // Hide Mobile
//			$this->template->content->show_mobile = FALSE;
//        }

        // Rebuild Header Block
//        TODO will this work with new view names? With non theme views?
        $this->template->header->header_block = $this->themes->header_block();
            
        }
        
        public function verify()
        {
          // Define error codes for this view.
          define("ER_CODE_VERIFIED", 0);
          define("ER_CODE_NOT_FOUND", 1);
          define("ER_CODE_ALREADY_VERIFIED", 3);

          $code = (isset($_GET['c']) && !empty($_GET['c'])) ?
            $_GET['c'] : "";

          $email = (isset($_GET['e']) && !empty($_GET['e'])) ?
            $_GET['e'] : "";

          // INITIALIZE the content's section of the view
          $this->template->content = new View('user_verify');
          $this->template->header->this_page = 'user_verify';

          $filter = " ";
          $missing_info = FALSE;
          if ($_POST AND isset($_POST['code'])
            AND ! empty($_POST['code']))
          {
//            TODO may later come in handy to link mobile number to account
//            if (isset($_POST['alert_mobile']) AND ! empty($_POST['alert_mobile']))
//            {
//                $filter = "alert.alert_type=1 AND alert_code='".strtoupper($_POST['alert_code'])."' AND alert_recipient='".$_POST['alert_mobile']."' ";
//            }
            if (isset($_POST['email']) AND ! empty($_POST['email']))
            {
                $filter = "users.email='".$_POST['email']."' AND code='".$_POST['code']."'";
            }
            else
            {
                $missing_info = TRUE;
            }
          }
          else
          {
            if (empty($code) OR empty($email))
            {
                $missing_info = TRUE;
            }
            else
            {
                $filter = "users.email='".$email."' AND code='".$code."'";
            }
          }

          if ( ! $missing_info)
          {
            $user = ORM::factory('users')
                ->where($filter)
                ->find();

            // IF there was no result
            if ( ! $user->loaded)
            {
                $this->template->content->errno = ER_CODE_NOT_FOUND;
            }
//            TODO add confirmed to user
            elseif ($user->confirmed)
            {
                $this->template->content->errno = ER_CODE_ALREADY_VERIFIED;
            }
            else
            {
                // SET the alert as confirmed, and save it
                $user->set('confirmed', 1)->save();
                $this->template->content->errno = ER_CODE_VERIFIED;
            }
          }
          else
          {
            $this->template->content->errno = ER_CODE_NOT_FOUND;
          }

        // Rebuild Header Block
//        TODO will this work with new view names? With non theme views?
          $this->template->header->header_block = $this->themes->header_block();
        } // END function verify

//Copied from Ush source, why is this in the controller I'll never know.
    /**
     * Checks if username already exists.
     * @param Validation $post $_POST variable with validation rules
     */
    public function username_exists_chk(Validation $post)
    {
        $users = ORM::factory('user');
        // If add->rules validation found any errors, get me out of here!
        if (array_key_exists('username', $post->errors()))
            return;

        if ($users->username_exists($post->username))
            $post->add_error( 'username', 'exists');
    }

    /**
     * Check if
     */

    /**
     * Checks if email address is associated with an account.
     * @param Validation $post $_POST variable with validation rules
     */
    public function email_exists_chk( Validation $post )
    {
        $users = ORM::factory('user');
        if (array_key_exists('email',$post->errors()))
            return;

        if ($users->email_exists( $post->email ) )
            $post->add_error('email','exists');
    }
}
?>
