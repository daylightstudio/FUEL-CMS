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
	<li class="first active"><a href="'.site_url('about').'">About</a>
	<ul>
		<li class="first active"><a href="'.site_url('about/history').'">History</a></li>
		<li class="last"><a href="'.site_url('about/contact').'">Contact</a></li>
	</ul>
	</li>

	<li class="last"><a href="'.site_url('products').'">Products</a>
	<ul>
		<li class="first last"><a href="'.site_url('products/X3000').'">X3000</a></li>
	</ul>
	</li>
</ul>';
		$expected = strip_whitespace($str);
		$this->run($test, $expected, 'Menu basic test');
	}
	
	public function test_collapsible()
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
		<li class="last"><a href="'.site_url('about/contact').'">Contact</a></li>
	</ul>
	</li>

	<li class="last"><a href="'.site_url('products').'">Products</a></li>
</ul>';

		$expected = strip_whitespace($str);
		$this->run($test, $expected, 'Menu collapsible test');
		
	}

		public function test_breadcrumb()
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


		public function test_page_title()
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
		
		public function test_delimited()
		{
			/*******************************************
			basic test
			********************************************/ 
			$this->CI->menu->reset();
			$active = NULL;
			$this->CI->menu->container_tag_id = 'footer_menu';
			$menu = $this->CI->menu->render($this->nav, $active, NULL, 'delimited');
			$test =  strip_whitespace($menu);
			$str = '<div id="footer_menu"><a href="'.site_url('about').'">About</a> &nbsp;|&nbsp; <a href="'.site_url('products').'">Products</a></div>';
			$expected = strip_whitespace($str);
			$this->run($test, $expected, 'Menu delimited test');

		}

		public function test_array()
		{
			/*******************************************
			basic test
			********************************************/ 
			$this->CI->menu->reset();
			$active = NULL;
			$menu = $this->CI->menu->render($this->nav, $active, NULL, 'array');
			$test =  $menu;
			$array = array (
			  'about' => 
			  array (
			    'id' => 'about',
			    'label' => 'About',
			    'location' => 'about',
			    'attributes' => 
			    array (
			    ),
			    'active' => NULL,
			    'parent_id' => NULL,
			    'hidden' => false,
			    'children' => 
			    array (
			      'about/history' => 
			      array (
			        'id' => 'about/history',
			        'label' => 'History',
			        'location' => 'about/history',
			        'attributes' => 
			        array (
			        ),
			        'active' => NULL,
			        'parent_id' => 'about',
			        'hidden' => false,
			      ),
			      'about/contact' => 
			      array (
			        'id' => 'about/contact',
			        'label' => 'Contact',
			        'location' => 'about/contact',
			        'attributes' => 
			        array (
			        ),
			        'active' => NULL,
			        'parent_id' => 'about',
			        'hidden' => false,
			      ),
			    ),
			  ),
			  'products' => 
			  array (
			    'id' => 'products',
			    'label' => 'Products',
			    'location' => 'products',
			    'attributes' => 
			    array (
			    ),
			    'active' => NULL,
			    'parent_id' => NULL,
			    'hidden' => false,
			    'children' => 
			    array (
			      'products/X3000' => 
			      array (
			        'id' => 'products/X3000',
			        'label' => 'X3000',
			        'location' => 'products/X3000',
			        'attributes' => 
			        array (
			        ),
			        'active' => NULL,
			        'parent_id' => 'products',
			        'hidden' => false,
			      ),
			    ),
			  ),
			);
			$expected = $array;
			$this->run($test, $expected, 'Menu array test');

		}

		public function test_hidden_items()
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
				<li class="first"><a href="'.site_url('about').'">About</a></li>
				<li class="last"><a href="'.site_url('products').'">Products</a></li>
			</ul>';
			
			$expected = strip_whitespace($str);
			
			$this->run($test, $expected, 'Test hidden items');
		}
}
