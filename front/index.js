const applicationServerPublicKey = 'BNDcG4tU4OdBy0GpwzqJs-XBdFnS70NdNApD2MyrnbKldYifUPfGH2Xs45RvuLhfzzXXietd95F42SGdXYoNDOU';

if ('serviceWorker' in navigator) {
    navigator.serviceWorker
        .register('https://pwa.coxel.ru/sw.js')
        .then(function (swReg) {
            swRegistration = swReg;
            console.log('Service Worker Registered');
            pushStarter()
        });
}

let isSubscribed = false;
let swRegistration = null;
let homeLocation = JSON.parse(window.localStorage.getItem('homeLocation'));
let geoOptions = {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0
};
mapboxgl.accessToken = 'pk.eyJ1IjoiZXZnZW55LWdpc3QiLCJhIjoiY2s5dHMybDdvMG1yNTNscGNnMzl4MDE2cyJ9.6NFHqCdOdvwndPxHD3GoiQ';
let map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v11',
    center: [55.708622, 37.578495],
    zoom: 12
});


function geoSuccess(pos) {
    let crd = {
        latitude: pos.coords.latitude,
        longitude: pos.coords.longitude,
        accuracy: pos.coords.accuracy,
    };
    map.setCenter({lng: crd.longitude, lat: crd.latitude})
    if (homeLocation === null) geoWriteHome(crd)
    let lastLocation = JSON.parse(window.localStorage.getItem('location'));
    if (lastLocation)
        checkForDrive(crd, lastLocation) // Проверяем на сколько переместился юзер

    window.localStorage.setItem('location', JSON.stringify(crd))

    console.log('Широта: ' + crd.latitude);
    console.log('Долгота: ' + crd.longitude);
    console.log(' ')
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
    subscribeUser()
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

function pushStarter() {
    swRegistration.pushManager.getSubscription()
        .then(function (subscription) {
            isSubscribed = !(subscription === null);

            if (isSubscribed) {
                console.log('User IS subscribed.');
            } else {
                console.log('User is NOT subscribed.');
            }
        });
}

function subscribeUser() {
    if (!swRegistration) return false;
    const applicationServerKey = urlB64ToUint8Array(applicationServerPublicKey);
    swRegistration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: applicationServerKey
    })
        .then(function (subscription) {
            console.log('User is subscribed.');

            updateSubscriptionOnServer(subscription);

            isSubscribed = true;
        })
        .catch(function (error) {
            console.error('Failed to subscribe the user: ', error);
        });
}

geolocationWork();
setInterval(geolocationWork, 1000 * 15) // Запуск каждые 120 секунд

function urlB64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

function updateSubscriptionOnServer(subscription) {
    let json_to_call = (JSON.stringify(subscription))
    let settings = {
        "url": "https://hack.triptip.tours/api.php",
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Content-Type": "text/plain",
        },
        "data": JSON.stringify({
            "method": "client_key_store",
            "client_endpoint": json_to_call,
        }),
    };

    console.log(json_to_call)
    $.ajax(settings).done(function (response) {
        console.log(response);
    });
}

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


