/**
 * JQX Framework
 * http://www.getfuelcms.com
 *
 * A lightweight javascript MVC framework.
 *
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @licence		http://www.opensource.org/licenses/mit-license.php
 */


/******************************************************************
TEST FOR JQUERY AND SET UP JQX
******************************************************************/
try {
	jQuery;
} catch(e) {
	alert("You need jQuery to run the jqx framework.");
}

// set namespace for jqx
var jqx_global = this;
var jqx = {};


/******************************************************************
SET UP JQX FUNCTIONS
******************************************************************/
jqx.addPreload = function(obj){
	if (obj.constructor == Array){
		jQuery.each(jqx.config.preload, function(i, n){
			jqx.config.preload.push(n);
		});
	} else {
		jqx.config.preload.push(obj);
	}
};

jqx.createController = function(parentObj, ctrlObj){

	// if only an object is passed, extend the BaseController
	if (!ctrlObj){
		ctrlObj = parentObj;
		parentObj = jqx.lib.BaseController;
	}
	return parentObj.extend(ctrlObj);
};
	
jqx.init = function(ctrlName, initObj, path){
	if (ctrlName) {
		jQuery.each(jqx.config.preload, function(i, n){
			if (n.indexOf('/') != -1) {
				jqx.include(n);
			} else {
				jqx.includeObject(n);
			}
		});
		
		jqx.controllerInitObj = initObj;
		
		// set the jsPath value to the path to the controller so that we can include the object
		var origJSPath = jqx.config.jsPath;
		
		if (path && path.length) {
			jqx.config.controllerPath = path;
			jqx.config.jsPath = jqx.config.controllerPath;
		}
		jqx.includeObject(ctrlName);
		
		// now change back jsPath back to what it was 
		jqx.config.jsPath = origJSPath;

		var readyCallback = function(){
			jqx.domready = true;
			if (jqx.config.showLoadErrors){
				jqx.initCallback(ctrlName, initObj);
			} else {
				try {
					jqx.initCallback(ctrlName, initObj);
				} catch(e){
					jqx.getMessage("noController");
					new jqx.Message(jqx.config.msg.noController, "fatal");
				}
			}
			jqx.execStopTime = new Date().getTime();
			jqx.debug(jqx.execStopTime - jqx.execStartTime);
			
		}
		if (jqx.config.defaultIncludeMethod == 'default'){
			jQuery(jqx_global).load(function(){
				readyCallback();
			});
		} else {
			jQuery().ready(function(){
				readyCallback();
			});
		}
	}
};

jqx.initCallback = function(ctrlName, initObj){
	var pageVar = jqx.config.controllerName;
	var controllerObj = eval(ctrlName);
	if (jqx.extender.classObject) controllerObj = controllerObj.extend(jqx.extender.classObject);
	if (jqx.extender.initObj) initObj = $.extend(initObj, jqx.extender.initObj);

	var controller = new controllerObj(initObj);
	if (jqx_global[pageVar] == undefined){
		jqx_global[pageVar] = {};
	}
	jqx_global[pageVar] = jQuery.extend(jqx_global[pageVar], controller);
};

jqx.getControllerPath = function(ctrlName){
	return jqx.config.controllerPath + ctrlName + ".js";
};

jqx.includeAJAX = function(file, callback){
	if (jqx._includeCache.isCached(file)) return;
	jqx.filesToLoad.push(file);
	jQuery.ajax({async:true, url: file, dataType: 'script', cache : jqx.config.cacheAjaxIncludes,
		success : function(){
			if (callback) {
				callback.call();
			}
			jqx.filesLoaded.push(file);
			if (jqx.isLoaded()) jqx.initCallback(jqx.controllerName, jqx.controllerInitObj);
		},
		error: function(){
			new jqx.Message('error loading ' + file, 'error');
		}
	});
};

jqx.isLoaded = function(){
	if (jqx.filesToLoad.length == jqx.filesLoaded.length){
		return true;
	} else {
		return false;
	}
};

jqx._include = function(file, method, callback){
	if (!method) method = jqx.config.defaultIncludeMethod;
	if (method == 'ajax'){
		jqx.includeAJAX(file, callback);
	} else if (method == 'dynamic'){
		var head, script;
		if (document.createElement && document.getElementsByTagName
			&& (head = document.getElementsByTagName('head')[0]) && head.appendChild
			&& (script = document.createElement('script'))) {
			script.type = 'text/javascript';
			script.src = file;
			script.onload = function(){
				jqx.filesLoaded.push(file);
				if (callback) callback();
				//console.log(jqx.filesToLoad.length + ' ' + jqx.filesLoaded.length)
			}
			head.appendChild(script);
		} else {
			return false;
		}
	} else {
		var i = jqx.scriptCallbacks.length;
		(function(index){
			var includeScript = '<script type="text/javascript" src="' + file + '"';
			if (callback){
				jqx.scriptCallbacks[index] = callback;
				includeScript += ' onload="jqx.scriptCallbacks[' + index + ']()" ';
			}
			includeScript += '><\/script>';
			document.write(includeScript);
		})(i);
	}
};


