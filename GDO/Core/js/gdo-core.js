"use strict";

/**
 * Let'z'eh'goh with GDO namespace
 */
window.GDO = {};

/**
 * Automatically focus the first writeable + required form field.
 * If none, focus the first writeable form field.
 * If none, all is ok.
 */
window.GDO.autofocusForm = function() {
	let el = window.document.querySelector('[gdo-focus-required]');
	if (el) {
		el.focus();
	} else {
		el = window.document.querySelector('[gdo-focus]');
		el && el.focus();
	}
};

window.GDO.enterForm = function(form, event) {
//	console.log('GDO.enterForm()', form, event);
	if (event.keyCode == 13) {
		let nn = event.srcElement.nodeName;
		if ( (nn === 'INPUT') || (nn === 'SELECT') ) {
			event.preventDefault();
			// @TODO This is horrible stupid to press the first submit you see in a doc -.-
			let submits = form.querySelectorAll('input[type=submit]');
			submits[0] && submits[0].click();
		}
	}
};

window.GDO.triggerResize = function(time) {
	time = time ? time : 1000;
	setTimeout(
		GDO.triggerEvent.bind(window, 'resize')
		, time);
};

window.GDO.triggerEvent = function(name) {
	if (typeof(Event) === 'function') {
		window.dispatchEvent(new Event(name));
	}
	else {
		var evt = window.document.createEvent('UIEvents'); 
		evt.initUIEvent(name, true, false, window, 0); 
		window.dispatchEvent(evt);
	}
};

/**
 * Init GDOv7js612
 * @returns interest
 */
document.addEventListener('DOMContentLoaded', function(){
	setTimeout(window.GDO.autofocusForm, 1);
}, false);

window.GDO.toggleAll = function(toggler) {
	console.log(toggler);
	var tc = "."+toggler.getAttribute('gdo-toggle-class');
	console.log(tc);
	var cbxes = window.document.querySelectorAll(tc);
	cbxes.forEach(function(cbx){
		cbx.checked = toggler.checked;
	});
};

window.GDO.responseError = function(response) {
	let message = JSON.stringify(response);
	
	if ( (!response.json) && (response.responseJSON) ) {
		response.json = response.responseJSON.json;
	}
	
	if (response.json && response.json.error) {
		message = response.json.error;
	}
	else if (response.json && response.json.topResponse) {
		let r = response.json.topResponse;
		if (r.error) {
			message = r.error;
		} else {
			message = JSON.stringify(r);
		}
	}
	else if (response.error) {
		message = response.error;
	}
	
	if (response.json && response.json.stack) {
		message += "\n\n" + response.json.stack;
	}
	
	return window.GDO.error(message, "Error");
};

/**
 * This method is a candidate to get overriden by JS frameworks.
 */
window.GDO.error = function(html, title) {
	alert(title + " - " + html);
};

window.GDO.exception = function(ex) {
	console.error(ex);
	return window.GDO.responseError({json:{error: ex.message, stack: ex.stack}});
};

////////////////////
// --- Dialog --- //
////////////////////

window.GDO.DIALOG_RESULT = null;

window.GDO.closeDialog = function(dialogId, result) {
	console.log('GDO.closeDialog()', dialogId, result);
	window.GDO.DIALOG_RESULT = result;
	let wrap = document.querySelector('#'+dialogId);
	let onclose = wrap.getAttribute('gdo-on-close');
	let dlg = document.querySelector('#'+dialogId+' dialog');
	dlg.close();
	wrap.style.display = 'none';
	if (onclose) {
		eval(onclose);
	}
}

window.GDO.openDialog = function(dialogId) {
	console.log('GDO.openDialog()', dialogId);
	var dlg = document.querySelector('#'+dialogId+' dialog');
	if (!dlg) {
		console.error('Cannot find dialog with id ' + dialogId);
		return;
	}
	let wrap = document.querySelector('#'+dialogId);
	wrap.style.display = 'block';
	dlg.showModal();
};

// ----------- //
// --- XHR --- //
// ----------- //

window.GDO.xhr = function(url, verb, data) {
	return fetch(url, {
		method: verb||'GET',
		headers: {'Content-Type': 'application/json'},
		body: JSON.stringify(data)
	});
};

window.GDO.href = function(module, method, append) {
	let href = window.GDO_WEB_ROOT + 'index.php';
	href += '?_mo=' + module;
	href += '&_me=' + method;
	href += '&_lang=' + window.GDO_LANGUAGE;
	href += append;
	return href;
};

window.GDO.gdoxhr = function(module, method, append, verb, data) {
	const href = window.GDO.href(module, method, append);
	return window.GDO.xhr(href, verb, data);
};

var origOpen = XMLHttpRequest.prototype.open;
XMLHttpRequest.prototype.open = function () {
	let result = origOpen.apply(this, arguments);
	let token = document.querySelector('meta[name="csrf-token"]');
	token = token ? token.getAttribute('content') : 'not-there';
	this.setRequestHeader('X-CSRF-TOKEN', token);
	this.withCredentials = true;
	return result;
};

// ------------ //
// --- Data --- //
// ------------ //
/**
 * Get URL GET parameter.
 * @since 6.11.3
 */
window.GDO.GET = function(sParam, def) {
    let sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName;
    for (let i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return def;
};
