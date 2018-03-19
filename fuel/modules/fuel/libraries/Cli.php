<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------

/**
 * CLI Class
 *
 * A wrapper class around some common CLI functions.
 * Credit to:
 * https://github.com/philsturgeon/codeigniter-cli
 * http://stackoverflow.com/questions/187736/command-line-password-prompt-in-php
 * http://stackoverflow.com/questions/297850/is-it-really-not-possible-to-write-a-php-cli-password-prompt-that-hides-the-pass
 * And from Laravel CLI
 * 
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @link		http://docs.getfuelcms.com/libraries/cli
 */

class Cli
{

	/**
	 * Returns the input from the CLI
	 *
	 * @return	string
	 */
	public function input($length = 4096)
	{
   		if (!isset($GLOBALS['StdinPointer'])) 
		{ 
			$GLOBALS['StdinPointer'] = fopen("php://stdin","r"); 
		} 
		$line = fgets($GLOBALS['StdinPointer'], $length);
		return $line;
	}

	/**
	 * Outputs a string to the cli.	 If you send an array it will implode them
	 * with a line break.
	 *
	 * @param	string|array	$text	the text to output, or array of lines
	 */
	public function write($text = '', $new_line = TRUE)
	{
		if (is_array($text))
		{
			$text = implode(PHP_EOL, $text);
		}
		if ($new_line)
		{
			$text .= PHP_EOL;
		}

		fwrite(STDOUT, $text);
	}

	/**
	 * Outputs an error to the CLI using STDERR instead of STDOUT
	 *
	 * @param	string|array	$text	the text to output, or array of errors
	 */
	public function error($text = '')
	{
		if (is_array($text))
		{
			$text = implode(PHP_EOL, $text);
		}
		fwrite(STDERR, $text.PHP_EOL);
	}

	/**
	 * Enter a number of empty lines
	 *
	 * @param	integer	Number of lines to output
	 * @return	void
	 */
	public function new_line($num = 1)
	{
        // Do it once or more, write with empty string gives us a new line
        for($i = 0; $i < $num; $i++)
		{
			$this->write();
		}
    }

    // --------------------------------------------------------------------
	
	/**
	 * CLI prompts the user for a response and then returns the response
	 *
	 * @access	public
	 * @param	int Length of input value to read
	 * @return	string
	 */	
	public function prompt($msg, $length = 4096)
	{
		$this->write($msg." ", FALSE);
		$line = $this->input($length);
		return trim($line); 
	}

	// --------------------------------------------------------------------
	
	/**
	 * CLI silent prompt (for passwords) the user for a response and then returns the response
	 * http://stackoverflow.com/questions/187736/command-line-password-prompt-in-php
	 * http://stackoverflow.com/questions/297850/is-it-really-not-possible-to-write-a-php-cli-password-prompt-that-hides-the-pass
	 * And from Laravel CLI
	 *
	 * @access	public
	 * @param	int Length of input value to read
	 * @return	string
	 */	
	public function secret($msg, $length = 4096)
	{
		if (defined('PHP_WINDOWS_VERSION_BUILD')) 
		{
			$exe = FUEL_PATH.'libraries/resources/hiddeninput.exe';

			// handle code running from a phar
			if ('phar:' === substr(__FILE__, 0, 5)) {
			    $tmp_exe = sys_get_temp_dir().'/hiddeninput.exe';
			    copy($exe, $tmp_exe);
			    $exe = $tmp_exe;
			}
			$this->write($msg." ", FALSE);
			$value = rtrim(shell_exec($exe));

			if (isset($tmp_exe))
			{
			    unlink($tmp_exe);
			}
        }

        // doesn't work for new versions of Windows
		// if (preg_match('/^win/i', PHP_OS))
		// {
		// 	$vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
		// 	file_put_contents($vbscript, 'wscript.echo(InputBox("' . addslashes($prompt) . '", "", "password here"))');
		// 	$command = "cscript //nologo " . escapeshellarg($vbscript);
		// 	$value = rtrim(shell_exec($command));
		// 	unlink($vbscript);
		// }
		else
		{
			static $has_stty;
			if (is_null($has_stty))
			{
				exec('stty 2>&1', $output, $exitcode);
				$has_stty = $exitcode === 0;
			}

			if ($has_stty)
			{
				$this->write($msg." ", FALSE);
				$stty_mode = shell_exec('stty -g');
				shell_exec('stty -echo');
				$value = fgets(STDIN, $length);
				shell_exec(sprintf('stty %s', $stty_mode));

				if (false === $value)
				{
				    return FALSE;
				}
				$value = trim($value);
			}
			else
			{
				$command = "/usr/bin/env bash -c 'echo OK'";
				if (rtrim(shell_exec($command)) !== 'OK')
				{
					trigger_error("Can't invoke bash");
					return;
				}
				$command = "/usr/bin/env bash -c 'read -s -p \"". addslashes($msg) . "\" mypassword && echo \$mypassword'";
				$value = rtrim(shell_exec($command));
			}
		}
		return $value;
	}

	/**
	 * Returns whether or not PHP is in CLI mode or not
	 *
	 * @return	boolean
	 */
	public function is_cli()
	{
    	return (php_sapi_name() == 'cli' OR defined('STDIN')) ? TRUE : FALSE;
	}
}