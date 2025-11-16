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
 * These are the standard scan codes that USB keyboards use
 * Reference: USB HID Usage Tables document
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
    
    // SHIFT-REQUIRED CHARACTERS (these need the shift modifier)
    '|' => 0x31,    // Pipe - same physical key as backslash but with SHIFT
    '"' => 0x34,    // Double quote - same physical key as single quote with SHIFT  
    '_' => 0x2d,    // Underscore - same physical key as minus with SHIFT
    '!' => 0x1e,    // Exclamation - same physical key as 1 with SHIFT
    '@' => 0x1f,    // At symbol - same physical key as 2 with SHIFT
    '#' => 0x20,    // Hash/Pound - same physical key as 3 with SHIFT
    '$' => 0x21,    // Dollar - same physical key as 4 with SHIFT
    '%' => 0x22,    // Percent - same physical key as 5 with SHIFT
    '^' => 0x23,    // Caret - same physical key as 6 with SHIFT
    '&' => 0x24,    // Ampersand - same physical key as 7 with SHIFT
    '*' => 0x25,    // Asterisk - same physical key as 8 with SHIFT
    '(' => 0x26,    // Open parenthesis - same physical key as 9 with SHIFT
    ')' => 0x27,    // Close parenthesis - same physical key as 0 with SHIFT
    ':' => 0x33,    // Colon - same physical key as semicolon with SHIFT
    '?' => 0x38,    // Question mark - same physical key as slash with SHIFT
    '+' => 0x2e,    // Plus - same physical key as equals with SHIFT
    '{' => 0x2f,    // Open curly brace - same physical key as bracket with SHIFT
    '}' => 0x30,    // Close curly brace - same physical key as bracket with SHIFT
    '~' => 0x35,    // Tilde - same physical key as backtick with SHIFT
    '<' => 0x36,    // Less than - same physical key as comma with SHIFT
    '>' => 0x37,    // Greater than - same physical key as period with SHIFT
];

/**
 * Keyboard modifier keys (used for Ctrl, Shift, Alt, etc.)
 * These are bitmask values that can be combined
 */
$modifiers = [
    'ctrl'  => 0x01,  // Left Control key
    'shift' => 0x02,  // Left Shift key  
    'alt'   => 0x04,  // Left Alt key
    'super' => 0x08,  // Windows/Command key
];

/**
 * Special function keys and navigation keys
 */
$special_keys = [
    // Basic editing keys
    'enter'     => 0x28,  // Enter/Return key
    'esc'       => 0x29,  // Escape key
    'backspace' => 0x2a,  // Backspace key
    'tab'       => 0x2b,  // Tab key
    'space'     => 0x2c,  // Space bar (same as regular space)
    
    // Function keys (F1-F12)
    'f1' => 0x3a, 'f2' => 0x3b, 'f3' => 0x3c, 'f4' => 0x3d,
    'f5' => 0x3e, 'f6' => 0x3f, 'f7' => 0x40, 'f8' => 0x41,
    'f9' => 0x42, 'f10' => 0x43, 'f11' => 0x44, 'f12' => 0x45,
    
    // System keys
    'print'      => 0x46,  // Print Screen
    'scrolllock' => 0x47,  // Scroll Lock
    'pause'      => 0x48,  // Pause/Break
    
    // Navigation keys
    'insert'  => 0x49,  // Insert
    'home'    => 0x4a,  // Home
    'pageup'  => 0x4b,  // Page Up
    'delete'  => 0x4c,  // Delete
    'end'     => 0x4d,  // End
    'pagedown'=> 0x4e,  // Page Down
    'right'   => 0x4f,  // Right Arrow
    'left'    => 0x50,  // Left Arrow
    'down'    => 0x51,  // Down Arrow
    'up'      => 0x52,  // Up Arrow
];

// =============================================================================
// PRE-DEFINED KEYBOARD SHORTCUTS
// =============================================================================

/**
 * Windows-specific keyboard shortcuts
 * Format: 'command_name' => ['modifiers' => [list], 'keys' => [list]]
 */
$windows_commands = [
    // System shortcuts
    'win'          => ['modifiers' => ['super']],  // Windows key alone
    'win+r'        => ['modifiers' => ['super'], 'keys' => ['r']],  // Run dialog
    'win+e'        => ['modifiers' => ['super'], 'keys' => ['e']],  // File Explorer
    'win+d'        => ['modifiers' => ['super'], 'keys' => ['d']],  // Show desktop
    'win+l'        => ['modifiers' => ['super'], 'keys' => ['l']],  // Lock computer
    'win+i'        => ['modifiers' => ['super'], 'keys' => ['i']],  // Settings
    'win+x'        => ['modifiers' => ['super'], 'keys' => ['x']],  // Power user menu
    'win+a'        => ['modifiers' => ['super'], 'keys' => ['a']],  // Action center
    'win+tab'      => ['modifiers' => ['super'], 'keys' => ['tab']], // Task view
    'win+prtscn'   => ['modifiers' => ['super'], 'keys' => ['print']], // Screenshot
    
    // Virtual desktop management
    'win+ctrl+d'   => ['modifiers' => ['super', 'ctrl'], 'keys' => ['d']],      // New virtual desktop
    'win+ctrl+left' => ['modifiers' => ['super', 'ctrl'], 'keys' => ['left']],  // Previous virtual desktop
    'win+ctrl+right'=> ['modifiers' => ['super', 'ctrl'], 'keys' => ['right']], // Next virtual desktop
    'win+ctrl+f4'  => ['modifiers' => ['super', 'ctrl'], 'keys' => ['f4']],     // Close virtual desktop
    
    // System management
    'ctrl+alt+del' => ['modifiers' => ['ctrl', 'alt'], 'keys' => ['delete']],   // Security screen
    'ctrl+shift+esc'=> ['modifiers' => ['ctrl', 'shift'], 'keys' => ['esc']],   // Task Manager
];

