<h1>Module Tools</h1>
<p>New to FUEL CMS 1.0 is the ability to create advanced module tools that can be accessed from the <a href="<?=user_guide_url('general/inline-editing')?>">inline editing toolbar</a> to perform certain actions.
For example, the <dfn>pag_analysis</dfn> and <dfn>validate</dfn> modules both provide tools to analyze and validate the contents of the currently viewed page.</p>

<p>To add a toolbar tool, you can simply add a 'toolbar', parameter in your advanced modules configuration. The value should be an array with the 
key being the URI path relative to the module and the value being the name you want displayed in the tools list dropdown.</p>

<h3>Example #1 (from the Page Analysis module)</h3>
<pre class="brush:php">
// the inline editing toolbar for doing page analysis
$config['page_analysis']['toolbar'] = array(
									'toolbar' => 'Page Analysis',
							);
</pre>

<h3>Example #2 (from the Validate module)</h3>
<pre class="brush:php">
// the inline editing toolbar for doing validation for HTML, links and page weight
$config['validate']['toolbar'] = array(
									'toolbar/html' => 'Validate HTML',
									'toolbar/links' => 'Validate Links',
									'toolbar/size_report' => 'Page Weight',
							);
</pre>