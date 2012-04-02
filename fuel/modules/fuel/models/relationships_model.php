<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once('base_module_model.php');

class Relationships_model extends Base_module_model {

	function __construct()
	{
		parent::__construct('fuel_relationships');
	}

}

class Relationship_model extends Base_module_record {
}