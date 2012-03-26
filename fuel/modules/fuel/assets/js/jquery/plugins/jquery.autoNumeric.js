/**
 * autoNumeric.js
 * @author: Bob Knothe
 * @author: Sokolov Yura aka funny_falcon
 * @version: 1.7.0
 *
 * Copyright (c) 2011 Robert J. Knothe  http://www.decorplanit.com/plugin/
 * Copyright (c) 2011 Sokolov Yura aka funny_falcon http://github.com/funny_falcon/auto_numeric_js
 *
 * The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
(function(d){function m(s){var r={};if(s.selectionStart===undefined){s.focus();var q=document.selection.createRange();
r.length=q.text.length;q.moveStart("character",-s.value.length);r.end=q.text.length;
r.start=r.end-r.length}else{r.start=s.selectionStart;r.end=s.selectionEnd;r.length=r.end-r.start
}return r}function e(t,u,q){if(t.selectionStart===undefined){t.focus();var s=t.createTextRange();
s.collapse(true);s.moveEnd("character",q);s.moveStart("character",u);s.select()}else{t.selectionStart=u;
t.selectionEnd=q}}function c(q,r){d.each(r,function(t,v){if(typeof(v)==="function"){r[t]=v(q,r,t)
}else{if(typeof(v)==="string"){var u=v.substr(0,4);if(u==="fun:"){var s=d.autoNumeric[v.substr(4)];
if(typeof(s)==="function"){r[t]=d.autoNumeric[v.substr(4)](q,r,t)}else{r[t]=null}}else{if(u==="css:"){r[t]=d(v.substr(4)).val()
}}}}})}function p(r,q){if(typeof(r[q])==="string"){r[q]*=1}}function f(t,s){var w=d.extend({},d.fn.autoNumeric.defaults,s);
if(d.metadata){w=d.extend(w,t.metadata())}c(t,w);var v=w.vMax.toString().split(".");
var r=(!w.vMin&&w.vMin!==0)?[]:w.vMin.toString().split(".");p(w,"vMax");p(w,"vMin");
p(w,"mDec");w.aNeg=w.vMin<0?"-":"";if(typeof(w.mDec)!=="number"){w.mDec=Math.max((v[1]?v[1]:"").length,(r[1]?r[1]:"").length)
}if(w.altDec===null&&w.mDec>0){if(w.aDec==="."&&w.aSep!==","){w.altDec=","}else{if(w.aDec===","&&w.aSep!=="."){w.altDec="."
}}}var q=w.aNeg?"([-\\"+w.aNeg+"]?)":"(-?)";w._aNegReg=q;w._skipFirst=new RegExp(q+"[^-"+(w.aNeg?"\\"+w.aNeg:"")+"\\"+w.aDec+"\\d].*?(\\d|\\"+w.aDec+"\\d)");
w._skipLast=new RegExp("(\\d\\"+w.aDec+"?)[^\\"+w.aDec+"\\d]\\D*$");var u=(w.aNeg?w.aNeg:"-")+w.aNum+"\\"+w.aDec;
if(w.altDec&&w.altDec!==w.aSep){u+=w.altDec}w._allowed=new RegExp("[^"+u+"]","gi");
w._numReg=new RegExp(q+"(?:\\"+w.aDec+"?(\\d+\\"+w.aDec+"\\d+)|(\\d*(?:\\"+w.aDec+"\\d*)?))");
return w}function l(t,v,u){if(v.aSign){while(t.indexOf(v.aSign)>-1){t=t.replace(v.aSign,"")
}}t=t.replace(v._skipFirst,"$1$2");t=t.replace(v._skipLast,"$1");t=t.replace(v._allowed,"");
if(v.altDec){t=t.replace(v.altDec,v.aDec)}var q=t.match(v._numReg);t=q?[q[1],q[2],q[3]].join(""):"";
if(u){var r="^"+v._aNegReg+"0*(\\d"+(u==="leading"?")":"|$)");r=new RegExp(r);t=t.replace(r,"$1$2")
}return t}function n(r,q,u){if(q&&u){var t=r.split(q);if(t[1]&&t[1].length>u){if(u>0){t[1]=t[1].substring(0,u);
r=t.join(q)}else{r=t[0]}}}return r}function o(t,r,q){if(r&&r!=="."){t=t.replace(r,".")
}if(q&&q!=="-"){t=t.replace(q,"-")}if(!t.match(/\d/)){t+="0"}return t}function i(t,r,q){if(q&&q!=="-"){t=t.replace("-",q)
}if(r&&r!=="."){t=t.replace(".",r)}return t}function k(q,t){q=l(q,t);q=n(q,t.aDec,t.mDec);
q=o(q,t.aDec,t.aNeg);var r=q*1;return r>=t.vMin&&r<=t.vMax}function b(r,s,q){if(r===""||r===s.aNeg){if(s.wEmpty==="zero"){return r+"0"
}else{if(s.wEmpty==="sign"||q){return r+s.aSign}else{return r}}}return null}function g(t,x){t=l(t,x);
var w=b(t,x,true);if(w!==null){return w}var q="";if(x.dGroup===2){q=/(\d)((\d)(\d{2}?)+)$/
}else{if(x.dGroup===4){q=/(\d)((\d{4}?)+)$/}else{q=/(\d)((\d{3}?)+)$/}}var v=t.split(x.aDec);
if(x.altDec&&v.length===1){v=t.split(x.altDec)}var u=v[0];if(x.aSep){while(q.test(u)){u=u.replace(q,"$1"+x.aSep+"$2")
}}if(x.mDec!==0&&v.length>1){if(v[1].length>x.mDec){v[1]=v[1].substring(0,x.mDec)
}t=u+x.aDec+v[1]}else{t=u}if(x.aSign){var r=t.indexOf(x.aNeg)!==-1;t=t.replace(x.aNeg,"");
t=x.pSign==="p"?x.aSign+t:t+x.aSign;if(r){t=x.aNeg+t}}return t}function h(u,z,r,B){u=(u==="")?"0":u+="";
var q="";var w=0;var C="";if(u.charAt(0)==="-"){C=(u*1===0)?"":"-";u=u.replace("-","")
}if((u*1)>0){while(u.substr(0,1)==="0"&&u.length>1){u=u.substr(1)}}var y=u.lastIndexOf(".");
if(y===0){u="0"+u;y=1}if(y===-1||y===u.length-1){if(B&&z>0){q=(y===-1)?u+".":u;for(w=0;
w<z;w++){q+="0"}return C+q}else{return C+u}}var x=(u.length-1)-y;if(x===z){return C+u
}if(x<z&&B){q=u;for(w=x;w<z;w++){q+="0"}return C+q}var s=y+z;var t=u.charAt(s+1)*1;
var v=[];for(w=0;w<=s;w++){v[w]=u.charAt(w)}var A=(u.charAt(s)===".")?(u.charAt(s-1)%2):(u.charAt(s)%2);
if((t>4&&r==="S")||(t>4&&r==="A"&&C==="")||(t>5&&r==="A"&&C==="-")||(t>5&&r==="s")||(t>5&&r==="a"&&C==="")||(t>4&&r==="a"&&C==="-")||(t>5&&r==="B")||(t===5&&r==="B"&&A===1)||(t>0&&r==="C"&&C==="")||(t>0&&r==="F"&&C==="-")||(t>0&&r==="U")){for(w=(v.length-1);
w>=0;w--){if(v[w]==="."){continue}v[w]++;if(v[w]<10){break}}}for(w=0;w<=s;w++){if(v[w]==="."||v[w]<10||w===0){q+=v[w]
}else{q+="0"}}if(z===0){q=q.replace(".","")}return C+q}function a(r,q){this.options=q;
this.that=r;this.$that=d(r);this.formatted=false;this.io=f(this.$that,this.options);
this.value=r.value}a.prototype={init:function(q){this.value=this.that.value;this.io=f(this.$that,this.options);
this.cmdKey=q.metaKey;this.shiftKey=q.shiftKey;this.selection=m(this.that);if(q.type==="keydown"||q.type==="keyup"){this.kdCode=q.keyCode
}this.which=q.which;this.processed=false;this.formatted=false},setSelection:function(s,q,r){s=Math.max(s,0);
q=Math.min(q,this.that.value.length);this.selection={start:s,end:q,length:q-s};if(r===undefined||r){e(this.that,s,q)
}},setPosition:function(r,q){this.setSelection(r,r,q)},getBeforeAfter:function(){var r=this.value;
var s=r.substring(0,this.selection.start);var q=r.substring(this.selection.end,r.length);
return[s,q]},getBeforeAfterStriped:function(){var q=this.getBeforeAfter();q[0]=l(q[0],this.io);
q[1]=l(q[1],this.io);return q},normalizeParts:function(u,s){var v=this.io;s=l(s,v);
var t=s.match(/^\d/)?true:"leading";u=l(u,v,t);if((u===""||u===v.aNeg)){if(s>""){s=s.replace(/^0*(\d)/,"$1")
}}var r=u+s;if(v.aDec){var q=r.match(new RegExp("^"+v._aNegReg+"\\"+v.aDec));if(q){u=u.replace(q[1],q[1]+"0");
r=u+s}}if(v.wEmpty==="zero"&&(r===v.aNeg||r==="")){u+="0"}return[u,s]},setValueParts:function(u,s){var v=this.io;
var t=this.normalizeParts(u,s);var r=t.join("");var q=t[0].length;if(k(r,v)){r=n(r,v.aDec,v.mDec);
if(q>r.length){q=r.length}this.value=r;this.setPosition(q,false);return true}return false
},signPosition:function(){var v=this.io,t=v.aSign,s=this.that;if(t){var r=t.length;
if(v.pSign==="p"){var u=v.aNeg&&s.value&&s.value.charAt(0)===v.aNeg;return u?[1,r+1]:[0,r]
}else{var q=s.value.length;return[q-r,q]}}else{return[1000,-1]}},expandSelectionOnSign:function(r){var q=this.signPosition();
var s=this.selection;if(s.start<q[1]&&s.end>q[0]){if((s.start<q[0]||s.end>q[1])&&this.value.substring(Math.max(s.start,q[0]),Math.min(s.end,q[1])).match(/^\s*$/)){if(s.start<q[0]){this.setSelection(s.start,q[0],r)
}else{this.setSelection(q[1],s.end,r)}}else{this.setSelection(Math.min(s.start,q[0]),Math.max(s.end,q[1]),r)
}}},checkPaste:function(){if(this.valuePartsBeforePaste!==undefined){var r=this.getBeforeAfter();
var q=this.valuePartsBeforePaste;delete this.valuePartsBeforePaste;r[0]=r[0].substr(0,q[0].length)+l(r[0].substr(q[0].length),this.io);
if(!this.setValueParts(r[0],r[1])){this.value=q.join("");this.setPosition(q[0].length,false)
}}},skipAllways:function(u){var q=this.kdCode,v=this.which,r=this.cmdKey;if(q===17&&u.type==="keyup"&&this.valuePartsBeforePaste!==undefined){this.checkPaste();
return false}if((q>=112&&q<=123)||(q>=91&&q<=93)||(q>=9&&q<=31)||(q<8&&(v===0||v===q))||q===144||q===145||q===45){return true
}if(r&&q===65){return true}if(r&&(q===67||q===86||q===88)){if(u.type==="keydown"){this.expandSelectionOnSign()
}if(q===86){if(u.type==="keydown"||u.type==="keypress"){if(this.valuePartsBeforePaste===undefined){this.valuePartsBeforePaste=this.getBeforeAfter()
}}else{this.checkPaste()}}return u.type==="keydown"||u.type==="keypress"||q===67}if(r){return true
}if(q===37||q===39){var s=this.io.aSep,w=this.selection.start,t=this.that.value;if(u.type==="keydown"&&s&&!this.shiftKey){if(q===37&&t.charAt(w-2)===s){this.setPosition(w-1)
}else{if(q===39&&t.charAt(w)===s){this.setPosition(w+1)}}}return true}if(q>=34&&q<=40){return true
}return false},processAllways:function(){var q;if(this.kdCode===8||this.kdCode===46){if(!this.selection.length){q=this.getBeforeAfterStriped();
if(this.kdCode===8){q[0]=q[0].substring(0,q[0].length-1)}else{q[1]=q[1].substring(1,q[1].length)
}this.setValueParts(q[0],q[1])}else{this.expandSelectionOnSign(false);q=this.getBeforeAfterStriped();
this.setValueParts(q[0],q[1])}return true}return false},processKeypress:function(){var u=this.io;
var q=String.fromCharCode(this.which);var t=this.getBeforeAfterStriped();var s=t[0],r=t[1];
if(q===u.aDec||(u.altDec&&q===u.altDec)||((q==="."||q===",")&&this.kdCode===110)){if(!u.mDec||!u.aDec){return true
}if(u.aNeg&&r.indexOf(u.aNeg)>-1){return true}if(s.indexOf(u.aDec)>-1){return true
}if(r.indexOf(u.aDec)>0){return true}if(r.indexOf(u.aDec)===0){r=r.substr(1)}this.setValueParts(s+u.aDec,r);
return true}if(q==="-"||q==="+"){if(!u.aNeg){return true}if(s===""&&r.indexOf(u.aNeg)>-1){s=u.aNeg;
r=r.substring(1,r.length)}if(s.charAt(0)===u.aNeg){s=s.substring(1,s.length)}else{s=(q==="-")?u.aNeg+s:s
}this.setValueParts(s,r);return true}if(q>="0"&&q<="9"){if(u.aNeg&&s===""&&r.indexOf(u.aNeg)>-1){s=u.aNeg;
r=r.substring(1,r.length)}this.setValueParts(s+q,r);return true}return true},formatQuick:function(){var x=this.io;
var v=this.getBeforeAfterStriped();var u=g(this.value,this.io);var q=u.length;if(u){var s=v[0].split("");
var r;for(r in s){if(!s[r].match("\\d")){s[r]="\\"+s[r]}}var w=new RegExp("^.*?"+s.join(".*?"));
var t=u.match(w);if(t){q=t[0].length;if(((q===0&&u.charAt(0)!==x.aNeg)||(q===1&&u.charAt(0)===x.aNeg))&&x.aSign&&x.pSign==="p"){q=this.io.aSign.length+(u.charAt(0)==="-"?1:0)
}}else{if(x.aSign&&x.pSign==="s"){q-=x.aSign.length}}}this.that.value=u;this.setPosition(q);
this.formatted=true}};d.fn.autoNumeric=function(q){return this.each(function(){var r=d(this);
var s=new a(this,q);if(s.io.aForm&&(this.value||s.io.wEmpty!=="empty")){r.autoNumericSet(r.autoNumericGet(q),q)
}r.keydown(function(t){s.init(t);if(s.skipAllways(t)){s.processed=true;return true
}if(s.processAllways()){s.processed=true;s.formatQuick();t.preventDefault();return false
}else{s.formatted=false}return true}).keypress(function(t){var u=s.processed;s.init(t);
if(s.skipAllways(t)){return true}if(u){t.preventDefault();return false}if(s.processAllways()||s.processKeypress()){s.formatQuick();
t.preventDefault();return false}else{s.formatted=false}}).keyup(function(u){s.init(u);
var t=s.skipAllways(u);s.kdCode=0;delete s.valuePartsBeforePaste;if(t){return true
}if(this.value===""){return true}if(!s.formatted){s.formatQuick()}}).focusout(function(w){var x=s.io,v=r.val(),t=v;
if(v!==""){v=l(v,x);if(b(v,x)===null&&k(v,x)){v=o(v,x.aDec,x.aNeg);v=h(v,x.mDec,x.mRound,x.aPad);
v=i(v,x.aDec,x.aNeg)}else{v=""}}var u=b(v,x,false);if(u===null){u=g(v,x)}if(u!==t){r.val(u)
}if(u!==s.inVal){r.change();delete s.inVal}}).focusin(function(u){s.inVal=r.val();
var t=b(s.inVal,s.io,true);if(t!==null){r.val(t)}})})};function j(q){if(typeof(q)==="string"){q=q.replace(/\[/g,"\\[").replace(/\]/g,"\\]");
q="#"+q.replace(/(:|\.)/g,"\\$1")}return d(q)}d.autoNumeric={};d.autoNumeric.Strip=function(s,r){var t=f(j(s),r);
var q=j(s).val();q=l(q,t);q=o(q,t.aDec,t.aNeg);if(q*1===0){q="0"}return q};d.autoNumeric.Format=function(s,r,q){r+="";
var t=f(j(s),q);r=h(r,t.mDec,t.mRound,t.aPad);r=i(r,t.aDec,t.aNeg);if(!k(r,t)){r=h("",t.mDec,t.mRound,t.aPad)
}return g(r,t)};d.fn.autoNumericGet=function(q){return d.fn.autoNumeric.Strip(this,q)
};d.fn.autoNumericSet=function(r,q){return this.val(d.fn.autoNumeric.Format(this,r,q))
};d.autoNumeric.defaults={aNum:"0123456789",aSep:",",dGroup:"3",aDec:".",altDec:null,aSign:"",pSign:"p",vMax:"999999999.99",vMin:"0.00",mDec:null,mRound:"S",aPad:true,wEmpty:"empty",aForm:false};
d.fn.autoNumeric.defaults=d.autoNumeric.defaults;d.fn.autoNumeric.Strip=d.autoNumeric.Strip;
d.fn.autoNumeric.Format=d.autoNumeric.Format})(jQuery);