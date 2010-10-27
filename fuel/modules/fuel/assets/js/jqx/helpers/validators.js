function validDate(sDate){
	if (sDate.constructor == String){
	    var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/
		if (!re.test(sDate)) return false;
	}
	var fields = sDate.split("/");
	var d = new Date(sDate);
	if (d.getMonth() != (parseInt(fields[0]) -1)) return false;
	if (d.getDate() != fields[1]) return false;
	if (d.getFullYear() != fields[2]) return false;
    return (d != "Invalid Date");
}


function isEmpty(val){
	if (!val || val == "" || parseInt(val) == 0) return true;
	return false;
}

function hasValue(val, values){
	$.each(values, function(i, wn){
		if (n == val) return true;
	});
	return false;
}

function validEmail(val){
	var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return (filter.test(val));
}