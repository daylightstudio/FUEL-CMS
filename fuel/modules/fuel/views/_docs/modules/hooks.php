<h1>Module Hooks</h1>
<p>FUEL has extended the <a href="http://codeigniter.com/user_guide/general/hooks.html" target="_blank">CodeIgniter Hooks</a> system to provide 
extra hooks into creating, editing, and deleting of simple module data. An additional <dfn>module</dfn> hook parameter was added to allow you to add hook files
in your advanced module folders (e.g. <dfn>fuel/modules/my_module/hooks</dfn>). To add a hook, you modify the <dfn>fuel/application/config/hooks.php</dfn>
just like the native <a href="http://codeigniter.com/user_guide/general/hooks.html" target="_blank">CodeIgniter Hooks</a>.</p>

<p>The hooks currently available are:</p>
<ul>
	<li><strong>before_create_{module}</strong> - executed right BEFORE creating a new module item. The posted module item information is passed as an array to the hook class/function.</li>
	<li><strong>after_create_{module}</strong> - executed right AFTER creating a new module item. The newly create module item information is passed as an array to the hook class/function.</li>
	<li><strong>before_edit_{module}</strong> - executed right BEFORE saving the edited module information. The posted item information is passed as an array to the hook class/function.</li>
	<li><strong>after_edit_{module}</strong> - executed right AFTER saving the edited module information. The edited module item information is passed as an array to the hook class/function.</li>
	<li><strong>before_save_{module}</strong> - executed right BEFORE saving module information (for both create and edit). The new/edited posted module item information is passed as an array to the hook class/function.</li>
	<li><strong>after_save_{module}</strong> - executed right AFTER saving module information (for both create and edit). The new/edited module item information is passed as an array to the hook class/function.</li>
	<li><strong>before_delete_{module}</strong> - executed right BEFPRE deleting of module item(s). The posted IDs of the items to delete are passed as an array to the hook class/function.</li>
	<li><strong>after_delete_{module}</strong> - executed right AFTER deleting of module item(s). The IDs of the items deleted are passed as an array to the hook class/function.</li>
</ul>

<p class="important">The {module} part of the hook name is the name of the simple module that the hook applies to.<strong> Additionally, the {module} of <dfn>"module"</dfn> will
be applied to all modules.</strong></p>

<p>The following is an example of an advanced module named "projects" that has a hook file located at <dfn>fuel/modules/projects/hooks/Project_hooks.php</dfn>.</p>

<pre class="brush:php">
$hook['before_edit_projects'] = array(
	'class'    => 'Project_hooks',
	'function' => 'before_edit_projects',
	'filename' => 'Project_hooks.php',
	'filepath' => 'hooks',
	'params'   => array(),
	'module' => 'projects',
);
</pre>

<h2>How Are These Hooks Different Then Model Hooks?</h2>
<p>Unlike <a href="http://www.getfuelcms.com/user_guide/libraries/my_model" target="_blank">model hooks</a>, 
<dfn>module hooks</dfn> allow a module to execute code before or after an event in another module. This allows for more autonomous integration between modules.
For example, say you want to integrate into the FUEL blog comment module to send an email notification to someone other then the author of the post (which the blog
module does automatically). You can add an <dfn>after_edit_blog_comments</dfn> hook to the <dfn>fuel/application/config/hooks.php</dfn> file to make that happen.
</p>