<?php
class Wikishidi_Install {

	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db =  new Database();
	}

	/**
	 * Creates the required database tables for my_plugin_name
	 */
	public function run_install()
	{
                // Make this work for admin users
		// Create the database tables
		// Include the table_prefix
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."wikishidi_user`
			(
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`user_id` INT NOT NULL COMMENT 'foreign key user_id',
                                FOREIGN KEY (user_id) REFERENCES user(id)
                                ON DELETE CASCADE,
				`confirm_code` VARCHAR(30) DEFAULT NULL COMMENT 'confirmation code to activate user account',
                                `confirmed` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'confirmation code to activate user account',
				PRIMARY KEY (id)
                                reputation_people INT NOT NULL COMMENT 'reputation derived from user votes counter cache',
                                reputation_auto INT NOT NULL COMMENT 'reputation derived from similar reports - auto verified counter cache'
                                reputation_manual INT NOT NULL COMMENT 'reputation derived from similar reports - manually selected counter cache'
			);");
//                TODO - user_votes join table - self ref user to user_report - more than one rel because through - to user and report..? No - bad normalisation cache with existing rels from recieving report - ORM
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."user_vote`
			(
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`wikishidi_user_id` INT NOT NULL COMMENT 'foreign key user_id',
                                FOREIGN KEY (wikishidi_user_id) REFERENCES wikishidi_user(id)
                                ON DELETE CASCADE,
                                `incident_id` INT NOT NULL COMMENT 'foreign key incident_id',
                                FOREIGN KEY (incident_id) REFERENCES incident(id)
                                ON DELETE CASCADE,
                                `supports` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'supports or contradicts?',
				PRIMARY KEY (id)
			);");
//                TODO - report_votes - self ref report to report orm - votes in cases - maybe floats not bool - see storm plan - cache with existing user rel from receiving report...
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."incident_vote`
			(
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`author_id` INT NOT NULL COMMENT 'foreign key user_id - will be the auto user if automatic',
                                FOREIGN KEY (author_id) REFERENCES wikishidi_user(id)
                                ON DELETE CASCADE,
                                `voter` INT NOT NULL COMMENT 'foreign key incident_id',
                                FOREIGN KEY (voter) REFERENCES incident(id)
                                ON DELETE CASCADE,
                                `votee` INT NOT NULL COMMENT 'foreign key incident_id',
                                FOREIGN KEY (votee) REFERENCES incident(id)
                                ON DELETE CASCADE,
                                `supports` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'supports or contradicts?',
				PRIMARY KEY (id)
			);");
	}

	/**
	 * Deletes the database tables for my_plugin_name
	 */
	public function uninstall()
	{
		$this->db->query("
			DROP TABLE ".Kohana::config('database.default.table_prefix')."wikishidi_user;
			");
                $this->db->query("
			DROP TABLE ".Kohana::config('database.default.table_prefix')."user_votes;
			");
                $this->db->query("
			DROP TABLE ".Kohana::config('database.default.table_prefix')."incident_votes;
			");
	}
}
?>
