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
        
            // return now >= startDate && now <= endDate;
            return true;
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
        // shouldStartFireworks();

        // Start the fireworks automatically when the page loads
        // window.onload = shouldStartFireworks;
    });