    // website theme setup
    const toggleButton = document.getElementById("theme-toggle");
    const icon = toggleButton.querySelector("i");

    // Load saved theme or fallback to system preference
    let theme = localStorage.getItem("theme");
    if (!theme) {
        const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
        theme = prefersDark ? "dark" : "light";
    }

    applyTheme(theme);
   
    window.addEventListener('storage', (event) => {
        if (event.key === 'theme') {
            const newTheme = event.newValue;
            if (newTheme) {
                applyTheme(newTheme);
            }
        }
    });

    // Toggle button click
    toggleButton.addEventListener("click", () => {
        theme = theme === "dark" ? "light" : "dark";
        localStorage.setItem("theme", theme);
        applyTheme(theme);
    });

    function applyTheme(theme) {
        // Set the data-theme attribute (this is what activates your CSS media queries)
        document.documentElement.setAttribute("data-theme", theme);

        // Update icon and tooltip
        const icon = document.querySelector("#theme-toggle i");
        const button = document.getElementById("theme-toggle");

        if (theme === "dark") {
            icon.classList.remove("fa-sun");
            icon.classList.add("fa-moon");
            button.title = "Current Theme: Dark";
        } else {
            icon.classList.remove("fa-moon");
            icon.classList.add("fa-sun");
            button.title = "Current Theme: Light";
        }
    }



    //Search Bar & Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const toggleSearchButton = document.getElementById('toggle-search');
        const searchBar = document.getElementById('search-bar');
        const toggleSidebarButton = document.getElementById('toggle-sidebar');
        const sideBar = document.getElementById('ke-sidebar');

        if (toggleSearchButton && searchBar) {
            toggleSearchButton.addEventListener('click', function() {
                if (searchBar.style.display === 'none' || searchBar.style.display === '') {
                    searchBar.style.display = 'block';
                } else {
                    searchBar.style.display = 'none';
                }
            });
        }
        if (toggleSidebarButton && sideBar) {
            toggleSidebarButton.addEventListener('click', function() {
                if (sideBar.style.display === 'none' || sideBar.style.display === '') {
                    sideBar.style.display = 'block';
                } else {
                    sideBar.style.display = 'none';
                }
            });
        }
    });

    
    // christmas animation
    document.addEventListener("DOMContentLoaded", function () {
        const snowContainer = document.querySelector(".snow-container");

        const particlesPerThousandPixels = 0.1;
        const fallSpeed = 1.25;
        const pauseWhenNotActive = true;
        const maxSnowflakes = 200;
        const snowflakes = [];

        let snowflakeInterval;
        let isTabActive = true;

        // Function to check if it's between Dec 22 and Dec 28
        function shouldStartSnow() {
            const now = new Date();
            const startDate = new Date(now.getFullYear(), 11, 22);  // December 22
            const endDate = new Date(now.getFullYear(), 11, 28, 23, 59, 59);  // December 28, 23:59:59
            
            return now >= startDate && now <= endDate;
            // return true;
        }

        function resetSnowflake(snowflake) {
            const size = Math.random() * 5 + 1;
            const viewportWidth = window.innerWidth - size; // Adjust for snowflake size
            const viewportHeight = window.innerHeight;

            snowflake.style.width = `${size}px`;
            snowflake.style.height = `${size}px`;
            snowflake.style.left = `${Math.random() * viewportWidth}px`; // Constrain within viewport width
            snowflake.style.top = `-${size}px`;

            const animationDuration = (Math.random() * 3 + 2) / fallSpeed;
            snowflake.style.animationDuration = `${animationDuration}s`;
            snowflake.style.animationTimingFunction = "linear";
            snowflake.style.animationName =
                Math.random() < 0.5 ? "fall" : "diagonal-fall";

            setTimeout(() => {
                if (parseInt(snowflake.style.top, 10) < viewportHeight) {
                    resetSnowflake(snowflake);
                } else {
                    snowflake.remove(); // Remove when it goes off the bottom edge
                }
            }, animationDuration * 1000);
        }

        function createSnowflake() {
            if (snowflakes.length < maxSnowflakes) {
                const snowflake = document.createElement("div");
                snowflake.classList.add("snowflake");
                snowflakes.push(snowflake);
                snowContainer.appendChild(snowflake);
                resetSnowflake(snowflake);
            }
        }

        function generateSnowflakes() {
            const numberOfParticles =
                Math.ceil((window.innerWidth * window.innerHeight) / 1000) *
                particlesPerThousandPixels;
            const interval = 5000 / numberOfParticles;

            clearInterval(snowflakeInterval);
            snowflakeInterval = setInterval(() => {
                if (isTabActive && snowflakes.length < maxSnowflakes) {
                    requestAnimationFrame(createSnowflake);
                }
            }, interval);
        }

        function handleVisibilityChange() {
            if (!pauseWhenNotActive) return;

            isTabActive = !document.hidden;
            if (isTabActive) {
                generateSnowflakes();
            } else {
                clearInterval(snowflakeInterval);
            }
        }

        // Only start snowflakes if it's between December 22 and December 28
        if (shouldStartSnow()) {
            document.body.style.backgroundImage = 'none';
            snowContainer.style.display = "block";
            generateSnowflakes();
        } else {
            snowContainer.style.display = "none";
            console.log('Snowflacks are not active at this time.');
        }

        window.addEventListener("resize", () => {
            clearInterval(snowflakeInterval);
            setTimeout(generateSnowflakes, 1000);
        });

        document.addEventListener("visibilitychange", handleVisibilityChange);
    });

    // new year animation
    document.addEventListener("DOMContentLoaded", function () {
        function shouldStartFireworks() {
            const now = new Date();
            let startYear = now.getFullYear();
        
            // If today is in January and less than or equal to the 5th, use the previous year
            if (now.getMonth() === 0 && now.getDate() <= 5) {
                startYear -= 1;
            }
        
            const startDate = new Date(startYear, 11, 31, 23, 59, 0); // December 31, 11:59 PM
            const endDate = new Date(startDate.getTime() + 5 * 24 * 60 * 60 * 1000); // 5 days later
        
            // console.log("Now:", now.toISOString());
            // console.log("Start Date:", startDate.toISOString());
            // console.log("End Date:", endDate.toISOString());
        
            return now >= startDate && now <= endDate;
            // return true;
        }
        

        // Get reference to the fireworks container
        let fireScreen = document.querySelector('.fireworksContainer');

        // Set screen dimensions
        let screen_width = window.innerWidth;
        let screen_height = window.innerHeight;

        // Define fire colors
        let colors = [
            '#ff7f00',  // Orange
            '#00ddff',  // Cyan
            '#ff53d1',  // Pink
            '#FFFF00',  // Yellow
            '#FF4500',  // Red-Orange
            '#FFF0F5',  // Lavender
            '#98FB98',  // Pale Green
            '#6A5ACD',  // Slate Blue
            '#F5FFFA',  // Mint Cream
            '#FF6347',  // Tomato
            '#32CD32',  // Lime Green
            '#FFD700',  // Gold
            '#FF1493'   // Deep Pink
        ];

        // Function to create a single firework group (fireG)
        function createFireworkGroup(barsCount) {
            const fireGroup = document.createElement('div');
            fireGroup.classList.add('fireG');
            fireGroup.style.setProperty('--bars', barsCount);

            // Create fireCrBr elements inside fireG
            for (let i = 1; i <= barsCount; i++) {
                const fireCrBr = document.createElement('div');
                fireCrBr.classList.add('fireCrBr');
                fireCrBr.style.setProperty('--barNumber', i);

                const fireCr = document.createElement('div');
                fireCr.classList.add('fireCr');
                
                fireCrBr.appendChild(fireCr);
                fireGroup.appendChild(fireCrBr);
            }

            return fireGroup;
        }

        // Function to add fireworks groups dynamically
        function addFireworks() {
            const numGroups = 3;  // Number of firework groups to display
            const barsPerGroup = 18;  // Number of bars per firework group
            
            for (let i = 0; i < numGroups; i++) {
                const fireworkGroup = createFireworkGroup(barsPerGroup);
                fireScreen.appendChild(fireworkGroup);
            }
        }

        // Function to trigger the fireworks animation
        function fireStart() {
            let fireWorks = document.querySelectorAll('.fireG');
            let cracker_x = 0, cracker_y = 0;

            fireWorks.forEach((fireWork, i) => {
                let rndColor = Math.floor(Math.random() * colors.length);
                fireWork.classList.remove('fired');
                fireWork.style.color = `${colors[rndColor]}`;

                cracker_x = Math.floor(Math.random() * screen_width);
                cracker_y = Math.floor(Math.random() * screen_height);
                
                fireWork.style.setProperty('--animationDelay', (i * 2) + 's');
                fireWork.style.top = cracker_y + 'px';
                fireWork.style.left = cracker_x + 'px';

                setTimeout(() => {
                    fireWork.classList.add('fired');
                }, 10);
            });
        }

        // Check if fireworks should start (December 31 at 11:59 PM, lasting for 10 days)
        if (shouldStartFireworks()) {
            document.body.style.backgroundImage = 'none';
            
            // Initialize fireworks
            addFireworks();
            
            // Start the fireworks animation every 3 seconds
            let fireTiming = setInterval(fireStart, 3000);
            document.addEventListener('click', fireStart);
            fireStart();
        } else {
            console.log('Fireworks are not active at this time.');

        }
        shouldStartFireworks();

        // Start the fireworks automatically when the page loads
        window.onload = shouldStartFireworks;
    });

    // holiday animation
    document.addEventListener("DOMContentLoaded", function () {
        const kenyanHolidays = {
            "01-01": "Happy New Year Kenyans 🎉",
            "04-01": "Happy Easter Monday Kenyans ✝️",
            "05-01": "Happy Labour Day Kenyans 💪",
            "06-01": "Happy Madaraka Day Kenyans 🇰🇪",
            "10-10": "Happy Mazingira Day Kenyans 🌍",
            "10-20": "Happy Mashujaa Day Kenyans 🦸",
            "12-12": "Happy Jamhuri Day Kenyans 🎊",
            "12-25": "Merry Christmas Kenyans 🎄",
            "12-26": "Happy Boxing Day Kenyans 🎁",
            // "09-13": "Happy Special Holiday Kenyans 🎉" // example for testing
        };

        // Get today's date in MM-DD format
        const today = new Date();
        const todayStr = today.toISOString().slice(5, 10); // e.g., "06-01"

        // Check if today is a holiday
        if (kenyanHolidays[todayStr]) {
            // Create a floating banner with Kenya flag emoji + message
            const banner = document.createElement("div");
            banner.textContent = "🇰🇪 " + kenyanHolidays[todayStr] + " 🇰🇪";
            banner.style.position = "fixed";
            banner.style.bottom = "0";
            banner.style.left = "0";
            banner.style.width = "100%";
            banner.style.background = "#006600";
            banner.style.color = "white";
            banner.style.textAlign = "center";
            banner.style.padding = "10px";
            banner.style.fontSize = "1.5rem";
            banner.style.fontWeight = "600";
            banner.style.zIndex = "99999999999999";
            banner.style.transition = "all 1s ease";
            
            document.body.appendChild(banner);

            // Hide banner after 5 seconds
            setTimeout(() => {
                banner.style.opacity = "0";
                banner.style.transform = "translateY(100%)";
                setTimeout(() => banner.remove(), 1000);
            }, 10000);
        }
    });


    // setInterval(function() {
    //     const threshold = window.innerWidth * 0.8; // 80% of device width
    //     if (window.outerWidth - window.innerWidth > threshold) {
    //         window.location.href = 'https://kiongos.co.ke';
    //     }
    // }, 1000);