<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Menu_test extends Tester_base {
	
	public $init = array();
	private $nav = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->CI->load->library('menu');
	}
	
	public function setup()
	{
		$this->nav = array();
		$this->nav['about'] = 'About';
		$this->nav['about/history'] = array('label' => 'History', 'parent_id' => 'about');
		$this->nav['about/contact'] = array('label' => 'Contact', 'parent_id' => 'about');

		$this->nav['products'] = 'Products';
		$this->nav['products/X3000'] = array('label' => 'X3000', 'parent_id' => 'products');

	}
	
	public function test_basic()
	{
		
		/*******************************************
		basic test
		********************************************/ 
		$active = 'about/history';
		$menu = $this->CI->menu->render($this->nav, $active);
		$test =  strip_whitespace($menu);
		$str = '
<ul>
	<li class="first active"><a href="'.site_url('about').'" title="About">About</a>
	<ul>
		<li class="first active"><a href="'.site_url('about/history').'" title="History">History</a></li>
		<li class="last"><a href="'.site_url('about/contact').'" title="Contact">Contact</a></li>
	</ul>
	</li>

	<li class="last"><a href="'.site_url('products').'" title="Products">Products</a>
	<ul>
		<li class="first last"><a href="'.site_url('products/X3000').'" title="X3000">X3000</a></li>
	</ul>
	</li>
</ul>';
		$expected = strip_whitespace($str);
		$this->run($test, $expected, 'Menu basic test', $test, $expected);
	}
	
	function test_collapsible()
	{
		/*******************************************
		basic test
		********************************************/ 
		$active = 'about/history';
		$menu = $this->CI->menu->render($this->nav, $active, NULL, 'collapsible');
		$test =  strip_whitespace($menu);
		$str = '
<ul>
	<li class="active"><a href="'.site_url('about').'">About</a>
	<ul>
		<li class="active"><a href="'.site_url('about/history').'">History</a></li>
		<li class="last"><a href="'.site_url('about/contact').'" title="Contact">Contact</a></li>
	</ul>
	</li>

	<li class="last"><a href="'.site_url('products').'" title="Products">Products</a></li>
</ul>';

		$expected = strip_whitespace($str);
		$this->run($test, $expected, 'Menu collapsible test');
		
	}

		function test_breadcrumb()
		{
			/*******************************************
			basic test
			********************************************/ 
			$active = 'about/history';
			$menu = $this->CI->menu->render($this->nav, $active, NULL, 'breadcrumb');
			$test =  strip_whitespace($menu);
			$str = '
<ul>
	<li><a href="'.site_url().'">Home</a> <span class="arrow"> &gt; </span> </li>
	<li><a href="'.site_url('about').'">About</a> <span class="arrow"> &gt; </span> </li>
	<li>History</li>
</ul>';
			$expected = strip_whitespace($str);
			$this->run($test, $expected, 'Menu breadcrumb test');

		}


		function test_page_title()
		{
			/*******************************************
			basic test
			********************************************/ 
			$active = 'about/history';
			$menu = $this->CI->menu->render($this->nav, $active, NULL, 'page_title');
			$test =  strip_whitespace($menu);

			$str = <<<EOD
Home &gt; About &gt; History
EOD;
			$expected = strip_whitespace($str);
			$this->run($test, $expected, 'Menu page title test');


			// test reverse order
			$this->CI->menu->reset();
			$this->CI->menu->order = 'desc';
			$this->CI->menu->delimiter = ' : ';
			$menu = $this->CI->menu->render($this->nav, $active, NULL, 'page_title');
			$test = strip_whitespace($menu);
			
			$str = <<<EOD
			History : About : Home
EOD;
			$expected = strip_whitespace($str);
			$this->run($test, $expected, 'Menu page title test in reverse order');


			// test just homepage
			$this->CI->menu->reset();
			$menu = $this->CI->menu->render($this->nav, '', NULL, 'page_title');
			$test = strip_whitespace($menu);
			$this->run($test, 'Home', 'Menu page title test for homepage');

		}
		
		function test_hidden_items()
		{
			$this->nav['about'] = 'About';
			$this->nav['about/history']['hidden'] = TRUE;
			$this->nav['about/contact']['hidden'] = TRUE;
			$this->nav['products'] = 'Products';
			$this->nav['products/X3000']['hidden'] = TRUE;
			$this->CI->menu->reset();
			$menu = $this->CI->menu->render($this->nav);
			$test =  strip_whitespace($menu);
			
			$str = '
			<ul>
				<li class="first"><a href="'.site_url('about').'" title="About">About</a></li>
				<li class="last"><a href="'.site_url('products').'" title="Products">Products</a></li>
			</ul>';
			
			$expected = strip_whitespace($str);
			
			$this->run($test, $expected, 'Test hidden items');
		}
}
