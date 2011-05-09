<h1>Validator Helper</h1>

<p> This helper is a collection of functions that allows the user to validate various sources of information against predefined paramaters and is useful most for form submission checks and data entry.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('validator');
</pre>

<p>The following functions are available:</p>


<h2>required(<var>'var'</var>)</h2>
<p>This function performs a simple boolean check to ensure a passed variable contains a value.</p>
<pre class="brush: php">
if(required($var))
{
    ...code goes here
}
</pre>

<h2>regex(<var>'var'</var>, <var>'regex'</var>)</h2>
<p>This function is a wrapper for php's preg_match function.  Passing a mixed var plus the regular expression produces a boolean result.</p>
<pre class="brush: php">
if(regex($var, 'pattern'))
{
    ...code goes here
}
</pre>

<h2>has_one_of_these(<var>'args'</var>)</h2>
<p>A simple check to ensure that at least one value within an array exists.</p>
<pre class="brush: php">
if(has_one_of_these($var))
{
    ...code goes here
}
</pre>

<h2>valid_email(<var>'email'</var>)</h2>
<p>A simple boolean check to ensure a string conforms to standard email protocol.</p>
<pre class="brush: php">
$email = 'sample@sampleemail.com';
if(valid_email($email))
{
    ...code goes here
}
</pre>

<h2>min_num(<var>'var'</var>, <var>'limit'</var>)</h2>
<p>A simple boolean check to ensure a integer is below a certain value.</p>
<pre class="brush: php">
$var = 123;
$limit = 1000;
if(min_num($var, $limit))
{
    ...code goes here
}
</pre>

<h2>max_num(<var>'var'</var>, <var>'limit'</var>)</h2>
<p>A simple boolean check to ensure a integer is above a certain value.</p>
<pre class="brush: php">
$var = 123;
$limit = 1000;
if(max_num($var, $limit))
{
    ...code goes here
}
</pre>

<h2>is_between(<var>'var'</var>, <var>'lo'</var>, <var>'hi'</var>)</h2>
<p>A simple boolean check to ensure a integer exists between the range of two test integers.</p>
<pre class="brush: php">
$var = 123;
$lo = 1;
$hi = 200;
if(is_between($var, $lo, $hi))
{
    ...code goes here
}
</pre>

<h2>is_outside(<var>'var'</var>, <var>'lo'</var>, <var>'hi'</var>)</h2>
<p>A simple boolean check to ensure a integer exists outside the range of two test integers.</p>
<pre class="brush: php">
$var = 123;
$lo = 1;
$hi = 200;
if(is_outside($var, $lo, $hi))
{
    ...code goes here
}
</pre>

<h2>length_max(<var>'str'</var>, <var>'limit'</var>)</h2>
<p>A simple boolean check to ensure a string's length is less than the given argument.</p>
<pre class="brush: php">
$str = 'string content';
$limit = 20;
if(length_max($str, $limit))
{
    ...code goes here
}
</pre>

<h2>length_min(<var>'str'</var>, <var>'limit'</var>)</h2>
<p>A simple boolean check to ensure a string's length is greater than the given argument.</p>
<pre class="brush: php">
$str = 'string content';
$limit = 20;
if(length_min($str, $limit))
{
    ...code goes here
}
</pre>