/**
 * Linux-specific keyboard shortcuts
 */
$linux_commands = [
    // Terminal operations
    'ctrl+alt+t'   => ['modifiers' => ['ctrl', 'alt'], 'keys' => ['t']],    // Open terminal
    'ctrl+alt+l'   => ['modifiers' => ['ctrl', 'alt'], 'keys' => ['l']],    // Lock screen
    'ctrl+alt+del' => ['modifiers' => ['ctrl', 'alt'], 'keys' => ['delete']], // Logout dialog
    
    // System shortcuts
    'alt+f1'       => ['modifiers' => ['alt'], 'keys' => ['f1']],  // Application menu
    'alt+f2'       => ['modifiers' => ['alt'], 'keys' => ['f2']],  // Run command
    'alt+f4'       => ['modifiers' => ['alt'], 'keys' => ['f4']],  // Close window
    'alt+f7'       => ['modifiers' => ['alt'], 'keys' => ['f7']],  // Move window
    
    // GNOME desktop shortcuts
    'super'        => ['modifiers' => ['super']],                  // Activities overview
    'super+a'      => ['modifiers' => ['super'], 'keys' => ['a']], // Show applications
    
    // Screenshot shortcuts
    'alt+print'    => ['modifiers' => ['alt'], 'keys' => ['print']],    // Screenshot window
    'shift+print'  => ['modifiers' => ['shift'], 'keys' => ['print']],  // Screenshot area
];

// List of basic special commands that work as single key presses
$basic_special_commands = ['enter', 'tab', 'space', 'backspace', 'esc'];

// =============================================================================
// HID COMMUNICATION FUNCTIONS
// =============================================================================

/**
 * Send a key combination with modifiers (Ctrl, Shift, Alt, etc.)
 * 
 * @param string $device The HID device path (/dev/hidg0)
 * @param array $modifier_list List of modifiers ['ctrl', 'shift', etc.]
 * @param array $key_list List of keys to press simultaneously
 * 
 * HID Report Structure (8 bytes):
 * Byte 0: Modifier keys bitmask
 * Byte 1: Reserved
 * Bytes 2-7: Key codes (up to 6 keys can be pressed at once)
 */
function send_key_combination($device, $modifier_list, $key_list = []) {
    // Build modifier byte from modifier list
    $modifier_byte = 0x00;
    foreach ($modifier_list as $mod) {
        global $modifiers;
        if (isset($modifiers[$mod])) {
            $modifier_byte |= $modifiers[$mod]; // Bitwise OR to combine modifiers
        }
    }
    
    // Prepare key codes array (max 6 keys)
    $key_bytes = array_fill(0, 6, 0x00); // Initialize with zeros
    $i = 0;
    foreach ($key_list as $key) {
        global $key_codes, $special_keys;
        if (isset($key_codes[$key])) {
            $key_bytes[$i] = $key_codes[$key];
        } elseif (isset($special_keys[$key])) {
            $key_bytes[$i] = $special_keys[$key];
        }
        $i++;
        if ($i >= 6) break; // Maximum 6 keys in HID report
    }
    
    // Send key down event (press the keys)
    $report = chr($modifier_byte) . chr(0x00) . implode('', array_map('chr', $key_bytes));
    file_put_contents($device, $report);
    usleep(50000); // 50ms delay - hold keys for a moment
    
    // Send key up event (release all keys)
    file_put_contents($device, "\x00\x00\x00\x00\x00\x00\x00\x00");
    usleep(50000); // 50ms delay before next action
}

/**
 * Send a single key press without modifiers
 * 
 * @param string $device The HID device path
 * @param int $key_code The HID key code to send
 */
function send_single_key($device, $key_code) {
    // Key down: press the key
    // Format: [modifier=0, reserved=0, key_code, 0, 0, 0, 0, 0]
    $report = pack('C8', 0x00, 0x00, $key_code, 0x00, 0x00, 0x00, 0x00, 0x00);
    file_put_contents($device, $report);
    usleep(100000); // 100ms delay - hold key for realistic typing
    
    // Key up: release the key
    $report = pack('C8', 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00);
    file_put_contents($device, $report);
    usleep(50000); // 50ms delay before next key
}

