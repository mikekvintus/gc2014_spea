// Check if a new cache is available on page load.
window.addEventListener('load', function(e) {
    switch(window.applicationCache) {
        case applicationCache.UPDATEREADY:
            launchUpdateConfirm();
            break;
        // case applicationCache.ERR
        default:
            window.applicationCache.addEventListener('updateready', function(e) {
                if (window.applicationCache.status == window.applicationCache.UPDATEREADY) {
                    // Browser downloaded a new app cache.
                    launchUpdateConfirm();
	            }
            }, false);
            break;
    }
});


function launchUpdateConfirm() {
    if (confirm('A new version of this page is available. Load it?')) {
        window.location.reload();
    }	
}