<?php
header('Content-Type: text/plain');

// =============================================================================
// SECURITY & VALIDATION CHECKS
// =============================================================================

// Only allow POST requests to prevent unauthorized access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo "Method not allowed";
    exit;
}

// Get the command from POST data, default to empty string if not provided
$command = $_POST['command'] ?? '';

// Validate that a command was actually provided
if (empty($command)) {
    http_response_code(400); // Bad Request
    echo "No command provided";
    exit;
}

// Define the HID device path
$device = '/dev/hidg0';

// Check if the HID device exists and is accessible
if (!file_exists($device)) {
    http_response_code(500); // Internal Server Error
    echo "HID device not found";
    exit;
}

// =============================================================================
// HID KEY MAPPING CONFIGURATION
// =============================================================================

/**
 * USB HID Usage IDs for keyboard keys
 */
$key_codes = [
    // Alphabet keys (a-z)
    'a' => 0x04, 'b' => 0x05, 'c' => 0x06, 'd' => 0x07, 'e' => 0x08,
    'f' => 0x09, 'g' => 0x0a, 'h' => 0x0b, 'i' => 0x0c, 'j' => 0x0d,
    'k' => 0x0e, 'l' => 0x0f, 'm' => 0x10, 'n' => 0x11, 'o' => 0x12,
    'p' => 0x13, 'q' => 0x14, 'r' => 0x15, 's' => 0x16, 't' => 0x17,
    'u' => 0x18, 'v' => 0x19, 'w' => 0x1a, 'x' => 0x1b, 'y' => 0x1c,
    'z' => 0x1d,
    
    // Number keys (0-9)
    '1' => 0x1e, '2' => 0x1f, '3' => 0x20, '4' => 0x21, '5' => 0x22,
    '6' => 0x23, '7' => 0x24, '8' => 0x25, '9' => 0x26, '0' => 0x27,
    
    // Special characters and symbols
    ' ' => 0x2c, // Space bar
    '-' => 0x2d, '=' => 0x2e, '[' => 0x2f, ']' => 0x30, '\\' => 0x31,
    ';' => 0x33, "'" => 0x34, '`' => 0x35, ',' => 0x36, '.' => 0x37,
    '/' => 0x38,
    
    // SHIFT-REQUIRED CHARACTERS
    '|' => 0x31, '"' => 0x34, '_' => 0x2d, '!' => 0x1e, '@' => 0x1f,
    '#' => 0x20, '$' => 0x21, '%' => 0x22, '^' => 0x23, '&' => 0x24,
    '*' => 0x25, '(' => 0x26, ')' => 0x27, ':' => 0x33, '?' => 0x38,
    '+' => 0x2e, '{' => 0x2f, '}' => 0x30, '~' => 0x35, '<' => 0x36,
    '>' => 0x37,
];

/**
 * Keyboard modifier keys
 */
$modifiers = [
    'ctrl'  => 0x01,
    'shift' => 0x02,  
    'alt'   => 0x04,
    'super' => 0x08,
];

/**
 * Special function keys and navigation keys
 */
$special_keys = [
    'enter'     => 0x28,
    'esc'       => 0x29,
    'backspace' => 0x2a,
    'tab'       => 0x2b,
    'space'     => 0x2c,
    
    'f1' => 0x3a, 'f2' => 0x3b, 'f3' => 0x3c, 'f4' => 0x3d,
    'f5' => 0x3e, 'f6' => 0x3f, 'f7' => 0x40, 'f8' => 0x41,
    'f9' => 0x42, 'f10' => 0x43, 'f11' => 0x44, 'f12' => 0x45,
    
    'print'      => 0x46,
    'scrolllock' => 0x47,
    'pause'      => 0x48,
    
    'insert'  => 0x49,
    'home'    => 0x4a,
    'pageup'  => 0x4b,
    'delete'  => 0x4c,
    'end'     => 0x4d,
    'pagedown'=> 0x4e,
    'right'   => 0x4f,
    'left'    => 0x50,
    'down'    => 0x51,
    'up'      => 0x52,
];

// =============================================================================
// PRE-DEFINED KEYBOARD SHORTCUTS
// =============================================================================

