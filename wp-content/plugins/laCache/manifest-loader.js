// Check if a new cache is available on page load.
window.addEventListener('load', function(e) {

//changes, use something that is more compatible withe ie 8 at minimum.
//nevermind, it isn't useable in ie below 10 anyways.
    // alert('onload is finished');
    switch(window.applicationCache) {
        case applicationCache.UPDATEREADY:
            launchUpdateConfirm();
            break;
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








// function handleCacheEvent(e) {
//   //...
// }

// function handleCacheError(e) {
//   alert('Error: Cache failed to update!');
// };

// // Fired after the first cache of the manifest.
// appCache.addEventListener('cached', handleCacheEvent, false);

// // Checking for an update. Always the first event fired in the sequence.
// appCache.addEventListener('checking', handleCacheEvent, false);

// // An update was found. The browser is fetching resources.
// appCache.addEventListener('downloading', handleCacheEvent, false);

// // The manifest returns 404 or 410, the download failed,
// // or the manifest changed while the download was in progress.
// appCache.addEventListener('error', handleCacheError, false);

// // Fired after the first download of the manifest.
// appCache.addEventListener('noupdate', handleCacheEvent, false);

// // Fired if the manifest file returns a 404 or 410.
// // This results in the application cache being deleted.
// appCache.addEventListener('obsolete', handleCacheEvent, false);

// // Fired for each resource listed in the manifest as it is being fetched.
// appCache.addEventListener('progress', handleCacheEvent, false);

// // Fired when the manifest resources have been newly redownloaded.
// appCache.addEventListener('updateready', handleCacheEvent, false);