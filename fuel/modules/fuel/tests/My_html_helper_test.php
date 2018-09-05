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
 * FUEL My Html Helper Test
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/my_html_helper
 */

// ------------------------------------------------------------------------

class My_html_helper_test extends Tester_base
{

    public function setup()
    {
        parent::setup();
        $this->CI->load->helper('my_html');
    }

    // ------------------------------------------------------------------------

    public function test_tag_with_no_values()
    {
        $actual = tag('foo', array());
        $expected = '';
        $this->run($actual, $expected, 'tag with no values');
    }

    // ------------------------------------------------------------------------

    public function test_tag_with_single_value()
    {
        $actual = tag('foo', 'bar');
        $expected = '<foo>bar</foo>';
        $this->run($actual, $expected, 'tag with single value');
    }

    // ------------------------------------------------------------------------

    public function test_tag_with_single_attribute()
    {
        $actual = tag('foo', 'bar', 'class="baz"');
        $expected = '<foo class="baz">bar</foo>';
        $this->run($actual, $expected, 'tag with single attribute');
    }

    // ------------------------------------------------------------------------

    public function test_tag_with_empty_attribute()
    {
        $actual = tag('foo', 'bar', '');
        $expected = '<foo>bar</foo>';
        $this->run($actual, $expected, 'tag with empty attribute');
    }

    // ------------------------------------------------------------------------

    public function test_tag_multiple_values()
    {
        $actual = tag('foo', array('bar', 'baz'));
        $actual = strip_whitespace($actual);
        $expected = '<foo>bar</foo><foo>baz</foo>';
        $this->run($actual, $expected, 'tag with multiple values');
    }

    // ------------------------------------------------------------------------

    public function test_html_attrs_empty_array()
    {
        $actual = html_attrs(array());
        $expected = '';
        $this->run($actual, $expected, 'html_attrs with empty attributes array');
    }

    // ------------------------------------------------------------------------

    public function test_html_attrs_one_attribute_in_array()
    {
        $actual = html_attrs(array('foo' => 'bar'));
        $expected = ' foo="bar"';
        $this->run($actual, $expected, 'html_attrs with one attribute in array');
    }

    // ------------------------------------------------------------------------

    public function test_html_attrs_multiple_attributes_in_array()
    {
        $actual = html_attrs(array('foo' => 'bar', 'baz' => 'qux'));
        $expected = ' foo="bar" baz="qux"';
        $this->run($actual, $expected, 'html_attrs with multiple attributes in array');
    }

    // ------------------------------------------------------------------------

    public function test_html_attrs_data_attribute_in_array()
    {
        $actual = html_attrs(array('data' => array('foo' => 'bar')));
        $expected = ' data-foo="bar"';
        $this->run($actual, $expected, 'html_attrs with data attribute in array');
    }

    // ------------------------------------------------------------------------

    public function test_html_attrs_mix_of_data_and_non_data_attributes_in_array()
    {
        $actual = html_attrs(array(
            'foo' => 'bar',
            'data' => array('baz' => 'qux', 'quux' => 'quuz'),
            'corge' => 'grault'
        ));
        $expected = ' foo="bar" data-baz="qux" data-quux="quuz" corge="grault"';
        $this->run($actual, $expected, 'html_attrs with data attribute in array');
    }

}

/* End of file My_html_helper_test.php */
/* Location: ./modules/fuel/tests/My_html_helper_test.php */
