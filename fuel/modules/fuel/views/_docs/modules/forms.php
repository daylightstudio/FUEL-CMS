<h1>Module Forms</h1>
<p>Modules use the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> class to create the form used to create and edit module information. 
You can modify a model's <a href="<?=user_guide_url('libraries/my_model')?>">form_fields()</a> method to customize the form.</p>

<h2>Trigger CSS Classes</h2>
<p>There are some special CSS classes that will trigger extra functionality with certain fields:</p>
<ul>
	<li><strong>add_edit</strong> - the <dfn>add_edit</dfn> CSS class allows you to add and/or edit another module directly in the module's form. This class works on select fields.</li>
	<li><strong>asset_select [pdf, css, js]</strong> - the <dfn>asset_select</dfn> CSS class allows you input asset file names into your form fields. 
	You can specify an optional second class to specify the specific asset folder (.e.g. 'class' => 'add_edit pdf'). The default asset folder is the <dfn>images</dfn> folder. This class works with text input fields.</li>
	<li><strong>multifile</strong> - the <dfn>multifile</dfn> CSS class allows you to add multiple files at a time. Works on file upload fields.</li>
	<li><strong>wysiwyg</strong> - the <dfn>wysiwyg</dfn> CSS class will trigger the CKEditor to be used instead of markItUp! on the field.</li>
</ul>

<p>Conversely, there are a couple CSS classes that are used to remove certain functionality.</p>
<ul>
	<li><strong>no_editor</strong> - the <dfn>no_editor</dfn> CSS class is used when you don't want to apply a MarkitUp editor to your text field.</li>
	<li><strong>no_combo</strong> - the <dfn>no_combo</dfn> CSS class is used when you don't want the combo box used and instead want a regular multi-select field.</li>
</ul>