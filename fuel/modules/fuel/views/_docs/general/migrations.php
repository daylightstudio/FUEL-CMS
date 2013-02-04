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

<p class="important">You can also simply browse to the URI path (e.g. fuel/migrate/latest).</p>