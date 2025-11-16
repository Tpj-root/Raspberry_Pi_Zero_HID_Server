<?php
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

$command = $_POST['command'] ?? '';

if (empty($command)) {
    http_response_code(400);
    echo "No command provided";
    exit;
}

$device = '/dev/hidg0';

if (!file_exists($device)) {
    http_response_code(500);
    echo "HID device not found";
    exit;
}

// HID key codes
$key_codes = [
    'a' => 0x04, 'b' => 0x05, 'c' => 0x06, 'd' => 0x07, 'e' => 0x08,
    'f' => 0x09, 'g' => 0x0a, 'h' => 0x0b, 'i' => 0x0c, 'j' => 0x0d,
    'k' => 0x0e, 'l' => 0x0f, 'm' => 0x10, 'n' => 0x11, 'o' => 0x12,
    'p' => 0x13, 'q' => 0x14, 'r' => 0x15, 's' => 0x16, 't' => 0x17,
    'u' => 0x18, 'v' => 0x19, 'w' => 0x1a, 'x' => 0x1b, 'y' => 0x1c,
    'z' => 0x1d,
    '1' => 0x1e, '2' => 0x1f, '3' => 0x20, '4' => 0x21, '5' => 0x22,
    '6' => 0x23, '7' => 0x24, '8' => 0x25, '9' => 0x26, '0' => 0x27,
    ' ' => 0x2c, // Space
    '-' => 0x2d, '=' => 0x2e, '[' => 0x2f, ']' => 0x30, '\\' => 0x31,
    ';' => 0x33, "'" => 0x34, '`' => 0x35, ',' => 0x36, '.' => 0x37,
    '/' => 0x38,
];

// Special keys
$special_keys = [
    'enter' => 0x28,
    'esc' => 0x29,
    'backspace' => 0x2a,
    'tab' => 0x2b,
    'space' => 0x2c,
];

function send_key_combination($device, $key_code) {
    // Create HID report: [modifier, reserved, key1, key2, key3, key4, key5, key6]
    $report = pack('C8', 0x00, 0x00, $key_code, 0x00, 0x00, 0x00, 0x00, 0x00);
    
    // Key down
    file_put_contents($device, $report);
    usleep(100000); // 100ms
    
    // Key up - all zeros
    $report = pack('C8', 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00);
    file_put_contents($device, $report);
    usleep(50000); // 50ms
}

try {
    // Handle special commands
    if (isset($special_keys[$command])) {
        send_key_combination($device, $special_keys[$command]);
        echo "Special key sent: " . $command;
    }
    // Handle text input
    else {
        $chars = str_split(strtolower($command));
        foreach ($chars as $char) {
            if (isset($key_codes[$char])) {
                send_key_combination($device, $key_codes[$char]);
            }
        }
        echo "Text sent: " . $command;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>
