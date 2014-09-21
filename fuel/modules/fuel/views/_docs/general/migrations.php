<h1>Migrations</h1>
<p>FUEL CMS provides a command line utility for using <a href="http://ellislab.com/codeigniter/user-guide/libraries/migration.html" target="_blank">CodeIgniter's migrations</a>.</p>

<pre class="brush:php">
// updates to the latest migration file regardless of what is specified in the migrations table
php index.php fuel/migrate/latest

// migrates to the most current version specified in the migrations table
php index.php fuel/migrate/current

// migrates to a specific version. Must be in a dev mode environment
php index.php fuel/migrate/version/2
</pre>

<p class="important">Fuel comes with a single "001_install" migration as an example which will load in the fuel_schema.sql file if it hasn't already been loaded. 
If you have already loaded the fuel_schema.sql file, replace this migration with the first migration of your site or you will get SQL errors stating that the tables have already been created.</p>

<p class="important">You can also simply browse to the URI path (e.g. fuel/migrate/latest).</p>
<h2>Web Hooks</h2>
<p>The <a href="<?=user_guide_url('installation/configuration')?>">FUEL configuration's <dfn>webhook_remote_ip</dfn> parameter</a> gives you the ability to set one or more IP addresses
that can be used to remotely call the fuel/migrate controller. For example, say you use <a href="http://beanstalkapp.com" target="_blank">Beanstalk</a> to manage your GIT repositories and you would like to run your migrations automatically upon 
commit. You can set this configuration value to the IP address ranges <a href="http://support.beanstalkapp.com/customer/portal/articles/68153-ip-addresses-for-access-to-beanstalk" target="_blank">provided here</a>.
Then, in Beanstalk you can set up your <a href="http://support.beanstalkapp.com/customer/portal/articles/68163-web-hooks-for-deployments" target="_blank">web deployment post hook</a>. In this case you would set it in Beanstalk to be the full URL path:</p>
<pre class="brush:php">
http://www.mysite.com/fuel/migrate/latest
</pre>