<h2>valid_phone(<var>'str'</var>)</h2>
<p>A simple boolean check to ensure a string conforms to standard phone number protocol.</p>
<code>
$str = '503-234-5678';<br /><br />
if($valid_phone($str))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>is_equal_to(<var>'val'</var>, <var>'val2'</var>)</h2>
<p>A simple boolean check to ensure to mixed vars are equal to each other.</p>
<code>
$val1 = 'some value';<br />
$val2 = 'some value';<br /><br />
if(is_equal_to($val1, $val2))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>is_not_equal_to(<var>'val'</var>, <var>'val2'</var>)</h2>
<p>A simple boolean check to ensure to mixed vars are not equal to each other.</p>
<code>
$val1 = 'some value';<br />
$val2 = 'some value';<br /><br />
if(is_not_equal_to($val1, $val2))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>is_one_of_these(<var>'val'</var>, <var>'search_in'</var>)</h2>
<p>A boolean check to ensure a value exists within an array.</p>
<code>
$val = 'some value';<br />
$array = array($val, $array);<br /><br />
if(is_one_of_these($val, $array))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>is_not_one_of_these(<var>'val'</var>, <var>'searchIn'</var>)</h2>
<p>A boolean check to ensure a value does not exist within an array.</p>
<code>
$val = 'some value';<br />
$array = array($val, $array);<br /><br />
if(is_not_one_of_these($val, $array))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>
<h2>is_of_file_type(<var>'fileName'</var>, <var>'fileType'</var>)</h2>
<p>A boolean check to ensure an uploaded files is equal to the past mime type.</p>
<code>
$fileName = 'myfile.txt';<br />
$fileType = 'txt';<br /><br />
if(is_of_file_type($fileName, $fileType))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>valid_file_upload(<var>'fileName'</var>)</h2>
<p>A boolean check to ensure that files exist within a post submission.</p>
<code>
$fileName = 'myfile.txt';<br /><br />
if(valid_file_upload($fileName))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>is_safe_character(<var>'val'</var>, <var>'allowed'</var>)</h2>
<p>A simple boolean check to ensure a string consists only of alphanumeric values.</p>
<code>
$val = 'string of characters';<br />
$allowed = '+-=';<br /><br />
if(is_safe_character($val, $allowed))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>valid_date_time(<var>'date'</var>, <var>'format'</var>, <var>'delimiter'</var>)</h2>
<p>A boolean check to ensure a date and time string conforms to a past test value.</p>
<code>
$date = '2010-03-27';<br />
$format = 'Y-m-d';<br />
$delimiter = '-';<br /><br />
if(valid_date_time($date, $format, $delimiter))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>valid_date(<var>'date'</var>, <var>'format'</var>, <var>'delimiter'</var>)</h2>
<p>A boolean check to ensure a date string conforms to a past test value.</p>
<code>
$date = '2010-03-27';<br />
$format = 'Y-m-d';<br />
$delimiter = '-';<br /><br />
if(valid_date($date, $format, $delimiter))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>valid_time(<var>'date'</var>)</h2>
<p>A boolean check to ensure a time string conforms to a past test value.</p>
<code>
$date = '2010-03-27 8:00:00';<br /><br />
if(valid_time($date))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>valid_mdy(<var>'m'</var>, <var>'d'</var>, <var>'y'</var>)</h2>
<p>A boolean check to ensure a date and time string conforms to a past test value.</p>
<code>
$m = '03';<br />
$d = '27';<br />
$y = '2010';<br />
if(valid_mdy($m, $d, $y))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>is_after_date(<var>'date1'</var>, <var>'date2'</var>)</h2>
<p>A boolean check to ensure a date string exists later than a given value</p>
<code>
$date1 = '2010-03-27';<br />
$date2 = '2010-03-27';<br /><br />
if(is_after_date($date1, $date2))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>is_before_date(<var>'date1'</var>, <var>'date2'</var>)</h2>
<p>A boolean check to ensure a date string exists before than a given value</p>
<code>
$date1 = '2010-03-27';<br />
$date2 = '2010-03-27';<br /><br />
if(is_before_date($date1, $date2))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>is_between_dates(<var>'val'</var>, <var>'date1'</var>, <var>'date2'</var>)</h2>
<p>A boolean check to ensure a date exists within a given range</p>
<code>
$val = '2010-04-01';<br />
$date1 = '2010-03-27';<br />
$date2 = '2010-04-27';<br /><br />
if(is_between_dates($date1, $date2))<br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>is_future_date(<var>'val'</var>)</h2>
<p>A boolean check to ensure a date exists at some point in the future</p>
<code>
$val = '2011-03-27';<br />
if(is_future_date($val))<br /><br />
{<br />
&nbsp;&nbsp;&nbsp;&nbsp;...code goes here<br />
}
</code>

<h2>is_past_date(<var>'val'</var>)</h2>
<p>A boolean check to esnure a date exists at some point in the past.</p>

<h2>display_errors_js(<var>'ERRORS'</var>)</h2>
<p>This function passes user-defined errors to a javascript function and are dsiplayed using javascript alerts.</p>

<h2>display_errors(<var>'class'</var>, <var>'ERRORS'</var>)</h2>
<p>This function passes user-defined errors to a javascript function and injects it's content as a child of any element owning the included class.</p>

<h2>has_errors()</h2>
<p>Returns all user defined global errors assigned to global var <dfn>GLOBAL_ERRORS</dfn>.</p>

<h2>add_error(<var>'msg'</var>, <var>'key'</var>)</h2>
<p>Add an error message in key/value format to the global errors var <dfn>GLOBAL_ERRORS</dfn>.</p>

<h2>add_errors(<var>'errors'</var>)</h2>
<p>Add an array of error messages in key/value format to the global errors var <dfn>GLOBAL_ERRORS</dfn>.</p>

<h2>get_error(<var>'key'</var>)</h2>
<p>Returns an error message from the <dfn>GLOBAL_ERRORS</dfn> array if it exists under a particular key.</p>

<h2>get_errors()</h2>
<p>Returns an array of all error messages from the <dfn>GLOBAL_ERRORS</dfn> array if it exists.</p>