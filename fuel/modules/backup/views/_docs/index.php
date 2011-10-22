<h1>Backup Documentation</h1>
<p>The Backup module can be used to backup the FUEL database as well as the <dfn>assets</dfn> folder.
It comes with a controller to backup the database with options to include the assets folder as well as email it as an attachment. 
The Backup module can be used along with the <a href="<?=user_guide_url('modules/cronjobs')?>">Cronjobs module</a> to do periodic backups.</p>

<h2>Backup Configuration</h2>
<ul>
	<li><dfn>file_prefix</dfn> - used for the name of the backup file. A value of AUTO will automatically create the name.</li>
	<li><dfn>include_assets</dfn> - determines whether to backup assets by default.</li>
	<li><dfn>cron_email</dfn> - the email address to send the cron job notification.</li>
	<li><dfn>cron_email_file</dfn> - use the email address to email notifications (seperate from the cron email).</li>
	<li><dfn>backup_path</dfn> - used for the admin/manage/backup. Beow default looks for folder called data_backup at the same level as the system and application folder</li>
	<li><dfn>db_backup_prefs</dfn> - use the email address to email notifications (seperate from the cron email). It is an array with the following options
		<ul>
			<li><strong>ignore</strong> - list of tables to omit from the backup.</li>
			<li><strong>add_drop</strong> - whether to add DROP TABLE statements to backup file. Default is TRUE.</li>
			<li><strong>add_insert</strong> - whether to add INSERT data to backup file. Default is TRUE.</li>
		</ul>
	
	</li>
</ul>