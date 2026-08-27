<!-- Current Daytime -->
<div class='calender-box mobile-hide'>
    <div>" . date('D') . "</div>
    <div class='day'>" . date('d') . "</div>
    <div class='month'>" . date('M') . "</div>
    <div class='year'>" . date('y') . "</div>
</div>
<!-- Cookie Consent Banner -->
<div id='cookie-banner' class='cookie-banner'>
    <div class='cookie-content'>
        <div class='cookie-text'>
            <p>
                We use cookies to improve your experience on our site. By using our website, you agree to our 
                <a href='{$base_url}/privacy/' target='_blank'>Privacy Policy</a>.
            </p>
        </div>
        <div class='cookie-buttons'>
            <button id='accept-cookies' class='accept-btn'>Accept</button>
        </div>
    </div>
</div>
<!-- login form container  -->
<div class='user-login-container'>
    <div class='login-message'>
        <p>To continue using the website, you must be logged in.</p>
        <p>Your login information is stored on your browser only.</p>
    </div>
    <div class='login-options'>
        <button id='math-login'>Solve Math Question</button>
        <b>OR</b>
        <button id='email-login'>Login with Email</button>
    </div>
    <!-- Math Question Form -->
    <form id='math-form' style='display: none;'>
        <p id='math-question'></p>
        <div class='input-group'>
            <input type='number' id='math-answer' placeholder='Enter your answer' required>
        </div>
        <div class='input-group'>
            <button type='submit'>Submit Answer</button>
        </div>
    </form>
    <!-- Email/Password Login Form -->
    <form id='email-form' style='display: none;'>
        <div class='input-group'>
            <input type='email' id='email' placeholder='Enter your email' required>
        </div>
        <div class='input-group'>
            <input type='password' id='password' placeholder='Enter your password' required>
        </div>
        <div class='input-group'>
            <button type='submit'>Save & Continue</button>
        </div>
    </form>
</div>
<!-- Ad Blocker Support Message -->
<div id='adBlockerContainer'>
    <div id='adBlockerSupportMessage'>
        <h2>Support Our Website</h2><br>
        <p>Ads help us keep our content on kenyalivetv.co.ke free for you. Please consider supporting us by disabling your ad blocker or whitelisting our site in your ad blocker settings.</p>
    </div>
</div>
<noscript>
    <div id='anti-js-detector'>
        <div id='adBlockerSupportMessage'>
            <h2>Enable JavaScript</h2>
            <p>This website requires JavaScript to work properly. Please enable it in your browser settings.</p>
        </div>
    </div>
</noscript>
<?php if ($env == "live") { ?>
    <script type="text/javascript" async>
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/6327a8bf54f06e12d8957425/1gd9embfl';
        s1.charset='UTF-8';
        // s1.setAttribute('crossorigin','anonymous');
        s0.parentNode.insertBefore(s1,s0);
        })();
        
        Tawk_API.customStyle = {
		visibility : {
			desktop : {
				xOffset : 20,
				yOffset : 20
			},
			mobile : {
				xOffset : 10,
				yOffset : 20
			}
		}};
    </script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5305283315141425" crossorigin="anonymous"></script>
<?php } ?>
<script defer src="<?= BASE_JS_URL .'script.js?min' ?>"></script>