jqx.include = function(){
	var a = arguments;
	if (a[0].constructor == Array) a = a[0];
	var callback = (a.length == 2) ? a[1] : null;
	jQuery.each(a, function(i, n){
		if (n.substr(( n.length - 3), (n.length)) != ".js") n = n + ".js";
		if (jqx._includeCache.isCached(n)) return;
		jqx.filesToLoad.push(n);
		jqx._include(n, jqx.config.defaultIncludeMethod, callback);
		jqx._includeCache.add(n);
	});
};

jqx.includeObject = function(){
	var a = arguments;
	if (a[0].constructor == Array) a = a[0];
	jQuery.each(a, function(i, n){
		var path = jqx.config.jsPath + n.split(".").join("/") + ".js";
		if (jqx._includeCache.isCached(path)) return;
		jqx.createObjectFromString(n, ".");
		jqx._include(path);
		jqx._includeCache.add(path);
	});
};

jqx.includeObjectAJAX = function(){
	var a = arguments;
	if (a[0].constructor == Array) a = a[0];
	jQuery.each(a, function(i, n){
		var path = jqx.config.jsPath + n.split(".").join("/") + ".js";
		if (jqx._includeCache.isCached(path)) return;
		jqx.createObjectFromString(n, ".");
		jqx._includeAJAX(path);
		jqx._includeCache.add(path);
	});
};

jqx.createObjectFromString = function(str, delimiter){
	if (!delimiter) delimiter = '.';
	var strs = str.split(delimiter);
	var objectCreator = function(obj, n){
		if (!obj[n]){
			return obj[n] = {};
		} else {
			return obj[n];
		}
	};
	var currentObj = jqx_global;
	jQuery.each(strs, function(i, n){
		currentObj = objectCreator(currentObj, n);
	});
}

jqx.Cache = function(){
	this._cache = {};
};

jqx.Cache.prototype = {

	add : function(key, val, max){
		if (!val) val = key;
		if (!max || val.length < max){
			this._cache[key] = val;
		}
	},

	remove : function(key){
		if (key != null) {
			delete this._cache[key];
		} else {
			this._cache = {};
		}
	},

	get : function(key){
		return this._cache[key];
	},

	isCached : function(key){
		return (this._cache[key] != null)
	},

	size : function(){
		var size = 0;
		jQuery.each(this._cache, function(i, n){
			if (this._cache[n].length) size += this._cache[n].length;
		});
		return size;
	}
};

jqx.Message = function (msg, type, autoDisplay){
	this.msg = msg;
	this.type = type;
	if (autoDisplay || autoDisplay == null) this.display();
};

jqx.Message.prototype = {
	display : function(){
		alert(this.msg);
	}
};
jqx.debug = function(msg){
	if (jqx_global["console"] && jqx.config.debug) {
		jqx_global[ "console"].debug(msg);
	}
};

jqx.getMessage = function(key){
	if (jqx.config.msg[key]) return jqx.config.msg[key];
	return null;
};

jqx.load = function(type, file){
	switch(type){
		case 'helper': jqx.include(jqx.config.helpersPath + file); break;
		case 'plugin' :  jqx.include(jqx.config.pluginPath + file); break;
		case 'object' :  jqx.includeObject(file); break;
		case 'file' :  jqx.include(jqx.config.jsPath + file); break;
		default : jqx.include(jqx.config.pluginPath + file);
	}
};

jqx.extendController = function(obj, initObj){
	jqx.extender.classObject = obj;
	jqx.extender.initObj = initObj;
	var pageVar = jqx.config.controllerName;
	if (jqx_global[pageVar])
	{
		jQuery.extend(jqx_global[pageVar], obj);
	}
	
};

/******************************************************************
SET UP VARIABLES
******************************************************************/

// set default configurations
jqx.config = {};
jqx.config.debug = false;
jqx.config.basePath = "";
jqx.config.jsPath = jqx.config.basePath + "js/";
jqx.config.jqxPath = jqx.config.jsPath + "jqx/";
jqx.config.imgPath = jqx.config.basePath + "image/";
jqx.config.cssPath = jqx.config.basePath + "css/";
jqx.config.htmlPath = jqx.config.basePath;
jqx.config.pluginPath = jqx.config.jsPath + "jquery/plugins/";
jqx.config.controllerPath =  jqx.config.jsPath + "controller/";
jqx.config.preload = ["jqx.lib.Class", "jqx.lib.BaseController"];
jqx.config.cookieDefaultLifetime = 30;
jqx.config.cookieDefaultPath = jqx.config.basePath;
jqx.config.cookieMaxSize = 4004;
jqx.config.controllerName = "page";
jqx.config.msg = {};
jqx.config.msg.noController = "Can not load the page controller";
jqx.config.cacheAjaxIncludes = true;
jqx.config.showLoadErrors = true;
jqx.config.defaultIncludeMethod = 'default';

// set up jqx variables
jqx.filesToLoad = [];
jqx.filesLoaded = [];
jqx.controller = {};
jqx.domready = false;
jqx.execStartTime = new Date().getTime();
jqx.scriptCallbacks = [];
jqx.extender = {};

if (jqx_config) jqx.config = jQuery.extend({}, jqx.config, jqx_config);

jqx._includeCache = new jqx.Cache();