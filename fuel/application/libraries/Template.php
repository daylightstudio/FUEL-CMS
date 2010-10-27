<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* Template Engine.
* 
* Has 2 modes of use:
* 1: Using a container template with template parts (blocks) inside.
* 2: Using template parts only (header,content,footer. etc).
* 
* Allows any data part to be re-assigned to all parts (global data).
* Allows a template source directory to be specified.
* Can be used with Matchbox modules.
* 
* In future: Output could render PDF files from HTML content.
* http://codeigniter.com/forums/viewthread/67028/
$this->template->assign_tpl( array(
			'header'	   => 'header',	  // name of the content part => file name to load
			'header_write' => 'header_write',
			'sidebar'	  => 'sidebar_posts_form',
			'editorjs'	 => 'editorjs',
			'posts_form'   => 'posts_form',
			'footer'	   => 'footer',
	  ));
			
	  $this->template->assign_global( array(	// all the above parts have access to this data
			'postid'		  => $this->postID,
			'users'		   => $users,
			'categories'	  => $categories,
			'writestyle'	  => "active",
			'writepostsstyle' => "active",
			'pagetitle'	   => "Administration Panel - Manage Posts",
			'categoryids'	 => $categoryids,
			'directory'	   => 'admin/',	   // the template source directory
	  ));
			
			$this->template->render();
*/

class Template 
{
	 var $file, $data;
	
	/**
	 * Constructor
	 *
	 * @param	string	the file name of the template to load
	 */
	function Template($file = null) 
	{
		$this->file = $file;
		$this->data = array
		(
			'module'	=> null,	// use with Matchbox modules
			'directory' => null,
		);
	}
	
	/**
	 * Assign data to this template object.
	 * 
	 * @param	 string
	 * @param	 array or string
	 */
	function assign($key, $value = null) 
	{
		if (is_array($key)) 
		{
			foreach ($key as $k => $v) 
			{
				$this->data[$k] = $v;
			}
		}
		else
		{
			$this->data[$key] = $value;
		}
	}
	
	/**
	 * Assign data to a specific template object.
	 * 
	 * @param	string
	 * @param	 string
	 * @param	 array or string
	 */	
	function assign_to($tpl, $key, $value = null)
	{
		$this->data[$tpl]->assign($key, $value);
	}
	
	/**
	 * Assign data to all template objects.
	 * 
	 * @param	 string
	 * @param	 array or string
	 */
	function assign_global($key, $value = null)
	{
			foreach ($this->data as $k => $v)	 // iterate through data
			{
				
				if (get_class($v) == 'Template') // if data is template object
				{
					$v->assign($key, $value);	 // assign to it
				}
			}
			$this->assign($key, $value);		 // assign to this template also
	}
	
	/**
	 * Assign new template object parts to this template.
	 * 
	 * @param	 string
	 * @param	 array or string
	 */
	   function assign_tpl($tpl, $file = null)
	   {
		if (is_array($tpl))
		{
			foreach ($tpl as $k => $v)
			{ 
				$this->assign($k, new Template($v));
			} 
		}
		else
		{
			$this->assign($tpl, new Template($file));
		}   
	}
  
	/**
	 * fetch a specific template data value.
	 *
	 * @param	 string	the template name
	 * @param	string	the data key
	 * @return	 string	 
	 */
	  function fetch($tpl, $key)
	  {
			return $this->data[$tpl]->data[$key];	
	  }
		
	/**
	 * Build the HTML output.
	 *
	 * @param	 bool	set the output mode
	 * @return	 string	 the rendered template
	 */
	function render($return = FALSE) 
	{		
		$out = '';
		ob_start();

		// iterate through data
		foreach ($this->data as $k => $v)				
		{
			 // if data is template object
			if (is_object($v) && strtolower(get_class($v)) == 'template')
			{
				// render it
				$this->data[$k] = $v->render(TRUE);

				// output the part
				if (!$this->file) echo $this->data[$k];
			}
		}

		if ($this->file) echo $this->out(TRUE);
		$buffer = ob_get_contents();
		@ob_end_clean();
		if ($return) return $buffer;
		echo $buffer;
	}
   
	/**
	 * render this template.
	 *
	 * @param	 bool	set the output mode
	 * @return	 string	 the rendered template
	 */
	function out($return = FALSE)
	{		
		$CI = & get_instance();
		return $CI->load->view($this->data['directory'].$this->file, $this->data, $return, $this->data['module']);
	}
}
/* End of file Template.php */
/* Location: ./application/libraries/Template.php */