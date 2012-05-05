<h1>Generate</h1>
<p>FUEL CMS provides a command line tool to help you auto generate starter files for your simple and advanced modules. To do so, navigate 
to the folder (e.g. using "cd") where the main bootstrap index.php file exists. Then you can type the commands below which uses the
<a href="http://codeigniter.com/user_guide/general/cli.html" target="_blank">CodeIgniter CLI</a> to generate files automatically.</p>

<h2>Configuration and Templates</h2>
<p>The default generated template files exist in the <span class="file">fuel/modules/fuel/views/_generated/{type}</span> folder where <dfn>{type}</dfn>
is either "advanced" or "simple". You can overwrite these defaults by creating a <span class="file">fuel/application/views/_generated/{type}</span> 
folder with files that correspond to the files specified in the <a href="<?=user_guide_url('installation/configuration')?>">FUEL configuration file</a> under the "generated" parameter.</p>

<h2>Models</h2>
<p>The following will create a model named "examples_model.php", generate a placeholder table (you'll need to modify):</p>
<pre class="brush:php">
php index.php fuel/generate/model/ examples
</pre>

<h2>Simple Modules</h2>
<p>The following will create a model named "examples_model.php", generate a placeholder table (you'll need to modify), add the permisions to manage it in the CMS,  and will add it to the <span class="file">fuel/application/config/MY_fuel_modules.php</span>:</p>
<pre class="brush:php">
php index.php fuel/generate/simple/ examples
</pre>

<h2>Advanced Modules</h2>
<p>The following will create a directory named "test" in the <span class="file">fuel/modules/</span> folder and will generate by default the files specified in the 
<a href="<?=user_guide_url('installation/configuration')?>">FUEL configuration file</a> under the "generated" parameter:</p>
<pre class="brush:php">
php index.php fuel/generate/advanced/ examples
</pre>

