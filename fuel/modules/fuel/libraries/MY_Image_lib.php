<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * A CodeIgniter MY_Image_lib Class Extension
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/my_image_lib
 */


class MY_Image_lib extends CI_Image_lib {
	
	protected $_width;
	protected $_height;
	protected $_thumb_marker = '';
	protected $_create_thumb = FALSE;
	protected $_save_props = array('width', 'height', 'thumb_marker', 'create_thumb');
	
	public function __construct($props = array())
	{
        parent::__construct($props);
    }

	// --------------------------------------------------------------------

	/**
	 * Initializes the Image class
	 *
	 * @access	public
	 * @param	array	an associative array of key/values
	 * @return	array
	 */	
	public function initialize($props = array())
	{
		foreach($this->_save_props as $sp)
		{
			$p = '_'.$sp;
			if (isset($props[$sp])) $this->$p = $props[$sp];
		}
		parent::initialize($props);
	}
	

	// --------------------------------------------------------------------

	/**
	 * Resize and crop an image
	 *
	 * @access	public
	 * @return	array
	 */	
	public function resize_and_crop()
	{
		// need these set
		$this->maintain_ratio = TRUE;
		
		foreach($this->_save_props as $sp)
		{
			$p = '_'.$sp;
			$this->$sp = $this->$p;
		}

		// get the current dimensions and see if it is a portrait or landscape image
		$props = $this->get_image_properties($this->source_folder.$this->source_image, TRUE);
		$orig_width = $props['width'];
		$orig_height = $props['height'];

		// if ($orig_width < $orig_height )
		// {
		// 	$this->master_dim = 'width';
		// }
		// 

		if (empty($this->width))
		{
			$this->width = $orig_width;
		}

		if (empty($this->height))
		{
			$this->height = $orig_height;
		}

		if (empty($orig_width) OR empty($orig_height))
		{
			return FALSE;
		}
		
		$w_ratio = $this->width/$orig_width;
		$h_ratio = $this->height/$orig_height;
		if ($w_ratio > $h_ratio)
		{
			$this->master_dim = 'width';
		}
		else
		{
			$this->master_dim = 'height';
		}
		
		$this->image_reproportion();

		// resize image
		if (!$this->resize())
		{
			// TODO... put in lang file
			$this->set_error('Could not resize');
		}
		
		if (!empty($this->dest_image))
		{
			// now crop if it is too wide
			$thumb_src = $this->explode_name($this->dest_image);
			$thumb_source = $thumb_src['name'].$this->thumb_marker.$thumb_src['ext'];
			$new_source = $this->dest_folder.$thumb_source;
			$props = $this->get_image_properties($new_source, TRUE);
			$new_width = $props['width'];
			$new_height = $props['height'];
			
			$config = array();
			$config['width'] = $this->_width;
			$config['height'] = $this->_height;
			$config['source_image'] = $new_source;
			$config['maintain_ratio'] = FALSE;
			$config['create_thumb'] = FALSE;


			// portrait
			if ($new_width < $new_height)
			{
				$this->x_axis = 0;
				$this->y_axis = round(($new_height - $this->_height)/2);
			}

			// landscape
			else
			{
				$this->x_axis = round(($new_width - $this->_width)/2);
				$this->y_axis = 0;
			}
			$this->dest_folder = '';
			$this->initialize($config);
			if (!$this->crop())
			{
				// TODO... put in lang file
				$this->set_error('Could not crop');
			}
		}
		else
		{
			// TODO... put in lang file
			$this->set_error('Could not crop');
		}

		return (empty($this->error_msg));
		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Converts from file types
	 * 
	 * Originally from: http://codeigniter.com/forums/viewthread/145527/
	 *
	 * @access	public
	 * @param	array	an associative array of key/values
	 * @param	boolean	indicates whether to remove empty array values from uri
	 * @return	array
	 */	
	public function convert($type = 'jpg', $delete_orig = FALSE)
	{
		$this->full_dst_path = $this->dest_folder . end($this->explode_name($this->dest_image)) . '.' . $type;

		if (!($src_img = $this->image_create_gd()))
		{
		    return FALSE;
		}

		if ($this->image_library == 'gd2' AND function_exists('imagecreatetruecolor'))
		{
		    $create = 'imagecreatetruecolor';
		    $copy = 'imagecopyresampled';
		}
		else
		{
		    $create = 'imagecreate';
		    $copy = 'imagecopyresized';
		}

		$dst_img = $create($this->width, $this->height);
		$copy($dst_img, $src_img, 0, 0, 0, 0, $this->width, $this->height, $this->width, $this->height);

		$types = array('gif' => 1, 'jpg' => 2, 'jpeg' => 2, 'png' => 3);

		$this->image_type = $types[$type];

		if ($delete_orig)
		{
		    unlink($this->full_src_path);        
		}

		if ($this->dynamic_output == TRUE)
		{
		    $this->image_display_gd($dst_img);
		}
		else
		{
		    if (!$this->image_save_gd($dst_img))
		    {
		        return FALSE;
		    }
		}

		imagedestroy($dst_img);
		imagedestroy($src_img);

		@chmod($this->full_dst_path, DIR_WRITE_MODE);

		return TRUE;
	}
}


/* End of file MY_Image_list.php */
/* Location: ./modules/fuel/libraries/MY_Image_list.php */