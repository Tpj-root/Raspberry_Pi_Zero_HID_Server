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
    // ADD THESE MISSING CHARACTERS:
    '|' => 0x31,    // Pipe (same as backslash with shift)
    '"' => 0x34,    // Double quote (same as single quote with shift)  
    '_' => 0x2d,    // Underscore (same as minus with shift)
    '!' => 0x1e,    // Exclamation (same as 1 with shift)
    '@' => 0x1f,    // At symbol (same as 2 with shift)
    '#' => 0x20,    // Hash (same as 3 with shift)
    '$' => 0x21,    // Dollar (same as 4 with shift)
    '%' => 0x22,    // Percent (same as 5 with shift)
    '^' => 0x23,    // Caret (same as 6 with shift)
    '&' => 0x24,    // Ampersand (same as 7 with shift)
    '*' => 0x25,    // Asterisk (same as 8 with shift)
    '(' => 0x26,    // Open paren (same as 9 with shift)
    ')' => 0x27,    // Close paren (same as 0 with shift)
    ':' => 0x33,    // Colon (same as semicolon with shift)
    '?' => 0x38,    // Question mark (same as slash with shift)
    '+' => 0x2e,    // Plus (same as equals with shift)
    '{' => 0x2f,    // Open curly (same as bracket with shift)
    '}' => 0x30,    // Close curly (same as bracket with shift)
    '~' => 0x35,    // Tilde (same as backtick with shift)
    '<' => 0x36,    // Less than (same as comma with shift)
    '>' => 0x37,    // Greater than (same as period with shift)
];

// Modifier keys
$modifiers = [
    'ctrl' => 0x01,
    'shift' => 0x02,
    'alt' => 0x04,
    'super' => 0x08, // Windows key
];

// Special keys - FIXED: Added proper HID codes
$special_keys = [
    'enter' => 0x28,
    'esc' => 0x29,
    'backspace' => 0x2a,
    'tab' => 0x2b,
    'space' => 0x2c,
    'capslock' => 0x39,
    'f1' => 0x3a, 'f2' => 0x3b, 'f3' => 0x3c, 'f4' => 0x3d,
    'f5' => 0x3e, 'f6' => 0x3f, 'f7' => 0x40, 'f8' => 0x41,
    'f9' => 0x42, 'f10' => 0x43, 'f11' => 0x44, 'f12' => 0x45,
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

// Basic special commands that should work as single keys
$basic_special_commands = [
    'enter', 'tab', 'space', 'backspace', 'esc'
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

function send_single_key($device, $key_code) {
    // Simple function to send a single key press
    // Key down: [modifier, reserved, key_code, 0, 0, 0, 0, 0]
    $report = pack('C8', 0x00, 0x00, $key_code, 0x00, 0x00, 0x00, 0x00, 0x00);
    file_put_contents($device, $report);
    usleep(100000); // 100ms delay
    
    // Key up: all zeros
    $report = pack('C8', 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00);
    file_put_contents($device, $report);
    usleep(50000); // 50ms delay
}

function send_text($device, $text) {
    global $key_codes, $modifiers;
    
    // Characters that require shift key
    $shift_chars = [
        '|', '"', '_', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', 
        ':', '?', '+', '{', '}', '~', '<', '>'
    ];
    
    $chars = str_split($text);
    foreach ($chars as $char) {
        if (isset($key_codes[$char])) {
            // Check if this character requires shift
            if (in_array($char, $shift_chars)) {
                // Send with shift modifier
                send_key_combination($device, ['shift'], [$char]);
            } else {
                // Send without shift
                send_single_key($device, $key_codes[$char]);
            }
        } else {
            // If character not found, try lowercase version
            $lower_char = strtolower($char);
            if (isset($key_codes[$lower_char])) {
                send_single_key($device, $key_codes[$lower_char]);
            }
        }
    }
}

try {
    // First, check for basic special commands
    if (in_array($command, ['enter', 'tab', 'space', 'backspace', 'esc'])) {
        global $special_keys;
        if (isset($special_keys[$command])) {
            send_single_key($device, $special_keys[$command]);
            echo "Special key sent: " . $command;
        } else {
            echo "Unknown special command: " . $command;
        }
    }
    // Check for Windows commands
    elseif (isset($windows_commands[$command])) {
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
    // Handle custom key combinations
    elseif (strpos($command, '+') !== false) {
        $parts = explode('+', $command);
        $modifiers_list = [];
        $keys_list = [];
        
        foreach ($parts as $part) {
            if (in_array($part, ['ctrl', 'shift', 'alt', 'super', 'win'])) {
                $modifiers_list[] = ($part === 'win') ? 'super' : $part;
            } else {
                $keys_list[] = $part;
            }
        }
        
        send_key_combination($device, $modifiers_list, $keys_list);
        echo "Custom combination executed: " . $command;
    }
    // Handle all text commands (including pipes and quotes) - FIXED LINE
    elseif (strlen($command) <= 100) {
        send_text($device, $command);
        echo "Text sent: " . $command;
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