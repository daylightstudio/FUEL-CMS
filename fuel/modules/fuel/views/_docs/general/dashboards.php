<h1>Dashboards</h1>
<p>The FUEL CMS dashboard is the intial page that appears after a successful normal login. By default, it displays recent activity within the system, FUEL blog news, 
as well as a link to site specific documentation created in the <span class="file">fuel/application/views/_docs/index.php</span> file. 
However, it also allows you with the ability to include your own content. To do so, you can create an advanced module with a controller named <dfn>dashboard</dfn>. 
You then include your dashboard in the FUEL configuration by adding your advanced module's name to your <span class="file">fuel/application/config/MY_fuel.php</span> file:
<pre class="brush:php">
$config['dashboards'] = array('fuel', 'my_module');
</pre>

<p>A great example of using this is the <a href="https://github.com/pierlo-upitup/google_analytics" target="_blank">google_analytics</a> module created by  pierlo-upitup on GitHub.</p>