$windows_commands = [
    'win+r'        => ['modifiers' => ['super'], 'keys' => ['r']],
    'win+e'        => ['modifiers' => ['super'], 'keys' => ['e']],
    'win+d'        => ['modifiers' => ['super'], 'keys' => ['d']],
    'win+l'        => ['modifiers' => ['super'], 'keys' => ['l']],
    'ctrl+alt+del' => ['modifiers' => ['ctrl', 'alt'], 'keys' => ['delete']],
    'ctrl+shift+esc'=> ['modifiers' => ['ctrl', 'shift'], 'keys' => ['esc']],
];

$linux_commands = [
    'ctrl+alt+t'   => ['modifiers' => ['ctrl', 'alt'], 'keys' => ['t']],
    'ctrl+alt+del' => ['modifiers' => ['ctrl', 'alt'], 'keys' => ['delete']],
];

$basic_special_commands = ['enter', 'tab', 'space', 'backspace', 'esc'];

// =============================================================================
// HID COMMUNICATION FUNCTIONS - FIXED VERSION
// =============================================================================

/**
 * Send a key combination with modifiers
 */
function send_key_combination($device, $modifier_list, $key_list = []) {
    // Build modifier byte
    global $modifiers, $key_codes, $special_keys;
    
    $modifier_byte = 0x00;
    foreach ($modifier_list as $mod) {
        if (isset($modifiers[$mod])) {
            $modifier_byte |= $modifiers[$mod];
        }
    }
    
    // Prepare key codes array
    $key_bytes = array_fill(0, 6, 0x00);
    $i = 0;
    foreach ($key_list as $key) {
        if (isset($key_codes[$key])) {
            $key_bytes[$i] = $key_codes[$key];
        } elseif (isset($special_keys[$key])) {
            $key_bytes[$i] = $special_keys[$key];
        }
        $i++;
        if ($i >= 6) break;
    }
    
    // Send key down event
    $report = chr($modifier_byte) . chr(0x00) . implode('', array_map('chr', $key_bytes));
    if (file_put_contents($device, $report) === false) {
        throw new Exception("Failed to write to HID device");
    }
    
    // MINIMAL DELAY - changed from usleep(50000) to:
    usleep(10000); // 10ms instead of 50ms
    
    // Send key up event
    if (file_put_contents($device, "\x00\x00\x00\x00\x00\x00\x00\x00") === false) {
        throw new Exception("Failed to write to HID device");
    }
    
    // MINIMAL DELAY - changed from usleep(50000) to:
    usleep(10000); // 10ms instead of 50ms
}

/**
 * Send a single key press without modifiers
 */
function send_single_key($device, $key_code) {
    // Key down
    $report = pack('C8', 0x00, 0x00, $key_code, 0x00, 0x00, 0x00, 0x00, 0x00);
    if (file_put_contents($device, $report) === false) {
        throw new Exception("Failed to write to HID device");
    }
    
    // MINIMAL DELAY - changed from usleep(100000) to:
    usleep(10000); // 10ms instead of 100ms
    
    // Key up
    $report = pack('C8', 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00);
    if (file_put_contents($device, $report) === false) {
        throw new Exception("Failed to write to HID device");
    }
    
    // MINIMAL DELAY - changed from usleep(50000) to:
    usleep(10000); // 10ms instead of 50ms
}

/**
 * Send text by typing each character individually - NO HUMAN-LIKE DELAYS
 */
function send_text($device, $text) {
    global $key_codes, $modifiers;
    
    $shift_chars = [
        '|', '"', '_', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', 
        ':', '?', '+', '{', '}', '~', '<', '>'
    ];
    
    $uppercase_letters = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    ];
    
    $chars = str_split($text);
    foreach ($chars as $char) {
        // Check if character is uppercase letter
        if (in_array($char, $uppercase_letters)) {
            $lower_char = strtolower($char);
            if (isset($key_codes[$lower_char])) {
                send_key_combination($device, ['shift'], [$lower_char]);
            }
        }
        // Check if character requires Shift key
        elseif (in_array($char, $shift_chars)) {
            send_key_combination($device, ['shift'], [$char]);
        }
        // Handle regular characters
        elseif (isset($key_codes[$char])) {
            send_single_key($device, $key_codes[$char]);
        }
        else {
            $lower_char = strtolower($char);
            if (isset($key_codes[$lower_char])) {
                send_single_key($device, $key_codes[$lower_char]);
            }
        }
        
        // REMOVED HUMAN-LIKE RANDOM DELAY:
        // OLD: usleep(rand(80000, 150000)); // 80-150ms random delay
        // NEW: Minimal consistent delay
        usleep(5000); // 5ms fixed delay instead of random human-like delay
    }
}

