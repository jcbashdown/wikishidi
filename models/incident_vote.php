<?php defined('SYSPATH') OR die('No direct access allowed.');

class Incident_Vote_Model extends ORM {

	// Relationships
	protected $belongs_to = array('wikishidi_user');
        // TODO - has 2 wikishidi incidents

        // Database table name
	protected $table_name = 'incident_vote';

}

?>