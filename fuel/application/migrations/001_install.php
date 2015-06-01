<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_install extends CI_Migration {

	public function up()
	{
		// THIS IS JUST A PLACEHOLDER!!!... PUT IN YOUR OWN CODE HERE
		$file_path = APPPATH.'../install/fuel_schema.sql';
		$this->db->load_sql($file_path);
	}

	public function down()
	{
	}
}