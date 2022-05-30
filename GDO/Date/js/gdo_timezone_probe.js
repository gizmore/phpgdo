"use strict";

window.GDO.Date = {
		
	probe: function() {
		var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
		var url = GDO_PROTOCOL + '://' + GDO_DOMAIN + GDO_WEB_ROOT + 'index.php?_mo=Date&_me=TimezoneDetect&_ajax=1&submit=1&timezone='+tz;
		var req = new XMLHttpRequest();
		req.addEventListener("load", function(response) {
			document.getElementById('form_tzform').style.display = 'none';
		});
		req.open("POST", url);
		req.send();
	},

};

document.addEventListener("DOMContentLoaded", function() {
	if (GDO_USER.JSON.user_timezone == 1) {
		GDO.Date.probe();
	}
});
