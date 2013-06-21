<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Typography Class
 *
 *
 * @access		private
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/
 */
class MY_Typography extends CI_Typography {

	// Block level elements that should not be wrapped inside <p> tags
	var $sometimes_block_elements = 'li';

	/**
	 * Auto Typography
	 *
	 * This function converts text, making it typographically correct:
	 *	- Converts double spaces into paragraphs.
	 *	- Converts single line breaks into <br /> tags
	 *	- Converts single and double quotes into correctly facing curly quote entities.
	 *	- Converts three dots into ellipsis.
	 *	- Converts double dashes into em-dashes.
	 *  - Converts two spaces into entities
	 *
	 * @access	public
	 * @param	string
	 * @param	bool	whether to reduce more then two consecutive newlines to two
	 * @param	bool	allows you to specify not to format special characters like quotes. Helps with templating syntax (CHANGED BY DAYLIGHT July 20, 2012)
	 * @return	string
	 */
	public function auto_typography($str, $reduce_linebreaks = FALSE)
	{
		if ($str == '')
		{
			return '';
		}

		// Standardize Newlines to make matching easier
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}

		// Reduce line breaks.  If there are more than two consecutive linebreaks
		// we'll compress them down to a maximum of two since there's no benefit to more.
		if ($reduce_linebreaks === TRUE)
		{
			$str = preg_replace("/\n\n+/", "\n\n", $str);
		}

		// HTML comment tags don't conform to patterns of normal tags, so pull them out separately, only if needed
		$html_comments = array();
		if (strpos($str, '<!--') !== FALSE)
		{
			if (preg_match_all("#(<!\-\-.*?\-\->)#s", $str, $matches))
			{
				for ($i = 0, $total = count($matches[0]); $i < $total; $i++)
				{
					$html_comments[] = $matches[0][$i];
					$str = str_replace($matches[0][$i], '{@HC'.$i.'}', $str);
				}
			}
		}

		// match and yank <pre> tags if they exist.  It's cheaper to do this separately since most content will
		// not contain <pre> tags, and it keeps the PCRE patterns below simpler and faster
		if (strpos($str, '<pre') !== FALSE)
		{
			$str = preg_replace_callback("#<pre.*?>.*?</pre>#si", array($this, '_protect_characters'), $str);
		}

		// Convert quotes within tags to temporary markers.
		$str = preg_replace_callback("#<.+?>#si", array($this, '_protect_characters'), $str);

		// Do the same with braces if necessary
		if ($this->protect_braced_quotes === TRUE)
		{
			$str = preg_replace_callback("#\{.+?\}#si", array($this, '_protect_characters'), $str);
		}

		// Convert "ignore" tags to temporary marker.  The parser splits out the string at every tag
		// it encounters.  Certain inline tags, like image tags, links, span tags, etc. will be
		// adversely affected if they are split out so we'll convert the opening bracket < temporarily to: {@TAG}
		$str = preg_replace("#<(/*)(".$this->inline_elements.")([ >])#i", "{@TAG}\\1\\2\\3", $str);

