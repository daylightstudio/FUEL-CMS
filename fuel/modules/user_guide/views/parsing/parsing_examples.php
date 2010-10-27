<h1>Parsing Examples</h1>
<p>Below are examples of FUEL's supported parsing syntax. For even more information, visit the <a href="http://wiki.dwoo.org/index.php/Main_Page" target="_blank">Dwoo Wiki</a></p>

<h2>Scalar Variable Merge</h2>

<pre class="brush:php">
$var['name'] = 'Luke';
$str = '{$name}, I am your father.';

$parsed_str = $this->parser->parse_string($str, $var);
</pre>


<h2>Array Variable Merge</h2>
<pre class="brush:php">
$var['darths_kiddos'] = array();
$var['darths_kiddos'][] = array('name' => 'Luke Skywalker', 'relationship' => 'son');
$var['darths_kiddos'][] = array('name' => 'Princess Leia', 'relationship' => 'daughter');

$str = '
{loop $darths_kiddos}
	{$name} is Vader\'s {$relationship}. 
{/loop}';

$parsed_str = $this->parser->parse_string($str, $var);
</pre>


<h2>Object Merge</h2>
<pre class="brush:php">
$this->load->model('users_model');
$data['users'] = $this->users_model->find_by_key(1);
$template = '{$user->full_name}';
$test = $this->parser->parse_string($template, $data);
echo $test; //'Darth Vader';
</pre>


<h2>Array of Objects Merge</h2>
<pre class="brush:php">
$this->load->model('users_model');
$data['users'] = $this->users_model->find_all();
$str = '
{loop $users}
<h2>{$user->first_name} {$user->last_name}</h2>
{$user->get_bio_formatted()}
{/loop}';

$parsed_str = $this->parser->parse_string($str, $data);
</pre>


<h2>Conditionals</h2>
<pre class="brush:php">
{if 1 + 1 == 2}
One plus one equals 2!
{/if}

{$my_var="test"}
{if $my_var == "test"}
my_var equals the word "test"!
{/if}
</pre>