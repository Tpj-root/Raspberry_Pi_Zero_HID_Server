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

// HID key codes (USB HID Usage IDs for keyboard)
$key_codes = [
    'a' => "\x04", 'b' => "\x05", 'c' => "\x06", 'd' => "\x07", 'e' => "\x08",
    'f' => "\x09", 'g' => "\x0a", 'h' => "\x0b", 'i' => "\x0c", 'j' => "\x0d",
    'k' => "\x0e", 'l' => "\x0f", 'm' => "\x10", 'n' => "\x11", 'o' => "\x12",
    'p' => "\x13", 'q' => "\x14", 'r' => "\x15", 's' => "\x16", 't' => "\x17",
    'u' => "\x18", 'v' => "\x19", 'w' => "\x1a", 'x' => "\x1b", 'y' => "\x1c",
    'z' => "\x1d",
    '1' => "\x1e", '2' => "\x1f", '3' => "\x20", '4' => "\x21", '5' => "\x22",
    '6' => "\x23", '7' => "\x24", '8' => "\x25", '9' => "\x26", '0' => "\x27",
    ' ' => "\x2c", // Space
];

// Special commands
$special_commands = [
    'enter' => "\x28",
    'tab' => "\x2b",
    'backspace' => "\x2a",
    'space' => "\x2c",
];

$device = '/dev/hidg0';

if (!file_exists($device)) {
    http_response_code(500);
    echo "HID device not found";
    exit;
}

function send_key($device, $key_code) {
    // Key down: send the key code
    file_put_contents($device, "\x00\x00" . $key_code . "\x00\x00\x00\x00\x00");
    usleep(50000); // 50ms delay
    
    // Key up: send all zeros
    file_put_contents($device, "\x00\x00\x00\x00\x00\x00\x00\x00");
    usleep(50000); // 50ms delay
}

try {
    if (array_key_exists($command, $special_commands)) {
        // Handle special commands
        send_key($device, $special_commands[$command]);
        echo "Special command executed: " . $command;
    } else {
        // Handle text input
        $chars = str_split(strtolower($command));
        foreach ($chars as $char) {
            if (isset($key_codes[$char])) {
                send_key($device, $key_codes[$char]);
            }
        }
        echo "Text sent: " . $command;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>
