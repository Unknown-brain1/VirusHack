// Register service worker to control making site work offline

//if('serviceWorker' in navigator) {
  //navigator.serviceWorker
         //  .register('https://pwa.coxel.ru/sw.js', {scope: 'https://pwa.coxel.ru/'})
           //.then(function() { console.log('Service Worker Registered'); }, function(error) {
    //console.log('Service worker registration failed:', error);
  //});
//}

if('serviceWorker' in navigator) {
    navigator.serviceWorker
        .register('https://pwa.coxel.ru/sw.js')
        .then(function() { console.log('Service Worker Registered'); });
}

var options = {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0
};

function success(pos) {
    var crd = pos.coords;

    console.log('Ваше текущее метоположение:');
    console.log(`Широта: ${crd.latitude}`);
    console.log(`Долгота: ${crd.longitude}`);
    console.log(`Плюс-минус ${crd.accuracy} метров.`);
};

function error(err) {
    console.warn(`ERROR(${err.code}): ${err.message}`);
};

navigator.geolocation.getCurrentPosition(success, error, options);

Notification.requestPermission().then(function(result) {
    console.log(result);
});

function spawnNotification(body, icon, title) {
    var options = {
        body: 'Yay its works!',
        icons: 'img/logo512.png',
        badge: 'img/logo192.png',
    };
    var n = new Notification(title, options);
}




//var findMeButton = $('.find-me');
//findMeButton.on('click', function(e) {

  //  e.preventDefault();

    //navigator.geolocation.getCurrentPosition(function(position) {

      //  // Get the coordinates of the current possition.
       // var lat = position.coords.latitude;
       // var lng = position.coords.longitude;
        //console.log(position);
    //})

// Code to handle install prompt on desktop

    let deferredPrompt;
    const addBtn = document.querySelector('.add-button');
    //addBtn.style.display = 'none';

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;
        // Update UI to notify the user they can add to home screen
        addBtn.style.display = 'block';

        addBtn.addEventListener('click', (e) => {
            // hide our user interface that shows our A2HS button
            addBtn.style.display = 'none';
            // Show the prompt
            deferredPrompt.prompt();
            // Wait for the user to respond to the prompt
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted the A2HS prompt');
                } else {
                    console.log('User dismissed the A2HS prompt');
                }
                deferredPrompt = null;
            });
        });
    });