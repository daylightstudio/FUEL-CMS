<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('Fuel_test_base.php');

/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2018, Daylight Studio LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL Google Helper Test
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/format_helper
 */

// ------------------------------------------------------------------------

class Google_helper_test extends Tester_base
{
	private $data = array();

	public function setup()
	{
		parent::setup();
		$this->CI->load->helper('google');

		$this->data = array(
			'address_components' => array(
				array(
					'types' => array('route'),
					'long_name' => 'route long name',
					'short_name' => 'route short name',
				),
			),
		);
	}

	// ------------------------------------------------------------------------

	public function test_google_geolocate_return_address_components()
	{
		$actual = google_geolocate($this->data, 'address_components');
		$expected = $this->data['address_components'];
		$this->run($actual, $expected, 'google_geolocate return address_components');
	}

	// ------------------------------------------------------------------------

	public function test_google_geolocate_return_route_long()
	{
		$actual = google_geolocate($this->data, 'route', TRUE);
		$expected = 'route long name';
		$this->run($actual, $expected, 'google_geolocate return route (long)');
	}

	// ------------------------------------------------------------------------

	public function test_google_geolocate_return_route_short()
	{
		$actual = google_geolocate($this->data, 'route', FALSE);
		$expected = 'route short name';
		$this->run($actual, $expected, 'google_geolocate return route (short)');
	}

}

/* End of file Google_helper_test.php */
/* Location: ./modules/fuel/tests/Google_helper_test.php */