/**
 * Send text by typing each character individually
 * Handles shift-required characters automatically
 * 
 * @param string $device The HID device path
 * @param string $text The text to type
 */
function send_text($device, $text) {
    global $key_codes, $modifiers;
    
    // Characters that require the Shift key to be pressed
    $shift_chars = [
        '|', '"', '_', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', 
        ':', '?', '+', '{', '}', '~', '<', '>'
    ];
    
    // Convert text to array of characters and process each one
    $chars = str_split($text);
    foreach ($chars as $char) {
        if (isset($key_codes[$char])) {
            // Check if this character requires the shift key
            if (in_array($char, $shift_chars)) {
                // Send character with Shift modifier
                send_key_combination($device, ['shift'], [$char]);
            } else {
                // Send character without modifiers
                send_single_key($device, $key_codes[$char]);
            }
        } else {
            // If character not found in key codes, try lowercase version
            // This handles case-insensitive fallback
            $lower_char = strtolower($char);
            if (isset($key_codes[$lower_char])) {
                send_single_key($device, $key_codes[$lower_char]);
            }
            // Note: If character still not found, it will be skipped
        }
    }
}

// =============================================================================
// NEW FUNCTION: sendCommandWithEnter - Types command then presses Enter
// =============================================================================

/**
 * Enhanced function to send a command followed by Enter key
 * This ensures the command is fully typed before executing with Enter
 * 
 * @param string $device The HID device path
 * @param string $command The command to type
 * @param int $enter_delay Additional delay before sending Enter (milliseconds)
 */
function sendCommandWithEnter($device, $command, $enter_delay = 1000) {
    global $key_codes, $modifiers, $special_keys;
    
    // Characters that require Shift key
    $shift_chars = [
        '|', '"', '_', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', 
        ':', '?', '+', '{', '}', '~', '<', '>'
    ];
    
    // Convert command to array of characters
    $chars = str_split($command);
    
    // Type each character individually with proper timing
    foreach ($chars as $char) {
        if (isset($key_codes[$char])) {
            if (in_array($char, $shift_chars)) {
                // Type character with Shift key
                send_key_combination($device, ['shift'], [$char]);
            } else {
                // Type character normally
                send_single_key($device, $key_codes[$char]);
            }
        } else {
            // Fallback to lowercase if character not found
            $lower_char = strtolower($char);
            if (isset($key_codes[$lower_char])) {
                send_single_key($device, $key_codes[$lower_char]);
            }
        }
        
        // Add random delay between characters for human-like typing
        // Range: 80-150ms between keystrokes
        usleep(rand(80000, 150000));
    }
    
    // Calculate dynamic delay based on command length
    // Longer commands need more time to ensure all characters are typed
    $calculated_delay = (strlen($command) * 100000) + ($enter_delay * 1000);
    
    // Wait for command typing to complete
    usleep($calculated_delay);
    
    // Send Enter key to execute the command
    send_single_key($device, $special_keys['enter']);
    
    return "Command typed and executed: " . $command;
}

// =============================================================================
// MAIN COMMAND PROCESSING LOGIC
// =============================================================================

try {
    // PROCESSING ORDER: Check command types from most specific to most general
    
    // 1. Check for basic special commands (single keys like Enter, Tab, etc.)
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
    
    // 4. Handle custom key combinations (like ctrl+alt+delete)
    elseif (strpos($command, '+') !== false) {
        $parts = explode('+', $command);
        $modifiers_list = [];
        $keys_list = [];
        
        // Separate modifiers from regular keys
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
    
    // 5. NEW: Handle commands that should be followed by Enter automatically
    // Commands ending with "_enter" will be typed then Enter is pressed
    elseif (substr($command, -6) === '_enter') {
        $actual_command = substr($command, 0, -6); // Remove "_enter" suffix
        if (strlen($actual_command) <= 100) {
            $result = sendCommandWithEnter($device, $actual_command);
            echo $result;
        } else {
            echo "Command too long: " . $actual_command;
        }
    }
    
    // 6. Handle all other text commands (regular typing)
    elseif (strlen($command) <= 100) {
        send_text($device, $command);
        echo "Text sent: " . $command;
    }
    
    // 7. Reject unknown or invalid commands
    else {
        http_response_code(400);
        echo "Unknown command or command too long: " . $command;
    }
    
} catch (Exception $e) {
    // Handle any unexpected errors
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}

// =============================================================================
// USAGE EXAMPLES FOR sendCommandWithEnter:
// =============================================================================
// 
// From JavaScript, you can now use:
// 
// 1. Regular command (just types the text):
//    sendCommand('hello world')
//
// 2. Command with auto-Enter (types then presses Enter):
//    sendCommand('whoami_enter')
//
// 3. Complex command with special characters:
//    sendCommand('date | tr " " "_"_enter')
//
// The '_enter' suffix tells the system to automatically press Enter
// after the command is fully typed, making it execute in terminals.
//
// =============================================================================
?>