<?php defined('SYSPATH') OR die('No direct access allowed.');

class Wikishidi_User_Model extends ORM {

	// Relationships
	protected $has_one = array('user');
        protected $has_many = array('user_vote', 'incident_vote');

        // Database table name
	protected $table_name = 'wikishidi_user';

}

?>
