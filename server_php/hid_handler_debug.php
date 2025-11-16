<?php
header('Content-Type: text/plain');

// Log everything for debugging
$debug_log = "/tmp/hid_debug.log";
file_put_contents($debug_log, "=== HID Debug Started ===\n", FILE_APPEND);
file_put_contents($debug_log, "Time: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    file_put_contents($debug_log, "ERROR: Method not allowed\n", FILE_APPEND);
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

$command = $_POST['command'] ?? '';
file_put_contents($debug_log, "Command received: " . $command . "\n", FILE_APPEND);

if (empty($command)) {
    file_put_contents($debug_log, "ERROR: No command provided\n", FILE_APPEND);
    http_response_code(400);
    echo "No command provided";
    exit;
}

$device = '/dev/hidg0';
file_put_contents($debug_log, "Checking device: " . $device . "\n", FILE_APPEND);

if (!file_exists($device)) {
    file_put_contents($debug_log, "ERROR: HID device not found\n", FILE_APPEND);
    http_response_code(500);
    echo "HID device not found";
    exit;
}

// Check if we can write to the device
if (!is_writable($device)) {
    file_put_contents($debug_log, "ERROR: HID device not writable\n", FILE_APPEND);
    http_response_code(500);
    echo "HID device not writable";
    exit;
}

file_put_contents($debug_log, "HID device is accessible and writable\n", FILE_APPEND);

// Test HID codes
$key_codes = [
    'a' => 0x04, 'b' => 0x05, 'c' => 0x06, 'd' => 0x07, 'e' => 0x08,
    'f' => 0x09, 'g' => 0x0a, 'h' => 0x0b, 'i' => 0x0c, 'j' => 0x0d,
    'k' => 0x0e, 'l' => 0x0f, 'm' => 0x10, 'n' => 0x11, 'o' => 0x12,
    'p' => 0x13, 'q' => 0x14, 'r' => 0x15, 's' => 0x16, 't' => 0x17,
    'u' => 0x18, 'v' => 0x19, 'w' => 0x1a, 'x' => 0x1b, 'y' => 0x1c,
    'z' => 0x1d,
];

function send_key_debug($device, $key_code, $debug_log) {
    file_put_contents($debug_log, "Sending key code: " . dechex($key_code) . "\n", FILE_APPEND);
    
    // Key down
    $report = pack('C*', 0x00, 0x00, $key_code, 0x00, 0x00, 0x00, 0x00, 0x00);
    $result = file_put_contents($device, $report);
    file_put_contents($debug_log, "Key down - Bytes written: " . $result . "\n", FILE_APPEND);
    usleep(50000);
    
    // Key up
    $report = pack('C*', 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00);
    $result = file_put_contents($device, $report);
    file_put_contents($debug_log, "Key up - Bytes written: " . $result . "\n", FILE_APPEND);
    usleep(50000);
    
    return $result;
}

try {
    file_put_contents($debug_log, "Processing command: " . $command . "\n", FILE_APPEND);
    
    if ($command === 'test') {
        file_put_contents($debug_log, "Sending test sequence\n", FILE_APPEND);
        // Send 'a'
        send_key_debug($device, $key_codes['a'], $debug_log);
        echo "Test command executed: sent 'a'";
    }
    elseif ($command === 'hello') {
        file_put_contents($debug_log, "Sending 'hello'\n", FILE_APPEND);
        $chars = str_split('hello');
        foreach ($chars as $char) {
            if (isset($key_codes[$char])) {
                send_key_debug($device, $key_codes[$char], $debug_log);
            }
        }
        echo "Text sent: hello";
    }
    else {
        // Send individual characters
        file_put_contents($debug_log, "Sending custom text: " . $command . "\n", FILE_APPEND);
        $chars = str_split(strtolower($command));
        foreach ($chars as $char) {
            if (isset($key_codes[$char])) {
                send_key_debug($device, $key_codes[$char], $debug_log);
            }
        }
        echo "Text sent: " . $command;
    }
    
    file_put_contents($debug_log, "Command completed successfully\n", FILE_APPEND);
    
} catch (Exception $e) {
    file_put_contents($debug_log, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}

file_put_contents($debug_log, "=== HID Debug Ended ===\n\n", FILE_APPEND);
?>