/**
 * NEW: Main function to send commands - replaces missing sendCommand
 * FIXED VERSION: Handles uppercase, special characters, and is fast
 */
function send_command($device, $command) {
    global $key_codes, $modifiers, $special_keys;
    
    $shift_chars = [
        '|', '"', '_', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', 
        ':', '?', '+', '{', '}', '~', '<', '>'
    ];
    
    $chars = str_split($command);
    foreach ($chars as $char) {
        // Check if character requires Shift key (special characters)
        if (in_array($char, $shift_chars)) {
            send_key_combination($device, ['shift'], [$char]);
        }
        // Check if character is uppercase letter (A-Z) - FIXED: using ctype_upper
        elseif (ctype_upper($char)) {
            $lower_char = strtolower($char);
            if (isset($key_codes[$lower_char])) {
                send_key_combination($device, ['shift'], [$lower_char]);
            }
        }
        // Handle regular lowercase characters and other supported keys
        elseif (isset($key_codes[$char])) {
            send_single_key($device, $key_codes[$char]);
        }
        else {
            // If character not found in key codes, try lowercase version as fallback
            $lower_char = strtolower($char);
            if (isset($key_codes[$lower_char])) {
                send_single_key($device, $key_codes[$lower_char]);
            }
        }
        
        // MINIMAL DELAY - no human-like randomness
        usleep(2000); // 2ms fixed delay (even faster)
    }
    
    return "Command sent: " . $command;
}

/**
 * Send command followed by Enter key
 */
function sendCommandWithEnter($device, $command, $enter_delay = 50) {
    global $special_keys;
    
    // Send the command text
    send_command($device, $command);
    
    // Short delay before Enter
    usleep($enter_delay * 1000); // Convert to microseconds
    
    // Send Enter key
    send_single_key($device, $special_keys['enter']);
    
    return "Command typed and executed: " . $command;
}

// =============================================================================
// MAIN COMMAND PROCESSING LOGIC - FIXED
// =============================================================================

try {
    // 1. Check for basic special commands
    if (in_array($command, $basic_special_commands)) {
        if (isset($special_keys[$command])) {
            send_single_key($device, $special_keys[$command]);
            echo "Special key sent: " . $command;
        } else {
            echo "Unknown special command: " . $command;
        }
    }
    // 2. Check for Windows keyboard shortcuts
    elseif (isset($windows_commands[$command])) {
        $cmd = $windows_commands[$command];
        send_key_combination($device, $cmd['modifiers'], $cmd['keys'] ?? []);
        echo "Windows command executed: " . $command;
    }
    // 3. Check for Linux keyboard shortcuts  
    elseif (isset($linux_commands[$command])) {
        $cmd = $linux_commands[$command];
        send_key_combination($device, $cmd['modifiers'], $cmd['keys'] ?? []);
        echo "Linux command executed: " . $command;
    }
    // 4. Handle custom key combinations
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
    // 5. Handle commands with auto-Enter
    elseif (substr($command, -6) === '_enter') {
        $actual_command = substr($command, 0, -6);
        if (!empty($actual_command) && strlen($actual_command) <= 100) {
            $result = sendCommandWithEnter($device, $actual_command);
            echo $result;
        } else {
            echo "Invalid command: " . $actual_command;
        }
    }
    // 6. Handle regular text commands
    elseif (strlen($command) <= 100) {
        send_text($device, $command);
        echo "Text sent: " . $command;
    }
    // 7. Reject invalid commands
    else {
        http_response_code(400);
        echo "Unknown command or command too long: " . $command;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>