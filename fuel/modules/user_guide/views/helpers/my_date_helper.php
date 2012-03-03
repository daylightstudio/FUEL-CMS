<h1>Extended Date Helper</h1>

<p>Extends the CodeIgniter date helper functions. This helper is <strong>autoloaded</strong>.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('date');
</pre>

<p>The following functions are available:</p>

<h2>datetime_now([<var>hms</var>])</h2>
<p>Returns a the current time in a yyyy-mm-dd hh:mm:ss format which can be used with MySQL databases. The optional parameter
will return the current date without the time if set to FALSE (default is TRUE).</p>
<pre class="brush: php">
echo datetime_now(); // <?=datetime_now()?> 
echo datetime_now(FALSE); // <?=datetime_now(FALSE)?>
</pre>


<h2>is_date_format('<var>date</var>')</h2>
<p>Returns a boolean value as to whether the date is in an english (mm/dd/yyyy) or database (yyyy-mm-dd) format.</p>
<pre class="brush: php">
is_date_format('2010/01/01');
// returns FALSE

is_date_format('2010-01-01');
// returns TRUE
</pre>


<h2>is_date_db_format('<var>date</var>')</h2>
<p>Returns a boolean value as to whether the date is in a valid database (yyyy-mm-dd) format.</p>
<pre class="brush: php">
is_date_format('2010-01-01');
// returns TRUE
</pre>

<h2>is_date_english_format('<var>date</var>')</h2>
<p>Returns a boolean value as to whether the date is in a valid 'english' (mm/dd/yyy) format.</p>
<pre class="brush: php">
is_date_english_format('01/01/2010');
// returns TRUE
</pre>


<h2>english_date('<var>date</var>', <var>[long]</var>, <var>[timezone]</var>, <var>[delimiter]</var>)</h2>
<p>Returns a date in 'english' (mm/dd/yyy) format. 
The <dfn>long</dfn> parameter determines whether to include the time or not.
The <dfn>timezone</dfn> parameter determines whether to include the time or not.
The <dfn>delimiter</dfn> parameter determines whether to include the time or not.
</p>
<pre class="brush: php">
english_date(time());
// returns <?=english_date(time())?>
</pre>


<h2>english_date_verbose('<var>date</var>')</h2>
<p>Returns date in 'verbose' ( Jan. 1, 2010) format.</p>
<pre class="brush: php">
english_date_verbose(time());
// returns <?=english_date_verbose(time())?>
</pre>


<h2>time_verbose('<var>time</var>', <var>[include_seconds]</var>)</h2>
<p>Returns the time into a verbose format (e.g. 12hrs 10mins 10secs).</p>
<pre class="brush: php">
time_verbose(time(), TRUE);
//returns <?=time_verbose(time(), TRUE)?>
</pre>

<h2>english_date_to_db_format(<var>date</var>, <var>[hour]</var>, <var>[min]</var>, <var>[sec]</var>, <var>[ampm]</var>, <var>[delimiter]</var>)</h2>
<p>Converts a date from english (e.g. mm/dd/yyyy) to db format (e.g yyyy-mm-dd). The default delimiter parameter is <strong>'/'</strong>.</p>

<pre class="brush: php">
english_date_to_db_format('01/01/2010', 12, 10, 0);
// returns <?=english_date_to_db_format('01/01/2010', 12, 10, 0)?>
</pre>


<h2>format_db_date(<var>[y]</var>, <var>[m]</var>, <var>[d]</var>, <var>[h]</var>, <var>[i]</var>, <var>[s]</var>)</h2>
<p>Formats a date for the database in (yyyy-mm-dd hh:mm:ss) format. If a value is left out, it will return the current date/time value for that slot.</p>
<pre class="brush: php">
format_db_date(2010, 1, 1, 12, 10, 10);
// returns <?=format_db_date(2010, 1, 1, 12, 10, 10)?>
</pre>


<h2>date_range_string(<var>date1</var>, <var>date2</var>)</h2>
<p>Creates a date range string (e.g. January 1-10, 2010).</p>

<pre class="brush: php">
date_range_string('2010-08-01', '2010-08-05');
// returns <?=date_range_string((time() - (24*60*60)), time())?>
</pre>

<h2>date_range_array(<var>start_date</var>, <var>end_date</var>, <var>[output_format]</var>, <var>[increment]</var>)</h2>
<p>Creates a date range array (e.g. array('2011-01-01', '2011-02-01', '2011-03-01', ...)).</p>

<h3>Example #1 date_range_array() example without extra params set</h3>
<pre class="brush: php">
date_range_array('2011-01-01', '2011-04-01');
// returns array('2011-01-01', '2011-02-01', '2011-03-01', '2011-04-01')
</pre>

<h3>Example #2 date_range_array() example with all params set</h3>
<pre class="brush: php">
date_range_array('2005-01-01', '2008-01-01', 'M Y', '+1 year');
// returns array('Jan 2005', 'Jan 2006', 'Jan 2007', 'Jan 2008')
</pre>


<h2>pretty_date(<var>timestamp</var>, <var>use_gmt</var>)</h2>
<p>Creates a string based on how long from the current time the date provided.</p>

<pre class="brush: php">
pretty_date(time() - (60 * 60));
// returns <?=pretty_date(time() - (60 * 60))?>
</pre>


<h2>get_age(<var>bday_ts</var>, <var>[at_time_ts]</var>)</h2>
<p>Returns an age based on a given date/timestamp. The second parameter is optional and by default will be the current date.</p>

<pre class="brush: php">
get_age('2000-01-01');
// returns <?=get_age('2000-01-01')?>
</pre>