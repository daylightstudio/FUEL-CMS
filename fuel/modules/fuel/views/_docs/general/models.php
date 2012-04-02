<h1>Models</h1>
<p>There are two main classes FUEL provides for creating models. The first, <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model</a>, is an extension of the 
<a href="http://codeigniter.com/user_guide/general/models.html" target="_blank">CodeIgniter model</a> class that wraps logic around your table models.
The second, <a href="<?=user_guide_url('libraries/base_module_model')?>">Base_module_model</a>, extends <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model</a> and provides 
<a href="<?=user_guide_url('modules/simple')?>">simple module</a> specific methods and is the class you will most likely want to extend for your models. </p>

<p>Both classes allow you to create simple to complex form interfaces with the model's <dfn>MY_Model::form_fields()</dfn>
method, which gets passed to the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> object to generate the forms in the CMS.</p>

<h2>Example</h2>
<p>Below is an example of a quotes model (a simple module in the CMS), and the SQL for the table to house the quotes data.</p>

<pre class="brush:sql">
CREATE TABLE `quotes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `precedence` int(11) NOT NULL DEFAULT '0',
  `published` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
</pre>

<pre class="brush:php">
&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(FUEL_PATH.'models/base_module_model.php');

class Quotes_model extends Base_module_model {

	public $required = array('content');

	function __construct()
	{
		parent::__construct('quotes'); // table name
	}

	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc')
	{
		$this->db->select('id, SUBSTRING(content, 1, 200) as content, name, title, precedence, published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}

	function _common_query()
	{
		parent::_common_query(); // to do active and published
		$this->db->order_by('precedence desc');
	}
}

class Quote_model extends Base_module_record {
	
	function get_quote_formatted()
	{
		return quote($this->_fields['content'], $this->name, $this->title);
	}

}
?&gt;
</pre>

<p class="important">For a tutorial on creating simple modules, <a href="<?=user_guide_url('modules/tutorial')?>">click here</a>.</p>

