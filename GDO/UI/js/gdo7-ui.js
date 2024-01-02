"use strict";

window.GDO.UI = {

    timeoutSaveRes: null,

    saveResolution: function () {
        let data = {
            w: document.documentElement.clientWidth,
            h: document.documentElement.clientHeight,
        };
        window.GDO.UI.timeoutSaveRes = null;
        window.GDO.gdoxhr('UI', 'Resolution', '&_ajax=1&_fmt=json', 'POST', data);

    }
};

window.onresize = function () {
    if (window.GDO.UI.timeoutSaveRes) {
        clearTimeout(window.GDO.UI.timeoutSaveRes);
    }
    window.GDO.UI.timeoutSaveRes = setTimeout(window.GDO.UI.saveResolution, 1000);
};
