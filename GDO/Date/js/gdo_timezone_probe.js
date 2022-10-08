"use strict";

/**
 * Basic JS for Module_Date. 
 */
window.GDO.Date = {
	probe: function() {
		console.log('GDO.Date.probe()');
		var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
		var url = GDO_PROTOCOL + '://' + GDO_DOMAIN + GDO_WEB_ROOT + 'index.php?_mo=Date&_me=TimezoneDetect&_ajax=1&submit=1&timezone='+tz;
		var req = new XMLHttpRequest();
		req.addEventListener("load", function() {
			let form = document.getElementById('form_tzform');
			form && (form.style.display = 'none');
		});
		req.open("POST", url);
		req.send();
	},
};

document.addEventListener("DOMContentLoaded", function() {
	if (window.GDO_USER.JSON.timezone == 1) {
		GDO.Date.probe();
	}
});
