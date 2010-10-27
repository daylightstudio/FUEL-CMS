/**
* jQuery fancyzoom plugin.
* This is an adaptation of the fancyzoom effect as a jQuery plugin
*
* Author: Mathieu Vilaplana <mvilaplana@df-e.com>
* Date: March 2008
* rev 1.0
* rev: 1.1
* Add title if alt in the img
* rev 1.2
* Correction of the image dimension and close button on top right of the image
* rev 1.3
* now fancyzoom can be apply on an image, no need any more link wrapper
* rev 1.4 correct the bug for the overlay in ie6
* rev 1.6 (09/2009), lot of impovement, now image get out of its context.
*/

// added showZoomIndicator option -  David McReynolds @ Daylight 10/16/2009

(function($) {
	
	$.fn.fancyzoom = function(userOptions) {
		//the var to the image box div
	 	var oOverlay = $('<div>').css({
			height: '100%',
			width: '100%',
   			position:'fixed',
   			zIndex:100,
			left: 0,
			top: 0,
			cursor:"wait"
		});
		
		function openZoomBox(imgSrc,o){
			if(o.showoverlay) {
				oOverlay
					.appendTo('body')
					.click(function(){closeZoomBox(o);});
				if( $.browser.msie && $.browser.version < 7 ){
					oOverlay.css({position:'absolute',height:$(document).height(),width:$(document).width()});
				}
			}
			var oImgZoomBox = o.oImgZoomBox;

            //calculate the start point of the animation, it start from the image of the element clicked
            pos=imgSrc.offset();
			o=$.extend(o,{imgSrc:imgSrc,dimOri:{width:imgSrc.outerWidth(),height:imgSrc.outerHeight(),left:pos.left,top:pos.top,'opacity':1}});
			if(!imgSrc.is('img')){
				o.dimOri = $.extend(o.dimOri,{width:0,height:0});
			}

			//calculate the end point of the animaton
			oImgZoomBox.css({'text-align':'center','border':'0px solid red'}).appendTo('body');
			var iWidth = oImgZoomBox.outerWidth();
			var iHeight = oImgZoomBox.outerHeight();
			
			//the target is in the center without the extra margin du to close Image
			dimBoxTarget=$.extend({},{width:iWidth,height:iHeight,'opacity':1}, __posCenter((iWidth),(iHeight+30)));
            
            //place the close button at the right of the zoomed Image
            o.oImgClose.css({left:(dimBoxTarget.left+dimBoxTarget.width-15),top:(dimBoxTarget.top-15)});
            
            var $fctEnd = function(){
            	//end of open, show the shadow
            	if($.fn.shadow && o.shadow && !$.browser.msie){ $('img:first',oImgZoomBox).shadow(o.shadowOpts);}
				if(o.Speed>0 && !$.browser.msie) {o.oImgClose.fadeIn('slow');$('div',oImgZoomBox).fadeIn('slow');}
				else {o.oImgClose.show();$('div',oImgZoomBox).show();}			
            };
            
            
            $('div',oImgZoomBox).hide();//cache le titre
            //cache l'image source
            if(o.imgSrc.is('img')){o.imgSrc.css({'opacity':0});}
            var oImgDisplay = $('img:first', oImgZoomBox).css({'width':'100%','height':'auto'});
  			if(o.Speed > 0) {
  				oImgZoomBox.css(o.dimOri).animate(dimBoxTarget,o.Speed,$fctEnd);
  			}
  			else {
  				oImgZoomBox.css(dimBoxTarget);
  				$fctEnd();
  			}
	 	 }//end openZoomBox
 	 	 
 	 	 /**
 	 	  * First hide the closeBtn, then remove the ZoomBox and the overlay
 	 	  * Animate if Speed > 0 
 	 	  */
 	 	 function closeZoomBox(o){
 	 	 	var oImgZoomBox = o.oImgZoomBox;
	 	 	o.oImgClose.remove();
	 	 	$('div',oImgZoomBox).remove();
	 	 	var endClose = function(){
	 	 		oImgZoomBox.empty().remove();
	 	 		o.imgSrc.css('opacity',1);
	 	 	};
		 	 if(o.Speed > 0){
		 	 	var pos = oImgZoomBox.offset();
		 	 	var iPercent = 0.15;
		 	 	var oDimPlus = {
		 	 		width:(oImgZoomBox.width()*(1+iPercent)),
		 	 		height:(oImgZoomBox.height()*(1+iPercent)),
		 	 		left:(pos.left-(oImgZoomBox.width()*(iPercent/2))),
		 	 		top:(pos.top-(oImgZoomBox.height()*(iPercent/2)))
		 	 	};
		 	 	oImgZoomBox.animate(oDimPlus,o.Speed*0.2,function(){
			 	 	oImgZoomBox.animate(o.dimOri,o.Speed,function(){endClose();});
					if(o.showoverlay) {oOverlay.animate({'opacity':0},o.Speed,function(){$(this).remove();});}
		 	 	});
	 	 	}else {
			 	endClose();
				if(o.showoverlay) {oOverlay.remove();}
	 	 	}
 	 	 }
    		
		/**
		 * The plugin chain.
		 */
   		return this.each(function() {
   			var $this = $(this);
   			var imgTarget = $this.is('img')?$this:($('img:first',$this).length==0)?$this:$('img:first',$this);
   			var imgTargetSrc=null;
   			if($this.attr('href')) {imgTargetSrc = $this.attr('href');}
		 	var oImgClose = $('<img class="jqfancyzoomclosebox">').css({position:'absolute',top:0,left:0,cursor:'pointer'});

			// build main options before element iteration		
	    	var opts = $.extend({},$.fn.fancyzoom.defaultsOptions, userOptions||{},{dimOri:{},
	    		oImgZoomBoxProp:{position:'absolute',left:0,top:0},
	    		oImgClose:oImgClose
	    	});
	    	
			if(imgTarget.is('img') && opts.showZoomIndicator){
		    	var oImgHover = $("<img src='"+opts.imgDir+"zoom.png'>").css({position:'absolute',top:0,left:0});
				imgTarget.hover(function(){
					if(imgTarget.css('opacity') != 0){
						oImgHover.appendTo(imgTarget.parent()).hide();
						var pos = imgTarget.position();
						var marginLeft = parseInt(imgTarget.css('margin-left').replace(/px/,''));
						var marginTop = parseInt(imgTarget.css('margin-top').replace(/px/,''));
						marginTop = (marginTop)?marginTop:0;
						marginLeft = (marginLeft)?marginLeft:0;
						oImgHover.css({left:(pos.left+marginLeft-12),top:(pos.top+marginTop-12)}).show();
						if($.fn.ifixpng) {oImgHover.ifixpng(opts.imgDir+'blank.gif');}
					}
				},function(){
					oImgHover.remove();
				});
			}
			
   			if($this.is('img')){
   				imgTargetSrc = $this.css('cursor','pointer').attr('src');
   				if(opts.imgResizeScript){
   					if( imgTargetSrc.match(new RegExp("^"+opts.imgResizeScript,"g")) ){
   						imgTargetSrc=imgTargetSrc.replace(/.*img=([^&]*).*/gi,'$1');
   					}
   				}
   			}
	    	oOverlay.css({
				opacity: opts.overlay,
				background:opts.overlayColor
	    	});

   			//make action only on link that point to an image
   			if( !/\.jpg|\.jpeg|\.png|\.gif/i.test(imgTargetSrc) ){
	   			return true;
   			}
   			
   			$this.click(function(){
   				var zoomOpened = $('div.jqfancyzoombox');
   				if( zoomOpened.length > 0  ){
   					//if user click on an other image, cancel the previous loading
					if($('img:first',zoomOpened).attr('src') != imgTargetSrc){
	   					if( oLoading && oLoading.is(':visible') ) {
	   						__cancelLoading();
	   					}
					}
	   				else {//solve the double click pb
	   					return false;
	   				}
   				}
   				var o = $.extend({},opts,userOptions);
   				var closeBtn = $("img.jqfancyzoomclosebox");
   				if(closeBtn.length > 0){
   					var imCurrent = $('img:first',zoomOpened);
   					if(imgTargetSrc == imCurrent.attr('src')){
						//calculate the start point of the animation, it start from the image of the element clicked
						pos=imgTarget.offset();
						o=$.extend(
								o,
								{dimOri:{width:(imgTarget.outerWidth()),height:(imgTarget.outerHeight()),left:pos.left,top:(pos.top),'opacity':0}
							});
						closeZoomBox(o);
						return false;
   					}else {
   						//user click on an other image, close the first one
   						closeBtn.trigger('click');
   						//return false;
   					}
   				}
   				
   				//remove the overlay and Reset
		 	 	if(o.showoverlay && oOverlay) {oOverlay.empty().remove().css({'opacity':o.overlay});}
				//reset the img close and fix png on it if plugin available
				oImgClose.attr('src',o.imgDir+'closebox.png').appendTo('body').hide();
				if($.fn.ifixpng) {$.ifixpng(o.imgDir+'blank.gif');oImgClose.ifixpng(o.imgDir+'blank.gif');}
				oImgClose.unbind('click').click(function(){closeZoomBox(o);});

				//reset zoom box prop and add image zoom with a margin top of 15px = imgclose height / 2
				var oImgZoomBox=$('<div class="jqfancyzoombox"></div>').css(o.oImgZoomBoxProp);
				o = $.extend(o,{oImgZoomBox:oImgZoomBox});

   				var strTitle = imgTarget.attr('alt');
   				if(strTitle){
   					var oTitle = $('<div><center><table height=0 border="0" cellspacing=0 cellpadding=0><tr><td></td><td class="fancyTitle">'+strTitle+'</td><td></td></table></center></div>').css({marginTop:10,marginRight:15});
   					
   					var tdL = oTitle.find('td:first').css({'background':'url('+o.imgDir+'zoom-caption-l.png)',width:'13px',height:'26px'});
   					var tdR = oTitle.find('td:last').css({'background':'url('+o.imgDir+'zoom-caption-r.png)',width:'13px',height:'26px'});
   					var tdC = $('.fancyTitle',oTitle).css({'background':'url('+o.imgDir+'zoom-caption-fill.png)',
   							'padding':'0px 20px',
   							color:'#FFF',
   							'font-size':'14px'
   							});

   					if($.fn.ifixpng){
   						tdL.ifixpng(o.imgDir+'blank.gif');
   						tdR.ifixpng(o.imgDir+'blank.gif');
   						tdC.ifixpng(o.imgDir+'blank.gif');
   					}
   					oTitle.appendTo(oImgZoomBox);   					
   				}
   				var oImgZoom=$('<img />').attr('src',imgTargetSrc).click(function(){closeZoomBox(o);}).prependTo(oImgZoomBox);
				/** Manage zIndex **/
				var imagezindex= opts.imagezindex;
				oOverlay.css('zIndex', imagezindex-1);
				oImgZoomBox.css('zIndex',imagezindex);
				oImgClose.css('zIndex',(imagezindex+10));
				
				//be shure that the image to display is loaded open the zoom box, if not display a loading Image.
   				var imgPreload = new Image();
   				imgPreload.src = imgTargetSrc;
   				var $fctEndLoading = function(){
					if(bCancelLoading) {bCancelLoading=false;}
					else {
						if(__getFileName(imgPreload.src) == __getFileName($('img:first',oImgZoomBox).attr('src')) ){
							fctCalculateImageSize(o.autoresize);
							openZoomBox(imgTarget, o);
							__stoploading();
						}
					}
   				};
   				var fctCalculateImageSize = function (autoresize) {
   					//calcul de la taille de l'image
   					if(autoresize){
	   					var divCalculate = $('<div></div>').css({position:'absolute','top':0,'left':0,opacity:0,'border':'0px solid red'});
	   					var bResize = false;
	   					oImgZoom.appendTo(divCalculate);
						divCalculate.appendTo('body');
						imWidth = oImgZoom.width();
						imHeight = oImgZoom.height();
						maxWidth = $(window).width()*0.9;
						maxHeight = $(window).height()-100;
						if( maxHeight < imHeight ){
							bResize = true;
							oImgZoom.height(maxHeight);
							imWidth= (imWidth*maxHeight)/imHeight;
							oImgZoom.width(imWidth);
							if( maxWidth < imWidth ){
								oImgZoom.width(maxWidth);
								oImgZoom.height(imHeight*maxWidth/imWidth);
							}
						}else if( maxWidth < imWidth ){
							bResize = true;
							oImgZoom.width(maxWidth);
							oImgZoom.height(imHeight*maxWidth/imWidth);
						}
						//because ie do not resize image correctly
						if( bResize && o.imgResizeScript /*&& $.browser.msie*/ ){
							var tWidth = oImgZoom.width();
							var tHeight = oImgZoom.height();
							var finalWidth = tWidth;
							var tabSizes = new Array(1440,1280,1024,800,640,480,360);
							for(i=0;i<tabSizes.length;i++){
								if(tWidth > tabSizes[i]){
									finalWidth = tabSizes[i];
									break;
								}
							}
							oImgZoom.width(finalWidth);
							oImgZoom.height(parseInt(tHeight*finalWidth/tWidth));
							
							var args = "img="+encodeURI(oImgZoom.attr('src'));
							args += "&width="+oImgZoom.width();
							args += "&height="+oImgZoom.height();
							oImgZoom.attr('src',o.imgResizeScript+"?"+args);
						}
						divCalculate.remove();
					}	
	   				oImgZoom.prependTo(oImgZoomBox);
   				};
   				
   				if(imgPreload.complete)	{
   					fctCalculateImageSize(o.autoresize);
   					openZoomBox(imgTarget, o);	
	   				/*__displayLoading(imgPreload);
	   				setTimeout($fctEndLoading,4000);*/
   				}
	   			else {
	   				__displayLoading(o);
	   				imgPreload.onload = function(){
	   					//when loading is finish display the zoombox if user not click on cancel
	   					$fctEndLoading();
	   				};
	   			}
   				return false;		
   			});
   		}
   	);//end return this
    };//end Plugin

    
    //Default Options
    $.fn.fancyzoom.defaultsOptions = {
    	overlayColor: '#000',
    	overlay: 0.6,
    	imagezindex:100,
    	showoverlay:true,
    	Speed:400,
    	shadow:true,
    	shadowOpts:{ color: "#000", offset: 4, opacity: 0.2 },
    	imgDir:'ressources/',
    	imgResizeScript:null,
    	autoresize:true,

		// added by David McReynolds @ Daylight 10/16/2009
		showZoomIndicator:true
 	 };
 	 
	function __posCenter(iWidth,iHeight){
		var iLeft = ($(window).width() - iWidth) / 2 + $(window).scrollLeft();
		var iTop = ($(window).height() - iHeight) / 2 + $(window).scrollTop();
		iLeft=(iLeft < 0)?0:iLeft;
		iTop=(iTop < 0)?0:iTop;
	  		return {left:iLeft,top:iTop};
    }
    
    //
    // LOADING MANAGEMENT
    //
    var oLoading =null ;
	var bCancelLoading = false;
	var timerLoadingImg = null;
	function __displayLoading(o){
		if(!oLoading){
			oLoading = $('<div></div>').css({width:50,height:50,position:'absolute','background':'transparent',
			opacity:8/10,color:'#FFF',padding:'5px','font-size':'10px', zIndex: 10000});
		}
		oLoading.css(__posCenter(50,50)).html('<img src="'+o.imgDir+'blank.gif" />').click(function(){__cancelLoading();}).appendTo('body').show();
		timerLoadingImg=setTimeout(function(){__changeimageLoading(o);},400);
	}
	function __cancelLoading(){
		bCancelLoading=true;
		__stoploading();
	}
	function __stoploading(){
		oLoading.hide().remove();
		if(timerLoadingImg){
			clearTimeout(timerLoadingImg);
			timerLoadingImg=null;
		}
	}
	
	/**
	 * Animate the png loading image.
	 */
	function __changeimageLoading(o){
		if(oLoading && !oLoading.is(':visible')){
			timerLoadingImg=null;
			return;
		}
		
		var $im=$('img',oLoading);
		//First call im.src ="", set it to the fire png zoom spin
		if(!$im.attr('src') || /blank\.gif/.test($im.attr('src'))){
			strImgSrc = o.imgDir+"zoom-spin-1.png";
		}
		//rotate the im src until 12
		else {
			tab = $im.attr('src').split(/[- .]+/);
			iImg = parseInt(tab[2]);
			iImg = (iImg < 12)? (iImg+1):1;
			strImgSrc= tab[0]+"-"+tab[1]+"-"+iImg+"."+tab[3];
		}
		var pLoad = new Image();
		pLoad.src=strImgSrc;
		var $fct = function (){
			
			oLoading && oLoading.css(__posCenter(50,50));
			$im.attr('src',strImgSrc);
			timerLoadingImg = setTimeout(__changeimageLoading,100);
		};
		//to preserve bug if img not exist change it only if load complete.
		if(pLoad.complete){$fct();}
		else{pLoad.onload=$fct;}
	}
 	
 	function __getFileName(strPath){
 		if(!strPath) {return false;}
		var tabPath = strPath.split('/');
		return ((tabPath.length<1)?strPath:tabPath[(tabPath.length-1)]);		
 	}
 	
})(jQuery);