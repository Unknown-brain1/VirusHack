// Register service worker to control making site work offline

//if('serviceWorker' in navigator) {
//navigator.serviceWorker
//  .register('https://pwa.coxel.ru/sw.js', {scope: 'https://pwa.coxel.ru/'})
//.then(function() { console.log('Service Worker Registered'); }, function(error) {
//console.log('Service worker registration failed:', error);
//});
//}

if ('serviceWorker' in navigator) {
    navigator.serviceWorker
        .register('https://pwa.coxel.ru/sw.js')
        .then(function () {
            console.log('Service Worker Registered');
        });
}

let homeLocation = JSON.parse(window.localStorage.getItem('homeLocation'));

let geoOptions = {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0
};

function geoSuccess(pos) {
    let crd = {
        latitude: pos.coords.latitude,
        longitude: pos.coords.longitude,
        accuracy: pos.coords.accuracy,
    };
    if (homeLocation === null) geoWriteHome(crd)
    let lastLocation = JSON.parse(window.localStorage.getItem('location'));
    if (lastLocation)
        checkForDrive(crd, lastLocation) // Проверяем на сколько переместился юзер

    window.localStorage.setItem('location', JSON.stringify(crd))

    console.log('Ваше текущее метоположение:');
    console.log(`Широта: ${crd.latitude}`);
    console.log(`Долгота: ${crd.longitude}`);
    console.log(`Плюс-минус ${crd.accuracy} метров.`);
}

function checkForDrive(currentLocation, lastLocation) {
    let distanceFromHome = geoDistance(currentLocation.latitude, currentLocation.longitude, lastLocation.latitude, lastLocation.longitude)
    let lastHomeState = isHome();
    console.log('Дистанция от дома')
    console.log(distanceFromHome)
    if (distanceFromHome > 0.1) { // Если больше чем в 100 метрах от дома
        if (lastHomeState === null) {
            isHome(false);
        }
        if (lastHomeState) { // Если мы вышли
            console.log('Вышли на улицу')
            new Notification("Наденьте маску!", {
                body: "Мы заметили что вы вышли на улицу, не забывайте о маске",
            })
            window.localStorage.setItem('timeExit', (new Date()).getTime().toString())

        } else { // Если мы все еще на улице
            let timeExit = new Date(parseInt(window.localStorage.getItem('timeExit')));
            let timeAgo = Math.round((((new Date) - timeExit) / 1000 / 60)) // Время от выхода в минутах
            if (timeAgo >= 120) { // Время менять маску
                new Notification("Поменяйте маску!", {
                    body: "Уже два часа как вы на улице. Время менять маску!",
                })
                window.localStorage.setItem('timeExit', (new Date()).getTime().toString())
            }
        }
    } else { // Если мы дома
        if (lastHomeState === null) isHome(true)
        if (lastHomeState) { // Если мы уже были дома
            console.log('Дома хорошо')
        } else { // Если мы только пришли
            console.log('Пришли домой')
            isHome(true)
            new Notification("Время мыть руки!", {
                body: "Мы заметили что вы пришли домой, не забудьте помыть руки",
            })
        }
    }
}

function isHome(newState = undefined) {
    let lastState = JSON.parse(window.localStorage.getItem('isHome'))
    if (newState !== undefined) {
        window.localStorage.setItem('isHome', JSON.stringify(newState))
    }
    return lastState;
}

function geoWriteHome(coords) {
    if (!confirm('Установить дом в текущем местоположении?')) return;
    window.localStorage.setItem('homeLocation', JSON.stringify(coords))
    homeLocation = coords;
}

function geoError(err) {
    console.warn(`ERROR(${err.code}): ${err.message}`);
}

function geolocationWork() {
    navigator.geolocation.getCurrentPosition(geoSuccess, geoError, geoOptions);
}

function geoDistance(lat1, lon1, lat2, lon2) {
    if ((lat1 == lat2) && (lon1 == lon2)) {
        return 0;
    } else {
        var radlat1 = Math.PI * lat1 / 180;
        var radlat2 = Math.PI * lat2 / 180;
        var theta = lon1 - lon2;
        var radtheta = Math.PI * theta / 180;
        var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
        if (dist > 1) {
            dist = 1;
        }
        dist = Math.acos(dist);
        dist = dist * 180 / Math.PI;
        dist = dist * 60 * 1.1515;
        dist = dist * 1.609344; // В километры
        return dist;
    }
}

setInterval(geolocationWork, 1000 * 30) // Запуск каждые 30 секунд

//Notification.requestPermission().then(function(result) {
//console.log(result);
//});

//function spawnNotification(body, icon, title) {
//  var options = {
//     body: 'Yay its works!',
//  icons: 'img/logo512.png',
//    badge: 'img/logo192.png',
// };
//  var n = new Notification(title, options);
//}

function notifyMe() {
    // Проверка поддержки браузером уведомлений
    if (!("Notification" in window)) {
        alert("This browser does not support desktop notification");
    }

    // Проверка разрешения на отправку уведомлений
    else if (Notification.permission === "granted") {
        // Если разрешено, то создаем уведомление
        var notification = new Notification("Hi there!");
    }

    // В противном случае, запрашиваем разрешение
    else if (Notification.permission !== 'denied') {
        Notification.requestPermission(function (permission) {
            // Если пользователь разрешил, то создаем уведомление
            if (permission === "granted") {
                var notification = new Notification("HEalth!");
            }
        });
    }

    // В конечном счете, если пользователь отказался от получения
    // уведомлений, то стоит уважать его выбор и не беспокоить его
    // по этому поводу.
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
if (addBtn)
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
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