		// Split the string at every tag.  This expression creates an array with this prototype:
		//
		//	[array]
		//	{
		//		[0] = <opening tag>
		//		[1] = Content...
		//		[2] = <closing tag>
		//		Etc...
		//	}
		$chunks = preg_split('/(<(?:[^<>]+(?:"[^"]*"|\'[^\']*\')?)+>)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

		// Build our finalized string.  We cycle through the array, skipping tags, and processing the contained text
		$str = '';
		$process = TRUE;
		$paragraph = FALSE;
		$current_chunk = 0;
		$total_chunks = count($chunks);

		foreach ($chunks as $chunk)
		{
			$current_chunk++;

			// Are we dealing with a tag? If so, we'll skip the processing for this cycle.
			// Well also set the "process" flag which allows us to skip <pre> tags and a few other things.
			if (preg_match("#<(/*)(".$this->block_elements.").*?>#", $chunk, $match))
			{
				if (preg_match("#".$this->skip_elements."#", $match[2]))
				{
					$process =  ($match[1] == '/') ? TRUE : FALSE;
				}

				if ($match[1] == '')
				{
					$this->last_block_element = $match[2];
				}

				$str .= $chunk;
				continue;
			}

			if ($process == FALSE)
			{
				$str .= $chunk;
				continue;
			}

			//  Force a newline to make sure end tags get processed by _format_newlines()
			if ($current_chunk == $total_chunks)
			{
				$chunk .= "\n";
			}

			//  Convert Newlines into <p> and <br /> tags
			$str .= $this->_format_newlines($chunk);
		}

		// No opening block level tag?  Add it if needed.
		if ( ! preg_match("/^\s*<(?:".$this->block_elements.")/i", $str))
		{
			$str = preg_replace("/^(.*?)<(".$this->block_elements.")/i", '<p>$1</p><$2', $str);
		}

		// Convert quotes, elipsis, em-dashes, non-breaking spaces, and ampersands
		$str = $this->format_characters($str);

		// restore HTML comments
		for ($i = 0, $total = count($html_comments); $i < $total; $i++)
		{
			// remove surrounding paragraph tags, but only if there's an opening paragraph tag
			// otherwise HTML comments at the ends of paragraphs will have the closing tag removed
			// if '<p>{@HC1}' then replace <p>{@HC1}</p> with the comment, else replace only {@HC1} with the comment
			$str = preg_replace('#(?(?=<p>\{@HC'.$i.'\})<p>\{@HC'.$i.'\}(\s*</p>)|\{@HC'.$i.'\})#s', $html_comments[$i], $str);
		}

		// Final clean up
		$table = array(

						// If the user submitted their own paragraph tags within the text
						// we will retain them instead of using our tags.
						'/(<p[^>*?]>)<p>/'	=> '$1', // <?php BBEdit syntax coloring bug fix

						// Reduce multiple instances of opening/closing paragraph tags to a single one
						'#(</p>)+#'			=> '</p>',
						'/(<p>\W*<p>)+/'	=> '<p>',

						// Clean up stray paragraph tags that appear before block level elements
						'#<p></p><('.$this->block_elements.')#'	=> '<$1',

						// Clean up stray non-breaking spaces preceeding block elements
						'#(&nbsp;\s*)+<('.$this->block_elements.'|'.$this->sometimes_block_elements.')#i'	=> '  <$2',

						// Replace the temporary markers we added earlier
						'/\{@TAG\}/'		=> '<',
						'/\{@DQ\}/'			=> '"',
						'/\{@SQ\}/'			=> "'",
						'/\{@DD\}/'			=> '--',
						'/\{@NBS\}/'		=> '  ',

						// An unintended consequence of the _format_newlines function is that
						// some of the newlines get truncated, resulting in <p> tags
						// starting immediately after <block> tags on the same line.
						// This forces a newline after such occurrences, which looks much nicer.
						"/><p>\n/"			=> ">\n<p>",

						// Similarly, there might be cases where a closing </block> will follow
						// a closing </p> tag, so we'll correct it by adding a newline in between
						"#</p></#"			=> "</p>\n</"
						);

		// Do we need to reduce empty lines?
		if ($reduce_linebreaks === TRUE)
		{
			$table['#<p>\n*</p>#'] = '';
		}
		else
		{
			// If we have empty paragraph tags we add a non-breaking space
			// otherwise most browsers won't treat them as true paragraphs
			$table['#<p></p>#'] = '<p>&nbsp;</p>';
		}

		$str = preg_replace(array_keys($table), $table, $str);

		// ADDED BY David McReynolds of Daylight Studio 7/12/2012 to deal with DWOO parsing errors
		// Examples:
		// {safe_mailto(&#8220;my_email@example.com&#8221;)}
		// {safe_mailto(&#8216;my_email@example.com&#8217;, &#8216;my_email@example.com&#8217;)}
		if (strpos($str, '{') !== FALSE)
		{
			$str = preg_replace_callback("#(.*\{\w+\()(.+)(\)\})#Ui", array($this, '_escape_template_syntax'), $str);
		}
		return $str;

	}

	public function _escape_template_syntax($matches)
	{
		static $search;
		static $replace;

		if (!isset($search))
		{
			$search = array('&#8220;', '&#8221;', '&#8216;', '&#8217;');
		}

		if (!isset($replace))
		{
			$replace = array('"', '"', '\'', '\'');
		}

		$output = $matches[0];
		if (!empty($matches[2]))
		{
			$output = $matches[1].str_replace($search, $replace, $matches[2]).$matches[3];
		}
		return $output;
	}


}
// END Typography Class

/* End of file MY_Typography.php */
/* Location: ./modules/fuel/libraries/MY_Typography.php */
