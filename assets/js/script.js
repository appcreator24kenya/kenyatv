    // show notification handler
    document.addEventListener("DOMContentLoaded", () => {
        const bellIcon = document.querySelector('.bell-icon');
        const notificationDropdown = document.querySelector('.notification-dropdown');
        const notificationElement = bellIcon.querySelector('[data-notification-count]');
        const notificationSound = new Audio('https://kenyalivetv.co.ke/assets/audio/notification.mp3');
        // const notificationSound = new Audio('https://cdn.pixabay.com/download/audio/2023/10/27/audio_8ab11e07a4.mp3');

        const FALLBACK_ICON = 'https://via.placeholder.com/50';

        // Request notification permission
        function requestNotificationPermission() {
            if (Notification.permission === "default") {
                Notification.requestPermission().then((permission) => {
                    if (permission !== "granted") {
                        console.warn("Notifications are blocked.");
                    }
                });
            }
        }

        // Convert HH:MM:SS to Date object for today
        function timeStringToDate(timeStr) {
            const [hour, minute, second] = timeStr.split(':').map(Number);
            const now = new Date();
            return new Date(now.getFullYear(), now.getMonth(), now.getDate(), hour, minute, second || 0);
        }

        // Convert to 12-hour display
        function formatTimeTo12Hour(timeStr) {
            const [hour, minute] = timeStr.split(':').map(Number);
            const period = hour >= 12 ? 'PM' : 'AM';
            const formattedHour = hour % 12 || 12;
            return `${formattedHour}:${minute.toString().padStart(2, '0')} ${period}`;
        }

        function calculateProgress(startTime, endTime) {
            const now = new Date();
            const start = timeStringToDate(startTime);
            const end = timeStringToDate(endTime);
            if (now < start) return -1;
            if (now > end) return 100;
            return ((now - start) / (end - start)) * 100;
        }

        function sendSoundNotification() {
            notificationSound.play().catch(err => console.warn("Notification sound error:", err));
        }

        // New function to handle the visual effect
        function addFadeEffectToNotification(itemDiv) {
            const iconDiv = itemDiv.querySelector('.notification-icon');
            if (iconDiv) {
                iconDiv.classList.add('fa-fade');
            }
            setTimeout(() => {
                if (iconDiv) {
                    iconDiv.classList.remove('fa-fade');
                }
            }, 5000); 
        }

        function updateNotificationDropdown() {
            notificationDropdown.innerHTML = "";
            const reminders = JSON.parse(localStorage.getItem('showReminders')) || {};
            const now = new Date();
            const currentDay = now.toLocaleString('en-US', { weekday: 'long' });
            let upcomingNotificationCount = 0;

            const sortedReminders = Object.entries(reminders)
                .filter(([_, data]) => Array.isArray(data.showScheduleDay) && data.showScheduleDay.includes(currentDay))
                .sort(([, a], [, b]) => timeStringToDate(a.startTime) - timeStringToDate(b.startTime));

            sortedReminders.forEach(([key, data]) => {
                const {
                    showType, channelName, channelIcon, radioName, radioIcon,
                    showName, startTime, endTime, channelPage, radioPage, notified
                } = data;

                const startDate = timeStringToDate(startTime);
                const endDate = timeStringToDate(endTime);
                const isPlaying = now >= startDate && now <= endDate;
                const timeDifference = (startDate - now) / 1000 / 60; // minutes

                if (now > endDate) return;

                if (timeDifference > 1 || isPlaying) {
                    upcomingNotificationCount++;
                }

                // Trigger notification 1-2 mins before
                if (!notified && timeDifference > 1 && timeDifference <= 2) {
                    sendSoundNotification();
                    addFadeEffectToNotification(itemDiv);

                    const icon = showType === 'tv' ? (channelIcon || FALLBACK_ICON) : (radioIcon || FALLBACK_ICON);
                    const body = `Don't miss the ${showName} starting at ${formatTimeTo12Hour(startTime)}!`;

                    if (navigator.serviceWorker?.controller) {
                        navigator.serviceWorker.controller.postMessage({
                            title: "Show Reminder",
                            body,
                            url: showType === 'tv' ? channelPage : radioPage,
                            icon,
                        });
                    } else {
                        if (Notification.permission === "granted") {
                            new Notification("Show Reminder", {
                                body,
                                icon,
                            });
                        }
                    }

                    data.notified = true;
                    reminders[key] = data;
                    localStorage.setItem("showReminders", JSON.stringify(reminders));
                }

                const itemDiv = document.createElement('div');
                itemDiv.classList.add('notification-item');
                itemDiv.dataset.url = showType === 'tv' ? channelPage : radioPage;
                const progress = calculateProgress(startTime, endTime);

                itemDiv.setAttribute('title', isPlaying
                    ? `${showName} is currently playing!`
                    : (progress < 0 ? `${showName} has not started yet!` : '')
                );

                const iconSrc = showType === 'tv' ? (channelIcon || FALLBACK_ICON) : (radioIcon || FALLBACK_ICON);
                const sourceName = showType === 'tv' ? channelName : radioName;

                itemDiv.innerHTML = `
                    <div class="notification-icon">
                        <img src="${iconSrc}" alt="${showName}">
                    </div>
                    <div class="notification-details">
                        <div class="notification-title">${showName}</div>
                        <div class="notification-meta">${sourceName}</div>
                        <div class="notification-meta">${formatTimeTo12Hour(startTime)} - ${formatTimeTo12Hour(endTime)}</div>
                        <div class="progress-bar">
                            <div class="show-type-label">${showType === 'tv' ? 'TV' : 'Radio'}</div>
                            <div class="progress ${progress > 0 && progress < 100 ? 'active' : ''}" style="width: ${progress}%"></div>
                        </div>
                    </div>
                `;

                itemDiv.addEventListener('click', () => {
                    window.open(showType === 'tv' ? channelPage : radioPage, '_blank');
                });

                notificationDropdown.appendChild(itemDiv);
            });

            notificationElement.setAttribute('data-notification-count', upcomingNotificationCount.toString());

            if (upcomingNotificationCount === 0) {
                notificationDropdown.innerHTML = `
                    <div class="no-notifications">
                        <strong>No Upcoming Shows At This Time</strong>
                        <p>There are no scheduled TV shows or radio programs for today or already ended. Check back later or set reminders for future shows.</p>
                    </div>
                `;
            }
        }

        bellIcon.addEventListener('click', () => {
            notificationDropdown.classList.toggle('active');
        });

        setInterval(updateNotificationDropdown, 10000);

        // This is the new part for cross-tab synchronization
        window.addEventListener('storage', (event) => {
            if (event.key === 'showReminders') {
                updateNotificationDropdown();
            }
        });

        // Initial load and setup
        updateNotificationDropdown();
        requestNotificationPermission();
    });  
    
    
    // general functions
    document.addEventListener('DOMContentLoaded', () => {
        const cookieBanner = document.getElementById('cookie-banner');
        const acceptCookies = document.getElementById('accept-cookies');
    
        if (!cookieBanner || !acceptCookies) {
            // Exit early if elements are missing
            return;
        }
    
        // Function to check and update the banner display
        function updateBannerState() {
            if (localStorage.getItem('cookiesAccepted')) {
                cookieBanner.style.display = 'none';
            } else {
                // Only show the banner after a delay if cookies haven't been accepted
                setTimeout(() => {
                    cookieBanner.style.display = 'flex';
                }, 30000);
            }
        }
    
        // Initial check on page load
        updateBannerState();
    
        // Accept button click
        acceptCookies.addEventListener('click', () => {
            localStorage.setItem('cookiesAccepted', 'true');
            cookieBanner.style.display = 'none';
        });
    
        // Sync across tabs
        window.addEventListener('storage', (event) => {
            if (event.key === 'cookiesAccepted') {
                updateBannerState();
            }
        });

        // Center most watched TV on homepage (for screens <= 400px)
        if (window.innerWidth <= 400) {
            const container = document.querySelector('.featured-content-area .center-featured');
        
            if (container && container.children.length > 1) {
                const secondChild = container.children[1];
        
                const containerWidth = container.clientWidth;
                const secondChildLeftOffset = secondChild.offsetLeft;
                const secondChildWidth = secondChild.clientWidth;
        
                // Scroll so the second item is centered
                container.scrollLeft = secondChildLeftOffset - (containerWidth / 2) + (secondChildWidth / 2);
            }
        }

        // check internet connection
        const offlineMessage = document.getElementById('offline-message');
        const onlineMessage = document.getElementById('online-message');
        if (offlineMessage && onlineMessage) {
            function updateOnlineStatus() {
                if (navigator.onLine) {
                    console.log('Online');
                    offlineMessage.style.display = 'none';
                    //onlineMessage.style.display = 'block';
                    setTimeout(() => {
                        onlineMessage.style.display = 'none';
                    }, 3000); // Display online message for 3 seconds
                } else {
                    console.log('Offline');
                    onlineMessage.style.display = 'none';
                    offlineMessage.style.display = 'block';
                }
            }
        }
        
        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
        updateOnlineStatus(); // Initial check when the page loads

        // handle shows scroll buttons
        const sectionContainer = document.querySelector('.live-tv-radio-shows-container');

        if (sectionContainer) {
            const scrollContainer = sectionContainer.querySelector('.live-tv-radio-shows-items'); 
            const scrollLeftButton = sectionContainer.querySelector('.fa-chevron-circle-left'); 
            const scrollRightButton = sectionContainer.querySelector('.fa-chevron-circle-right'); 
        
            if (scrollContainer && scrollLeftButton && scrollRightButton) {
                scrollLeftButton.addEventListener('click', function () {
                    scrollContainer.scrollBy({
                        left: -300,
                        behavior: 'smooth'
                    });
                });
        
                scrollRightButton.addEventListener('click', function () {
                    scrollContainer.scrollBy({
                        left: 300,
                        behavior: 'smooth'
                    });
                });
            } else {
                // console.error('Scroll buttons or container not found.');
            }
        }

        document.addEventListener("keydown", function (event) {
            if (event.key === "F12" || event.keyCode === 123) {
                event.preventDefault();
                return false;
            }
            if (event.ctrlKey && event.shiftKey && event.key.toLowerCase() === "i") {
                event.preventDefault();
                return false;
            }
            if (event.ctrlKey && event.shiftKey && event.key.toLowerCase() === "j") {
                event.preventDefault();
                return false;
            }
            if (event.ctrlKey && event.shiftKey && event.key.toLowerCase() === "c") {
                event.preventDefault();
                return false;
            }
            if (event.ctrlKey && event.key.toLowerCase() === "s") {
                event.preventDefault();
                return false;
            }
            if (event.ctrlKey && event.key.toLowerCase() === "u") {
                event.preventDefault();
                return false;
            }
        });

        document.addEventListener("contextmenu", function (event) {
            event.preventDefault();
            return false;
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        let loginTimeoutId = null; // global

        function showLoginFormAfterDelay(container, delay) {
            // Only start timer if not logged in
            if (localStorage.getItem("isLoggedIn") === "true") return;

            container.style.display = "none";
            loginTimeoutId = setTimeout(() => {
                // Check again before showing
                if (localStorage.getItem("isLoggedIn") !== "true") {
                    container.style.display = "flex";
                }
            }, delay);
        }

        function setLoginCookieOnce(name, value, days) {
            // Don’t overwrite if cookie already exists
            if (document.cookie.split('; ').some(cookie => cookie.startsWith(name + '='))) {
                return;
            }

            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        function handleSuccessfulLogin(type) {
            localStorage.setItem("isLoggedIn", "true");
            localStorage.removeItem("loginStartTime");

            setLoginCookieOnce("user_login_type", type, 3650);
            document.querySelector(".user-login-container").style.display = "none";

            // Cancel scheduled login popup
            if (loginTimeoutId) {
                clearTimeout(loginTimeoutId);
                loginTimeoutId = null;
            }

            // Dispatch custom event so other scripts can react
            document.dispatchEvent(new Event("userLoggedIn"));
        }

        // Run once on page load
        document.addEventListener("DOMContentLoaded", () => {
            const loginContainer = document.querySelector(".user-login-container");
            if (loginContainer) {
                showLoginFormAfterDelay(loginContainer, 3 * 60 * 1000); // 3 minutes
            }
        });

        function setupDatabase() {
            const request = indexedDB.open("UserDB", 1);

            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                if (!db.objectStoreNames.contains("users")) {
                    db.createObjectStore("users", { keyPath: "email" });
                }
            };

            request.onsuccess = () => {
                checkIfLoggedIn();
            };

            request.onerror = (event) => {
                console.error("Database setup failed:", event.target.error);
            };
        }

        window.checkIfLoggedIn = function (forceImmediate = false) {
            return new Promise((resolve) => {
                const loginContainer = document.querySelector(".user-login-container");
                const loginStartTimeKey = "loginStartTime";
                const delay = 180000; // 3 minutes
                const currentTime = Date.now();

                // Math question shortcut (fixed)
                const mathQuestionFlag = localStorage.getItem("mathQuestionAnswered");
                if (mathQuestionFlag === "true") {
                    loginContainer.style.display = "none";
                    localStorage.removeItem(loginStartTimeKey);
                    setLoginCookieOnce("user_login_type", "math", 3650);
                    localStorage.setItem("isLoggedIn", "true");
                    return resolve(true);
                }

                // IndexedDB check
                const request = indexedDB.open("UserDB", 1);
                request.onsuccess = (event) => {
                    const db = event.target.result;
                    const transaction = db.transaction("users", "readonly");
                    const store = transaction.objectStore("users");
                    const query = store.getAll();

                    query.onsuccess = () => {
                        if (query.result.length > 0) {
                            // Logged in
                            loginContainer.style.display = "none";
                            localStorage.removeItem(loginStartTimeKey);
                            setLoginCookieOnce("user_login_type", "email", 3650);
                            localStorage.setItem("isLoggedIn", "true");
                            resolve(true);
                        } else {
                            // Not logged in
                            localStorage.setItem("isLoggedIn", "false");
                            if (forceImmediate) {
                                // Show instantly (when rating clicked)
                                loginContainer.style.display = "flex";
                            } else {
                                // Default delayed behavior
                                const storedStartTime = parseInt(localStorage.getItem(loginStartTimeKey), 10);
                                if (isNaN(storedStartTime)) {
                                    localStorage.setItem(loginStartTimeKey, currentTime);
                                    showLoginFormAfterDelay(loginContainer, delay);
                                } else {
                                    const remainingTime = delay - (currentTime - storedStartTime);
                                    if (remainingTime > 0) {
                                        showLoginFormAfterDelay(loginContainer, remainingTime);
                                    } else {
                                        loginContainer.style.display = "flex";
                                    }
                                }
                            }
                            resolve(false);
                        }
                    };

                    query.onerror = () => {
                        // console.error("Error checking user data");
                        resolve(false);
                    };
                };
            });
        };

        function showLoginFormAfterDelay(container, delay) {
            container.style.display = "none";
            setTimeout(() => {
                container.style.display = "flex";
            }, delay);
        }

        function addUser(email, password) {
            const request = indexedDB.open("UserDB", 1);

            request.onsuccess = (event) => {
                const db = event.target.result;
                const transaction = db.transaction("users", "readwrite");
                const store = transaction.objectStore("users");

                const hashedPassword = btoa(password);
                const user = { email, password: hashedPassword };

                const addRequest = store.add(user);

                addRequest.onsuccess = () => {
                    // console.log("User added successfully!");
                    handleSuccessfulLogin("email");
                };

                addRequest.onerror = (event) => {
                    // console.error("Error adding user:", event.target.error);
                };
            };
        }

        function generateMathQuestion() {
            const num1 = Math.floor(Math.random() * 20);
            const num2 = Math.floor(Math.random() * 20);
            const isAddition = Math.random() > 0.5;
            const operator = isAddition ? "+" : "-";
            const answer = isAddition ? num1 + num2 : num1 - num2;

            document.getElementById("math-question").textContent = `What is ${num1} ${operator} ${num2}?`;
            return answer;
        }

        setupDatabase();

        const mathLoginButton = document.getElementById("math-login");
        const emailLoginButton = document.getElementById("email-login");
        const mathForm = document.getElementById("math-form");
        const emailForm = document.getElementById("email-form");

        let correctAnswer;

        // Show Math Login Form
        mathLoginButton.addEventListener("click", () => {
            emailForm.style.display = "none";
            mathForm.style.display = "block";
            correctAnswer = generateMathQuestion();
        });

        // Show Email/Password Login Form
        emailLoginButton.addEventListener("click", () => {
            mathForm.style.display = "none";
            emailForm.style.display = "block";
        });

        // Math Form Submission
        mathForm.addEventListener("submit", (event) => {
            event.preventDefault();
            const userAnswer = parseInt(document.getElementById("math-answer").value, 10);

            if (userAnswer === correctAnswer) {
                alert("Math answer is correct! You are logged in.");
                localStorage.setItem("mathQuestionAnswered", "true");
                handleSuccessfulLogin("math");
            } else {
                alert("Incorrect answer. Please try again.");
            }
        });

        // Email/Password Form Submission
        emailForm.addEventListener("submit", (event) => {
            event.preventDefault();
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;

            if (email && password) {
                addUser(email, password);
            } else {
                alert("Please fill out both fields!");
            }
        });
    });
    
    // ad blocker detector 2
    var overlay = document.getElementById('adBlockerContainer');
    if (overlay) {
        justDetectAdblock.detectAnyAdblocker().then(function(detected) {
        if(detected){
            overlay.style.display = "block";
        }
        else {
            overlay.style.display = "none"; 
        }
        });
    }