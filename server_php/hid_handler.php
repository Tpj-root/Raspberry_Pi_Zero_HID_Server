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

// HID key codes (USB HID Usage IDs for keyboard)
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

// Modifier keys
$modifiers = [
    'ctrl' => 0x01,
    'shift' => 0x02,
    'alt' => 0x04,
    'super' => 0x08, // Windows key
];

// Special keys
$special_keys = [
    'enter' => 0x28,
    'esc' => 0x29,
    'backspace' => 0x2a,
    'tab' => 0x2b,
    'capslock' => 0x39,
    'print' => 0x46,
    'scrolllock' => 0x47,
    'pause' => 0x48,
    'insert' => 0x49,
    'home' => 0x4a,
    'pageup' => 0x4b,
    'delete' => 0x4c,
    'end' => 0x4d,
    'pagedown' => 0x4e,
    'right' => 0x4f,
    'left' => 0x50,
    'down' => 0x51,
    'up' => 0x52,
];

// Windows-specific commands
$windows_commands = [
    'win' => ['modifiers' => ['super']],
    'win+r' => ['modifiers' => ['super'], 'keys' => ['r']],
    'win+e' => ['modifiers' => ['super'], 'keys' => ['e']],
    'win+d' => ['modifiers' => ['super'], 'keys' => ['d']],
    'win+l' => ['modifiers' => ['super'], 'keys' => ['l']],
    'win+i' => ['modifiers' => ['super'], 'keys' => ['i']],
    'win+x' => ['modifiers' => ['super'], 'keys' => ['x']],
    'win+a' => ['modifiers' => ['super'], 'keys' => ['a']],
    'win+tab' => ['modifiers' => ['super'], 'keys' => ['tab']],
    'win+prtscn' => ['modifiers' => ['super'], 'keys' => ['print']],
    'win+ctrl+d' => ['modifiers' => ['super', 'ctrl'], 'keys' => ['d']],
    'win+ctrl+left' => ['modifiers' => ['super', 'ctrl'], 'keys' => ['left']],
    'win+ctrl+right' => ['modifiers' => ['super', 'ctrl'], 'keys' => ['right']],
    'win+ctrl+f4' => ['modifiers' => ['super', 'ctrl'], 'keys' => ['f4']],
    'ctrl+alt+del' => ['modifiers' => ['ctrl', 'alt'], 'keys' => ['delete']],
    'ctrl+shift+esc' => ['modifiers' => ['ctrl', 'shift'], 'keys' => ['esc']],
];

// Linux-specific commands
$linux_commands = [
    'ctrl+alt+t' => ['modifiers' => ['ctrl', 'alt'], 'keys' => ['t']],
    'ctrl+alt+l' => ['modifiers' => ['ctrl', 'alt'], 'keys' => ['l']],
    'ctrl+alt+del' => ['modifiers' => ['ctrl', 'alt'], 'keys' => ['delete']],
    'alt+f1' => ['modifiers' => ['alt'], 'keys' => ['f1']],
    'alt+f2' => ['modifiers' => ['alt'], 'keys' => ['f2']],
    'alt+f4' => ['modifiers' => ['alt'], 'keys' => ['f4']],
    'alt+f7' => ['modifiers' => ['alt'], 'keys' => ['f7']],
    'super' => ['modifiers' => ['super']],
    'super+a' => ['modifiers' => ['super'], 'keys' => ['a']],
    'alt+print' => ['modifiers' => ['alt'], 'keys' => ['print']],
    'shift+print' => ['modifiers' => ['shift'], 'keys' => ['print']],
];

function send_key_combination($device, $modifier_list, $key_list = []) {
    $modifier_byte = 0x00;
    foreach ($modifier_list as $mod) {
        global $modifiers;
        if (isset($modifiers[$mod])) {
            $modifier_byte |= $modifiers[$mod];
        }
    }
    
    $key_bytes = array_fill(0, 6, 0x00);
    $i = 0;
    foreach ($key_list as $key) {
        global $key_codes, $special_keys;
        if (isset($key_codes[$key])) {
            $key_bytes[$i] = $key_codes[$key];
        } elseif (isset($special_keys[$key])) {
            $key_bytes[$i] = $special_keys[$key];
        }
        $i++;
        if ($i >= 6) break;
    }
    
    // Key down
    $report = chr($modifier_byte) . chr(0x00) . implode('', array_map('chr', $key_bytes));
    file_put_contents($device, $report);
    usleep(50000); // 50ms
    
    // Key up
    file_put_contents($device, "\x00\x00\x00\x00\x00\x00\x00\x00");
    usleep(50000); // 50ms
}

function send_text($device, $text) {
    global $key_codes;
    $chars = str_split(strtolower($text));
    foreach ($chars as $char) {
        if (isset($key_codes[$char])) {
            send_key_combination($device, [], [$char]);
        }
    }
}

try {
    // Check for Windows commands
    if (isset($windows_commands[$command])) {
        $cmd = $windows_commands[$command];
        send_key_combination($device, $cmd['modifiers'], $cmd['keys'] ?? []);
        echo "Windows command executed: " . $command;
    }
    // Check for Linux commands
    elseif (isset($linux_commands[$command])) {
        $cmd = $linux_commands[$command];
        send_key_combination($device, $cmd['modifiers'], $cmd['keys'] ?? []);
        echo "Linux command executed: " . $command;
    }
    // Handle simple text
    elseif (strlen($command) <= 50 && strpos($command, '+') === false) {
        send_text($device, $command);
        echo "Text sent: " . $command;
    }
    // Handle custom key combinations - FIXED LINE (using strpos instead of str_contains)
    elseif (strpos($command, '+') !== false) {
        $parts = explode('+', $command);
        $modifiers = [];
        $keys = [];
        
        foreach ($parts as $part) {
            if (in_array($part, ['ctrl', 'shift', 'alt', 'super', 'win'])) {
                $modifiers[] = ($part === 'win') ? 'super' : $part;
            } else {
                $keys[] = $part;
            }
        }
        
        send_key_combination($device, $modifiers, $keys);
        echo "Custom combination executed: " . $command;
    }
    else {
        http_response_code(400);
        echo "Unknown command: " . $command;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>