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
 * FUEL Validator Helper Test
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/validator_helper
 */

// ------------------------------------------------------------------------

class Validator_helper_test extends Tester_base
{

    public function setup()
    {
        parent::setup();
        $this->CI->load->helper('validator');
    }

    // ------------------------------------------------------------------------

    public function test_is_outside_below_lower_limit()
    {
        $actual = is_outside(0, 1, 3);
        $expected = TRUE;
        $this->run($actual, $expected, 'is_outside: below lower limit');
    }

    // ------------------------------------------------------------------------

    public function test_is_outside_equals_lower_limit()
    {
        $actual = is_outside(1, 1, 3);
        $expected = FALSE;
        $this->run($actual, $expected, 'is_outside: equals lower limit');
    }

    // ------------------------------------------------------------------------

    public function test_is_outside_between_lower_and_upper_limit()
    {
        $actual = is_outside(2, 1, 3);
        $expected = FALSE;
        $this->run($actual, $expected, 'is_outside: between lower and upper limit');
    }

    // ------------------------------------------------------------------------

    public function test_is_outside_equals_upper_limit()
    {
        $actual = is_outside(3, 1, 3);
        $expected = FALSE;
        $this->run($actual, $expected, 'is_outside: equals upper limit');
    }

    // ------------------------------------------------------------------------

    public function test_is_outside_above_upper_limit()
    {
        $actual = is_outside(4, 1, 3);
        $expected = TRUE;
        $this->run($actual, $expected, 'is_outside: above upper limit');
    }

}

/* End of file Validator_helper_test.php */
/* Location: ./modules/fuel/tests/Validator_helper_test.php */
