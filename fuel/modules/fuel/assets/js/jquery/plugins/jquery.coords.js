jQuery.fn.xCoord = function() {
	eElement = jQuery(this).get(0);
	var nLeftPos = eElement.offsetLeft;
	var eParElement = eElement.offsetParent;
	while (eParElement != null){
	nLeftPos += eParElement.offsetLeft;
		eParElement = eParElement.offsetParent;
	}
	return nLeftPos;
};

jQuery.fn.yCoord = function() {
	eElement = jQuery(this).get(0);
	var nTopPos = eElement.offsetTop;
	var eParElement = eElement.offsetParent;
	while (eParElement != null){
		nTopPos += eParElement.offsetTop;
		eParElement = eParElement.offsetParent;
	}
	return nTopPos;
};

