<h1>Cronjobs Documentation</h1>
<p>Cron jobs (crontab) is a way to periodically execute tasks. For more information about cron jobs 
<a href="http://www.google.com/search?client=safari&rls=en-us&q=cron+job+tutorial&ie=UTF-8&oe=UTF-8" target="_blank">click here</a>. 
For cron jobs to work correctly, you must do the following:</p>
<ol>
	<li>Make the file <dfn><?=INSTALL_ROOT?>crons/ci_cron.php</dfn> executable. This file will run the CodeIgniter bootstrap index file.</li>
	<li>Open up the <dfn><?=INSTALL_ROOT?>crons/ci_cron.php</dfn> and change the CRON_CI_INDEX, CRON_LOG, the $_SERVER['SERVER_NAME'], and the $_SERVER['SERVER_PORT'] (if needed).</li>
	<li>Make the file <dfn><?=INSTALL_ROOT?>crons/crontab.php</dfn> writable.</li>
</ol>


<p class="important">The cron folder should be protected by the .htaccess or live above the server's root directory.</p>

<h2>Database Backup</h2>
<p>The backup module comes with a controller to backup the database with options to 
include the assets folder as well as email it as an attachment. Below is an example of the command to specify for a cron job to do that:</p>

<pre class="brush: php">
// * asterisks mean every minute/hour/day/... etc depending on the field
php <?=INSTALL_ROOT?>data_backup/ci_cron.php --run=backup/cron --show-output

// adding a 1 at the end of the URI path will include the assets (if not specified in the config)
php <?=INSTALL_ROOT?>data_backup/ci_cron.php --run=backup/cron/1 --show-output


</pre>


<h2>Cronjobs Configuration</h2>
<ul>
	<li><strong>crons_folder</strong> - the folder to save the cronjob file.</li>
	<li><strong>cron_user</strong> - the user to associate the cronjob to.</li>
	<li><strong>sudo_pwd</strong> - the sudo password if needed.</li>
</ul>