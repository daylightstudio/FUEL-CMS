
var jqx_config = {};
jqx_config.basePath =  "<?=(substr(site_url(), -1) != '/') ? site_url().'/' : site_url();?>";
jqx_config.jsPath = "<?=js_path('', 'fuel')?>";
jqx_config.imgPath = "<?=img_path('', 'fuel')?>";

jqx_config.assetsImgPath = "<?=img_path('')?>";
jqx_config.assetsPath = "<?=assets_path('')?>";
jqx_config.assetsCssPath = "<?=css_path('')?>";

jqx_config.jqxPath = jqx_config.jsPath + "jqx/";
jqx_config.controllerPath = jqx_config.jsPath + "fuel/controller/";
jqx_config.pluginPath = jqx_config.jsPath + "jquery/";
jqx_config.helpersPath = jqx_config.jqxPath + 'helpers/';
jqx_config.pluginPath = jqx_config.jsPath + 'jquery/plugins/';
jqx_config.fuelPath = '<?=site_url($this->config->item('fuel_path', 'fuel'))?>';
jqx_config.cookieDefaultPath = '<?=$this->config->item('fuel_cookie_path', 'fuel')?>';
<?php if (!empty($keyboard_shortcuts)){ ?>jqx_config.keyboardShortcuts = <?=json_encode($keyboard_shortcuts)?>;<?php } ?> 
jqx_config.warnIfModified = <?=(int)$this->config->item('warn_if_modified', 'fuel')?>; 
jqx_config.cacheString = new Date('<?=date('F d, Y H:i:s', strtotime($this->config->item('last_updated'))) ?>').getTime().toString();
jqx_config.assetsAccept = '<?php $editable_asset_types = $this->config->item('editable_asset_filetypes', 'fuel'); echo (!empty($editable_asset_types['media']) ? $editable_asset_types['media'] : 'jpg|gif|png'); ?>';
<?php if (!empty($js_localized)) :?>
jqx_config.localized = <?=json_lang($js_localized)?>;
<?php endif; ?>
jqx_config.editor = '<?=$this->config->item('text_editor', 'fuel')?>';
jqx_config.ckeditorConfig = <?=json_encode($this->config->item('ck_editor_settings', 'fuel'))?>;

var __FUEL_PATH__ = '<?=site_url($this->config->item('fuel_path', 'fuel'))?>'; // for preview in markitup settings
