<?php defined('SYSPATH') OR die('No direct access allowed.');

class User_Vote_Model extends ORM {

	// Relationships
	protected $belongs_to = array('wikishidi_user');
        // TODO - has wikishidi incident

        // Database table name
	protected $table_name = 'user_vote';

}

?>