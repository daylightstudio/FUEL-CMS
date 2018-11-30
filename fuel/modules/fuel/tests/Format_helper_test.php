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
 * FUEL Format Helper Test
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/format_helper
 */

// ------------------------------------------------------------------------

class Format_helper_test extends Tester_base {

	public function setup()
	{
		parent::setup();
		$this->CI->load->helper('format');
	}

	// ------------------------------------------------------------------------

	public function test_currency_nullValueSupplied()
	{
		$actual = currency(NULL);
		$expected = '$0.00';
		$this->run($actual, $expected, 'value NULL');
	}

	// ------------------------------------------------------------------------

	public function test_currency_nullSymbol()
	{
		$actual = currency('5', NULL);
		$expected = '5.00';
		$this->run($actual, $expected, 'symbol NULL');
	}

	// ------------------------------------------------------------------------

	public function test_currency_includeCentsFalse()
	{
		$actual = currency('5', '$', FALSE);
		$expected = '$5';
		$this->run($actual, $expected, 'include_cents FALSE');
	}

	// ------------------------------------------------------------------------

	public function test_currency_includeCentsNull()
	{
		$actual = currency('5', '$', NULL);
		$expected = '$5';
		$this->run($actual, $expected, 'include_cents NULL');
	}

	// ------------------------------------------------------------------------

	public function test_currency_customDecimalSeparator()
	{
		$actual = currency('5', '$', TRUE, ',');
		$expected = '$5,00';
		$this->run($actual, $expected, 'custom decimal separator');
	}

	// ------------------------------------------------------------------------

	public function test_currency_exceedsThousand()
	{
		$actual = currency('5000');
		$expected = '$5,000.00';
		$note = 'is thousands separator displayed when value exceeds 1000';
		$this->run($actual, $expected, 'default thousands separator', $note);
	}

	// ------------------------------------------------------------------------

	public function test_currency_customThousandsSeparator()
	{
		$actual = currency('5000', '$', TRUE, '.', 'a');
		$expected = '$5a000.00';
		$this->run($actual, $expected, 'custom thousands separator');
	}

}

/* End of file Format_helper_test.php */
/* Location: ./modules/fuel/tests/Format_helper_test.php */
