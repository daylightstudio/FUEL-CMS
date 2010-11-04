<h1>Asset Helper</h1>

<p>The Asset helper is really just a set of convenience functions that map to the <a href="<?=user_guide_url('libraries/asset')?>">Asset class</a>. 
This was done because these functions may be used a lot on in view files 
and it is easier to type <kbd>&lt;?=img_path('my_img.jpg')?&gt;</kbd> instead of <kbd>&lt;?=$this->asset->img_path('my_img.jpg')?&gt;</kbd>.
This helper is <strong>autoloaded</strong>.</p>
</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('asset');
</pre>

<p>The following functions are available:</p>

<h2>img_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#img_path')?>">$this->asset->img_path()</a>.</p>

<h2>css_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#css_path')?>">$this->asset->css_path()</a>.</p>

<h2>js_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#js_path')?>">$this->asset->js_path()</a>.</p>

<h2>swf_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#swf_path')?>">$this->asset->swf_path()</a>.</p>

<h2>pdf_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#pdf_path')?>">$this->asset->pdf_path()</a>.</p>

<h2>media_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#media_path')?>">$this->asset->media_path()</a>.</p>

<h2>cache_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#cache_path')?>">$this->asset->cache_path()</a>.</p>

<h2>captcha_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#captcha_path')?>">$this->asset->captcha_path()</a>.</p>

<h2>assets_path(<var>['file']</var>, <var>['type']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#assets_path')?>">$this->asset->assets_path()</a>.</p>

<h2>assets_server_path(<var>['file']</var>, <var>['type']</var>, <var>['module']</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#assets_server_path')?>">$this->asset->assets_server_path()</a>.</p>

<h2>assets_server_to_web_path(<var>'file'</var>, <var>truncate_to_asset_folder</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#assets_server_to_web_path')?>">$this->asset->assets_server_to_web_path()</a>.</p>

<h2>js(<var>'file'</var>, <var>['module']</var>, <var>params</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#js')?>">$this->asset->js()</a>.</p>

<h2>css(<var>'file'</var>, <var>['module']</var>, <var>params</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#css')?>">$this->asset->css()</a>.</p>

<h2>swf(<var>'file'</var>, <var>'id'</var>, <var>'width'</var>, <var>'height'</var>, <var>other_options</var>)</h2>
<p>This function is the same as <a href="<?=user_guide_url('libraries/asset#swf')?>">$this->asset->swf()</a>.</p>

