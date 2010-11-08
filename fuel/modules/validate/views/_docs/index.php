<h1>Validate Module Documentation</h1>
<p>This Validate documentation is for version <?=VALIDATE_VERSION?>.</p>

<h2>Overview</h2>
<p>The Validate module allows you to validate your HTML, check for invalid links and check the file size weight of your pages.<p>

<h2>Configuration</h2>
<ul>
	<li><strong>validator_url</strong> - validator url</li>
	<li><strong>valid_internal_server_names</strong> - a list of valid internal domains. Can contain regular expression or :any, :num wildcards</li>
	<li><strong>size_report_warn_limit</strong> - sets the warning limit of the filesize of a resource in KB</li>
	<li><strong>default_page_input</strong> - default value for page input field. Must be delimited by \n</li>
</ul>