<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linux HID Commands - Raspberry Pi Zero HID</title>
    <style>
        body {
            font-family: 'Ubuntu', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            min-height: 100vh;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-top: 20px;
        }
        .header {
            background: linear-gradient(135deg, #e44d26, #f16529);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1em;
        }
        .notes {
            background: #fff3e0;
            border-left: 5px solid #ff9800;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .notes h3 {
            color: #e65100;
            margin-top: 0;
        }
        .command-section {
            background: #fafafa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid #e0e0e0;
        }
        .command-section h3 {
            color: #37474f;
            border-bottom: 2px solid #b0bec5;
            padding-bottom: 10px;
            margin-top: 0;
        }
		.button-group {
		    display: grid;
		    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		    gap: 20px;
		    margin: 20px 0;
		    align-items: start;
		}
		
		.command-button {
		    background: linear-gradient(135deg, #4caf50, #45a049);
		    margin: 0;
		    color: white;
		    border: none;
		
		    padding: 8px 12px; /* smaller height & width */
		    border-radius: 6px; /* smaller corners */
		
		    cursor: pointer;
		    font-size: 13px;  /* smaller text */
		    font-weight: 500;
		    text-align: left;
		
		    transition: all 0.3s ease;
		    display: flex;
		    justify-content: space-between;
		    align-items: center;
		}

        .command-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .command-button:active {
            transform: translateY(0);
        }
        .command-button.terminal {
            background: linear-gradient(135deg, #37474f, #263238);
        }
        .command-button.danger {
            background: linear-gradient(135deg, #f44336, #d32f2f);
        }
        .command-button.warning {
            background: linear-gradient(135deg, #ff9800, #f57c00);
        }
        .command-button.gnome {
            background: linear-gradient(135deg, #4a6572, #344955);
        }
        .command-description {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #4caf50;
            font-size: 0.95em;
            color: #455a64;
        }
        .command-description_t {
            background: white;
            padding: 10px;
            border-radius: 10px;
            margin: 0 0 0 0;
            border-left: 2px solid #263238;
            border-right: 2px solid #263238;
            border-top: 2px solid #263238;
            border-bottom: 2px solid #263238;
            font-size: 0.55em;
            color: #455a64;
        }
        .message {
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            display: none;
            font-weight: 500;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .nav-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .nav-button {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .nav-button.primary {
            background: #e44d26;
        }
        .key-combination {
            font-family: 'Ubuntu Mono', monospace;
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .desktop-environment {
            font-size: 0.8em;
            opacity: 0.8;
            margin-left: 10px;
        }
        .cmd-group {
        display: flex;
        flex-direction: column;
        gap: 0;   /* no space between button & description */
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">

            <h1>🐧 Linux HID One-liners</h1>
            <p>A collection of handy Bash One-Liners and terminal tricks </p>
            <p>Send keyboard shortcuts and commands remotely via your mobile phone</p>


        <div class="command-section">
            <h3>💻 Custom Commands</h3>
        
            <div class="button-group">
        
                <!-- Sudo last command -->
				<div class="cmd-group">
					<div class="command-description_t">
				        <strong>Run the last command as root</strong>
				        <p>This repeats your previous command with sudo.</p>
				    </div>

				    <button class="command-button terminal" onclick="sendCommand('sudo !!_enter')">
				        Run "sudo !!"
				    </button>
				</div>

                <!-- Sudo last command -->
<!--                 <div class="cmd-group">
                	<div class="command-description_t">
                        <strong>Run the last command as root</strong>
                        <p>This repeats your previous command with sudo.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('sudo !!_enter')">
                        Run "sudo !!"
                    </button>
                </div> -->
                
                <!-- Simple HTTP Server -->
                <div class="cmd-group">
                	<div class="command-description_t">
                        <strong> Serve current directory tree</strong>
                        <p>Tested Python 3.13.5.</p>
                        <p>Starts a simple web server on port 8000.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('python3 -m http.server 8000_enter')">
                        Run "python3 -m http.server 8000"
                    </button>
                </div>
                
                <!-- Replace part of previous command -->
<!--                 <div class="cmd-group">
                	<div class="command-description_t">
                        <strong>Replace text in last command</strong>
                        <p>Runs previous command but replaces a word.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('^foo^bar_enter')">
                        Run "^foo^bar"
                    </button>
                </div> -->
                
                <!-- Command editor -->
                <div class="cmd-group">
                	<div class="command-description_t">
                        <strong>❌ Edit a complex command</strong>
                        <p>Opens an editor to type long or tricky commands.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ctrl-x e_enter')">
                        Run "ctrl-x e"
                    </button>
                </div>
                
                <!-- Insert last arg -->
                <div class="cmd-group">
                	<div class="command-description_t">
                        <strong>Insert last command argument</strong>
                        <p>Puts the final argument of the previous command into the shell.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ALT+._enter')">
                        Run "ALT+."
                    </button>
                </div>
                
                <!-- Mount column -->
                <div class="cmd-group">
                	<div class="command-description_t">
                        <strong>Show mounted filesystems cleanly</strong>
                        <p>Displays mounted filesystems in a nicely aligned layout.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('mount | column -t_enter')">
                        Run "mount | column -t"
                    </button>
                </div>
                
                <!-- Reset terminal -->
                <div class="cmd-group">
                	<div class="command-description_t">
                        <strong>Reset broken terminal</strong>
                        <p>Restores terminal if output is garbled.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('reset_enter')">
                        Run "reset"
                    </button>
                </div>
                
                <!-- External IP -->
                <div class="cmd-group">
                	<div class="command-description_t">
                        <strong>Get external IP address</strong>
                        <p>Fetches your public IP from ifconfig.me.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('curl ifconfig.me_enter')">
                        Run "curl ifconfig.me"
                    </button>
                </div>
                
                <!-- Run at midnight -->
                <div class="cmd-group">
                	<div class="command-description_t">
                        <strong>Schedule a command</strong>
                        <p>Executes the given command at midnight.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('echo \"ls -l\" | at midnight_enter')">
                        Run "echo 'ls -l' | at midnight"
                    </button>
                </div>
                
                <!-- ASCII table -->
                <div class="cmd-group">
                	<div class="command-description_t">
                        <strong>Show ASCII table</strong>
                        <p>Displays a reference ASCII table.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('man ascii_enter')">
                        Run "man ascii"
                    </button>
                </div>
       
                <!-- Output mic to remote speaker -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Output microphone to remote speaker</strong>
                        <p>Sends your mic audio to a remote machine through SSH.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('dd if=/dev/dsp | ssh -c arcfour -C username@host dd of=/dev/dsp_enter')">
                        Run "dd if=/dev/dsp | ssh …"
                    </button>
                </div>
                
                <!-- Kill command, yank back -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Edit and resume a killed command</strong>
                        <p>Cut a partially typed command and paste it back.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ctrl-u ctrl-y_enter')">
                        Use "ctrl+u … ctrl+y"
                    </button>
                </div>
                
                <!-- Wikipedia DNS query -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Query Wikipedia via DNS</strong>
                        <p>Gets Wikipedia content using DNS TXT records.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('dig +short txt keyword.wp.dg.cx_enter')">
                        Run "dig +short txt keyword.wp.dg.cx"
                    </button>
                </div>
                
                <!-- SSHFS mount -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Mount remote folder over SSH</strong>
                        <p>Mounts a server directory locally using sshfs.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('sshfs name@server:/path/to/folder /path/to/mount/point_enter')">
                        Run "sshfs …"
                    </button>
                </div>
                
                <!-- RAM filesystem -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Mount temporary RAM partition</strong>
                        <p>Creates a RAM-backed filesystem for fast storage.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('mount -t tmpfs tmpfs /mnt -o size=1024m_enter')">
                        Run "mount -t tmpfs …"
                    </button>
                </div>
                
                <!-- Mirror website -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Download entire website</strong>
                        <p>Mirrors a website with wget while respecting delays.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('wget --random-wait -r -p -e robots=off -U mozilla http://www.example.com_enter')">
                        Run "wget --random-wait …"
                    </button>
                </div>
                
                <!-- Clear terminal -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Clear the terminal</strong>
                        <p>Shortcut to clean the screen instantly.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ctrl-l_enter')">
                        Run "ctrl-l"
                    </button>
                </div>
                
                <!-- diff remote vs local -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Compare remote file with local</strong>
                        <p>Pipes a remote file through SSH into diff.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ssh user@host cat /path/to/remotefile | diff /path/to/localfile -_enter')">
                        Run "ssh … | diff"
                    </button>
                </div>
                
                <!-- SSH through jump host -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>SSH through an intermediate host</strong>
                        <p>Connects to an unreachable machine via a reachable one.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ssh -t reachable_host ssh unreachable_host_enter')">
                        Run "ssh -t reachable_host …"
                    </button>
                </div>
                
                <!-- Update twitter -->           
                <div class="cmd-group">           
                    <div class="command-description_t">           
                        <strong>Tweet from the terminal</strong>              
                        <p>Updates Twitter status using curl.</p>             
                    </div>              
                    <button class="command-button terminal" onclick="sendCommand('curl -u user:pass -d status=\"Tweeting from the shell\" http://twitter.com/statuses/              update.xml_enter')">
                        Run "curl -u user …"
                    </button>
                </div>
                <!-- Simple stopwatch -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Simple stopwatch</strong>
                        <p>Counts the time until you press Ctrl+D.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('time read_enter')">
                        Run "time read"
                    </button>
                </div>
                
                <!-- Console clock top-right -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Console clock in top corner</strong>
                        <p>Shows a live clock at top-right of terminal.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('while sleep 1; do tput sc; tput cup 0 $(($(tput cols)-29)); date; tput rc; done &_enter')">
                        Run "while sleep 1 …"
                    </button>
                </div>
                
                <!-- less behaves like tail -f -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Follow file with less</strong>
                        <p>Makes less behave like tail -f.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('less +F somelogfile_enter')">
                        Run "less +F somelogfile"
                    </button>
                </div>
                
                <!-- Close shell but keep subprocess alive -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Exit shell and keep processes</strong>
                        <p>Closes terminal without killing background jobs.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('disown -a && exit_enter')">
                        Run "disown -a && exit"
                    </button>
                </div>
                
                <!-- Star Wars via telnet -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Watch Star Wars</strong>
                        <p>Plays ASCII Star Wars via telnet.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('telnet towel.blinkenlights.nl_enter')">
                        Run "telnet towel.blinkenlights.nl"
                    </button>
                </div>
                
                <!-- 32 or 64 bit -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Check system bit width</strong>
                        <p>Shows whether system is 32-bit or 64-bit.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('getconf LONG_BIT_enter')">
                        Run "getconf LONG_BIT"
                    </button>
                </div>
                
                <!-- Most used commands -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Most used commands</strong>
                        <p>Lists commands you run most frequently.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('history | awk \'{a[$2]++}END{for(i in a){print a[i] \" \" i}}\' | sort -rn | head_enter')">
                        Run "history | awk …"
                    </button>
                </div>
                
                <!-- Simulate typing -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Simulate typing</strong>
                        <p>Outputs text slowly like movie-style typing.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('echo \"You can simulate on-screen typing just like in the movies\" | pv -qL 10_enter')">
                        Run "pv -qL 10"
                    </button>
                </div>
                
                <!-- Alarm when IP online -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>IP online alarm</strong>
                        <p>Audible alert when host responds.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ping -i 60 -a IP_address_enter')">
                        Run "ping -i 60 -a IP_address"
                    </button>
                </div>
                
                <!-- System reboot magic keys -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Emergency reboot</strong>
                        <p>Safe reboot using magic SysRq keys.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('SysRq_RSEIUB_enter')">
                        Magic keys sequence
                    </button>
                </div>
                
                <!-- Quick rename -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Quick rename</strong>
                        <p>Fast rename of file extension.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('mv filename.{old,new}_enter')">
                        Run "mv filename.{old,new}"
                    </button>
                </div>
                
                <!-- Top 10 memory processes -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Top memory processes</strong>
                        <p>Displays processes sorted by memory usage.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ps aux | sort -nk +4 | tail_enter')">
                        Run "ps aux | sort …"
                    </button>
                </div>
                
                <!-- Delete files except extensions -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Delete all except extensions</strong>
                        <p>Removes every file except selected patterns.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('rm !(*.foo|*.bar|*.baz)_enter')">
                        Run "rm !(*.foo|*.bar|*.baz)"
                    </button>
                </div>
                <!-- Push directory to stack -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Push directory to stack</strong>
                        <p>Saves current working directory for later pop.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('pushd /tmp_enter')">
                        Run "pushd /tmp"
                    </button>
                </div>
                
                <!-- Script last executed command -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Create script from last command</strong>
                        <p>Saves previous command into a shell script.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('echo \"!!\" > foo.sh_enter')">
                        Run "echo '!!' > foo.sh"
                    </button>
                </div>
                
                <!-- Network activity real-time -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Watch network service activity</strong>
                        <p>Lists open network connections live.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('lsof -i_enter')">
                        Run "lsof -i"
                    </button>
                </div>
                
                <!-- Fast access to long command -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Label complex commands</strong>
                        <p>Tag long commands so they’re easy to search later.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('some_very_long_and_complex_command # label_enter')">
                        Run with label
                    </button>
                </div>
                
                <!-- Escape aliases -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Ignore command aliases</strong>
                        <p>Runs original command without alias expansion.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('\\[command]_enter')">
                        Run "\[command]"
                    </button>
                </div>
                
                <!-- Show apps using internet -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Show apps using internet</strong>
                        <p>Lists programs currently talking over the network.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('lsof -P -i -n_enter')">
                        Run "lsof -P -i -n"
                    </button>
                </div>
                
                <!-- diff unsorted files -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Diff unsorted files</strong>
                        <p>Compares sorted output of two files without temp files.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('diff <(sort file1) <(sort file2)_enter')">
                        Run "diff <(sort …)"
                    </button>
                </div>
                
                <!-- reuse all parameters -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Reuse previous parameters</strong>
                        <p>Expands to all arguments from last command.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('!*_enter')">
                        Run "!*"
                    </button>
                </div>
                
                <!-- Backticks are evil -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Better command substitution</strong>
                        <p>Uses $( ) instead of backticks for clarity.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('echo \"The date is: $( date +%D)\"_enter')">
                        Run "echo $(date)"
                    </button>
                </div>
                
                <!-- Share file via HTTP 80 -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Share file over port 80</strong>
                        <p>Serves file content via netcat on port 80.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('nc -v -l 80 < file.ext_enter')">
                        Run "nc -v -l 80 < file.ext"
                    </button>
                </div>
                
                <!-- File system hierarchy -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Show filesystem hierarchy</strong>
                        <p>Displays documentation for Linux directory structure.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('man hier_enter')">
                        Run "man hier"
                    </button>
                </div>
                
                <!-- AWK text block -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Extract text block</strong>
                        <p>Prints content between two patterns.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('awk \'/start_pattern/,/stop_pattern/\' file.txt_enter')">
                        Run "awk '/start/…/stop/'"
                    </button>
                </div>
                
                <!-- CDPATH navigation -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Set CDPATH</strong>
                        <p>Allows cd to jump quickly between common directories.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('CDPATH=:..:~:~/projects_enter')">
                        Run "CDPATH=…"
                    </button>
                </div>
                
                <!-- Save command output to image -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Save output to image</strong>
                        <p>Renders terminal output as a PNG image.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ifconfig | convert label:@- ip.png_enter')">
                        Run "ifconfig | convert"
                    </button>
                </div>
                <!-- Add Password Protection -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Password protect a file (vim)</strong>
                        <p>Edit a file with encryption.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('vim -x <FILENAME>_enter')">
                        Run "vim -x"
                    </button>
                </div>
                
                <!-- Remove duplicate lines -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Remove duplicate lines</strong>
                        <p>Remove duplicates without sorting.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('awk \\''!x[$0]++\\'' <file>_enter')">
                        Run "awk remove dup"
                    </button>
                </div>

                <!-- SSH copy key -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>SSH key copy</strong>
                        <p>Enable passwordless login.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ssh-copy-id username@hostname_enter')">
                        Run "ssh-copy-id"
                    </button>
                </div>
                
                 <!-- Duplicate file finder -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Find duplicate files</strong>
                        <p>Match by size + MD5 hash.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('find -not -empty -type f -printf \\\"%s\\\\n\\\" | sort -rn | uniq -d | xargs -I{} -n1 find -type f               -size {}c -print0 | xargs -0 md5sum | sort | uniq -w32 --all-repeated=separate_enter')">
                        Run "find duplicates"
                    </button>
                </div>
                
                <!-- Kill process locking file -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Kill process using file</strong>
                        <p>Force release of locked file.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('fuser -k filename_enter')">
                        Run "fuser -k"
                    </button>
                </div>
                
                <!-- Insert last command minus last arg -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Bash history trick</strong>
                        <p>Insert last command without last argument.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('!:-_enter')">
                        Run "!: -"
                    </button>
                </div>
                
                <!-- Python SMTP server -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Python SMTP debug server</strong>
                        <p>Local mail test listener.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('python -m smtpd -n -c DebuggingServer localhost:1025_enter')">
                        Run SMTP server
                    </button>
                </div>
                
                <!-- Show Linux distro -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Show distro</strong>
                        <p>Display installed OS.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('cat /etc/issue_enter')">
                        Run "cat /etc/issue"
                    </button>
                </div>
                
                <!-- Find process ignoring grep -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Find process</strong>
                        <p>Search without showing grep process.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ps aux | grep [p]rocess-name_enter')">
                        Run "ps aux | grep"
                    </button>
                </div>
                
                <!-- Extract tarball from net -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Extract online tarball</strong>
                        <p>No need to save locally.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('wget -qO - \\\"http://www.tarball.com/tarball.gz\\\" | tar zxvf -_enter')">
                        Run "wget | tar"
                    </button>
                </div>
                
                <!-- Copy SSH key without ssh-copy-id -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Manual SSH key copy</strong>
                        <p>Alternative when ssh-copy-id is missing.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('cat ~/.ssh/id_rsa.pub | ssh user@machine \\\"mkdir ~/.ssh; cat >> ~/.ssh/authorized_keys\\\"_enter')">
                        Run manual SSH copy
                    </button>
                </div>
                
                <!-- Matrix style rain-->         
                <div class="cmd-group">           
                    <div class="command-description_t">          
                        <strong>Matrix style</strong>             
                        <p>Green digital rain effect.</p>             
                    </div>              
                    <button class="command-button terminal" onclick="sendCommand('tr -c \\\"[:digit:]\\\" \\\" \\\" < /dev/urandom | dd cbs=$COLUMNS conv=unblock |                 GREP_COLOR=\\\"1;32\\\" grep --color \\\"[^ ]\\\"_enter')">
                        Run Matrix
                    </button>
                </div>
                
                <!-- Replace spaces with underscores -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Fix filenames</strong>
                        <p>Replace spaces with underscores.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('rename \\''y/ /_/\\'' * _enter')">
                        Run rename
                    </button>
                </div>
                
                <!-- Rip audio from video -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Rip audio</strong>
                        <p>Extract audio track from video.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('mplayer -ao pcm -vo null -vc dummy -dumpaudio -dumpfile output.wav input.mp4_enter')">
                        Run audio rip
                    </button>
                </div>
                
                <!-- Google Translate function -->         
                <div class="cmd-group">           
                    <div class="command-description_t">           
                        <strong>Translate text</strong>           
                        <p>Quick Google Translate from terminal.</p>              
                    </div>              
                    <button class="command-button terminal" onclick="sendCommand('translate(){ wget -qO- \\\"http://ajax.googleapis.com/ajax/services/language/             translate?v=1.0&q=$1&langpair=$2|${3:-en}\\\" | sed \\\"s/.*\\\"translatedText\\\":\\\"\\([^\\\"]*\\)\\\".*}/\\\\1/\\\"; } _enter')">
                        Run translate()
                    </button>
                </div>
                
                <!-- Insert autocomplete results -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Autocomplete expand</strong>
                        <p>Insert all matches into command line.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ESC * _enter')">
                        Run ESC *
                    </button>
                </div>
                
                <!-- Invoke editor for previous command -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Edit last cmd</strong>
                        <p>Open editor for previous command.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('fc_enter')">
                        Run fc
                    </button>
                </div>
                
                <!-- Peek RAM strings -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>View RAM text</strong>
                        <p>Dump readable strings from RAM.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('sudo dd if=/dev/mem | cat | strings_enter')">
                        Run RAM peek
                    </button>
                </div>
                
                <!-- Graphical tree -->           
                <div class="cmd-group">           
                    <div class="command-description_t">           
                        <strong>Tree view</strong>            
                        <p>ASCII directory structure.</p>             
                    </div>              
                    <button class="command-button terminal" onclick="sendCommand('ls -R | grep \\\":$\\\" | sed -e \\\"s/:$//\\\" -e \\\"s/[^-][^\\/]*\\//--/g\\\" -e \\\"s/^/   /\\\"              -e \\\"s/-/|/\\\" _enter')">
                        Run tree
                    </button>
                </div>
                
                <!-- Intercept stdout/stderr -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Trace output</strong>
                        <p>Catch another process output.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('strace -ff -e trace=write -e write=1,2 -p SOME_PID_enter')">
                        Run strace
                    </button>
                </div>
                
                <!-- Copy with progress -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Copy with progress</strong>
                        <p>Show speed/info while copying.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('pv sourcefile > destfile_enter')">
                        Run pv copy
                    </button>
                </div>
                
                <!-- Quick calculator -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Terminal calc</strong>
                        <p>Math using bc.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('? () { echo \\\"$*\\\" | bc -l; } _enter')">
                        Run ?()
                    </button>
                </div>
                
                <!-- Create ISO -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Create ISO</strong>
                        <p>Make CD/DVD ISO image.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('readom dev=/dev/scd0 f=/path/to/image.iso_enter')">
                        Run ISO create
                    </button>
                </div>
                
                <!-- mkdir + cd -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Make & enter</strong>
                        <p>Create directory and enter it.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('mkdir /home/foo/doc/bar && cd $_enter')">
                        Run mkdir+cd
                    </button>
                </div>
                
                <!-- man page → PDF -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Man to PDF</strong>
                        <p>Convert manpage to PDF file.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('man -t manpage | ps2pdf - filename.pdf_enter')">
                        Run man→pdf
                    </button>
                </div>
                
                <!-- Stream YouTube to mplayer -->         
                <div class="cmd-group">           
                    <div class="command-description_t">           
                        <strong>YT to mplayer</strong>            
                        <p>Play YouTube URL directly.</p>             
                    </div>              
                    <button class="command-button terminal" onclick="sendCommand('i=\\\"8uyxVmdaJ-w\\\"; mplayer -fs $(curl -s \\\"http://www.youtube.com/              get_video_info?&video_id=$i\\\" | echo -e $(sed \\\"s/%/\\\\x/g;s/.*\\\\(v[0-9]\\\\.lscache.*\\\\)/http:\\/\\/\\\\1/g\\\") | grep -oP \\\"^[^|,]*\\\" )_enter')">
                        Run YT stream
                    </button>
                </div>
                
                <!-- mkdir with intermediate directories -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Create full path</strong>
                        <p>Make all parent directories.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('mkdir -p a/long/directory/path_enter')">
                        Run mkdir -p
                    </button>
                </div>
                
                <!-- ps? alias -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Search processes</strong>
                        <p>Shortcut alias for ps.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('alias \\\"ps?=ps ax | grep \\\"_enter')">
                        Run alias ps?
                    </button>
                </div>
                
                <!-- multiple var assignment -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Multi assign</strong>
                        <p>Split command output into variables.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('read day month year <<< $(date +\\\"%d %m %y\\\")_enter')">
                        Run date read
                    </button>
                </div>
                
                <!-- remove all except one -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Keep one file</strong>
                        <p>Delete everything except chosen file.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('rm -f !(survivior.txt)_enter')">
                        Run rm !(file)
                    </button>
                </div>
                
                <!-- git add -u -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Git cleanup</strong>
                        <p>Stage deleted files.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('git add -u_enter')">
                        Run git add -u
                    </button>
                </div>
                
                <!-- edit remote file -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Edit remote file</strong>
                        <p>Open remote file in vim.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('vim scp://username@host//path/to/somefile_enter')">
                        Run remote vim
                    </button>
                </div>
                
                <!-- job control -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Job control</strong>
                        <p>Background & disown tasks.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('^Z; bg; disown_enter')">
                        Run job control
                    </button>
                </div>
                
                <!-- random password -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Random password</strong>
                        <p>30-character random string.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('strings /dev/urandom | grep -o \\\"[[:alnum:]]\\\" | head -n 30 | tr -d \\\\n; echo_enter')">
                        Run password gen
                    </button>
                </div>
                
                <!-- apps using internet -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Network apps</strong>
                        <p>Show current internet connections.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ss -p_enter')">
                        Run ss -p
                    </button>
                </div>
                
                <!-- connections per host -->          
                <div class="cmd-group">           
                    <div class="command-description_t">           
                        <strong>Connection graph</strong>             
                        <p>Count active connections by host.</p>              
                    </div>              
                    <button class="command-button terminal" onclick="sendCommand('netstat -an | grep ESTABLISHED | awk \\\"{ print $5 }\\\" | awk -F: \\\"{ print $1 }\\\" | sort |                 uniq -c | awk \\\"{ printf(\\\\\\\"%s\\\\t%s\\\\t\\\\\\\",$2,$1); for(i=0;i<$1;i++){printf(\\\\\\\"*\\\\\\\")}; print \\\\\\\"\\\\\\\" }\\\"_enter')">
                        Run connection graph
                    </button>
                </div>
                
                <!-- record screencast -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Screencast</strong>
                        <p>Record screen to MPEG.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('ffmpeg -f x11grab -r 25 -s 800x600 -i :0.0 /tmp/outputFile.mpg_enter')">
                        Run screencast
                    </button>
                </div>
                
                <!-- monitor progress -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Monitor progress</strong>
                        <p>Track file compression speed.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('pv access.log | gzip > access.log.gz_enter')">
                        Run pv | gzip
                    </button>
                </div>
                
                <!-- grep recursively -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Search text</strong>
                        <p>Find pattern in all files.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('grep -RnisI <pattern> * _enter')">
                        Run grep -R
                    </button>
                </div>
                
                <!-- monitor MySQL queries -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Monitor MySQL</strong>
                        <p>Watch running queries live.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('watch -n 1 mysqladmin --user=<user> --password=<password> processlist_enter')">
                        Run MySQL monitor
                    </button>
                </div>
                
                <!-- biggest files -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Top big files</strong>
                        <p>Show largest items in directory.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('du -s * | sort -n | tail_enter')">
                        Run size check
                    </button>
                </div>
                
                <!-- show 256 colors -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>256 colors</strong>
                        <p>Show terminal color table.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('for code in {0..255}; do echo -e \\\\\"\\\\e[38;05;${code}m ${code}: Test\\\\\"; done_enter')">
                        Run color table
                    </button>
                </div>
                
                <!-- remove empty dirs -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Clean empty dirs</strong>
                        <p>Remove all empty folders.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('find . -type d -empty -delete_enter')">
                        Run clean
                    </button>
                </div>
                
                <!-- cool clock -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Terminal clock</strong>
                        <p>Show animated clock.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('watch -t -n1 \\\"date +%T|figlet\\\"_enter')">
                        Run clock
                    </button>
                </div>
                
                <!-- seconds to time -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Readable time</strong>
                        <p>Convert epoch to human format.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('date -d@1234567890_enter')">
                        Run time convert
                    </button>
                </div>
                
                <!-- weather -->
                <div class="cmd-group">
                    <div class="command-description_t">
                        <strong>Weather forecast</strong>
                        <p>Live weather in terminal.</p>
                    </div>
                    <button class="command-button terminal" onclick="sendCommand('curl wttr.in/seville_enter')">
                        Run weather
                    </button>
                </div>
            </div>
        </div>


            
        <!-- One-click text commands -->
        <div class="button-group">
            <h3>Quick Text Commands</h3>
            <button class="command-button" onclick="sendCommand('Hello World!')">Send "Hello World!"</button>
            <button class="command-button" onclick="sendCommand('sudo apt update')">Send "sudo apt update"</button>
            <button class="command-button" onclick="sendCommand('ls -la')">Send "ls -la"</button>
            <button class="command-button" onclick="sendCommand('pwd')">Send "pwd"</button>
            <button class="command-button" onclick="sendCommand('whoami')">Send "whoami"</button>
            <button class="command-button" onclick="sendCommand('enter')">↵ Enter Key</button>
        </div>
        
        <!-- One-click special commands -->
<!--         <div class="button-group">
            <h3>Quick Special Commands</h3>
            <button class="command-button" onclick="sendCommand('enter')">↵ Enter Key</button>
            <button class="command-button" onclick="sendCommand('tab')">⇥ Tab Key</button>
            <button class="command-button" onclick="sendCommand('space')">␣ Space Bar</button>
            <button class="command-button" onclick="sendCommand('backspace')">⌫ Backspace</button>
            <button class="command-button" onclick="sendCommand('esc')">⎋ Escape</button>
        </div> -->
        
        <!-- One-click system commands -->
<!--         <div class="button-group">
            <h3>Quick System Commands</h3>
            <button class="command-button" onclick="sendCommand('ctrl+c')">Ctrl + C (Copy/Interrupt)</button>
            <button class="command-button" onclick="sendCommand('ctrl+v')">Ctrl + V (Paste)</button>
            <button class="command-button" onclick="sendCommand('ctrl+a')">Ctrl + A (Select All)</button>
            <button class="command-button" onclick="sendCommand('ctrl+z')">Ctrl + Z (Undo)</button>
            <button class="command-button" onclick="sendCommand('ctrl+l')">Ctrl + L (Clear Terminal)</button>
        </div> -->


        <!-- Custom Command -->
        <div class="command-section">
            <h3>🔧 Custom Linux Command</h3>
            <div class="button-group">
                <input type="text" id="customLinuxCommand" placeholder="Enter custom Linux command" 
                       style="padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; flex-grow: 1;">
                <button class="command-button" onclick="sendCustomLinuxCommand()" 
                        style="white-space: nowrap;">Execute Custom Command</button>
            </div>
            <div class="command-description">
                <strong>Custom Commands:</strong> Enter any Linux shortcut combination (e.g., "ctrl+alt+down" for workspace down, "super+space" for switcher).
            </div>
        </div>
    </div>

    <script>
        function showMessage(text, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = text;
            messageDiv.className = 'message ' + type;
            messageDiv.style.display = 'block';
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 4000);
        }

        function sendCommand(command) {
            fetch('hid_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'command=' + encodeURIComponent(command)
            })
            .then(response => response.text())
            .then(data => {
                showMessage('✅ Linux command executed: ' + command, 'success');
            })
            .catch(error => {
                showMessage('❌ Error sending command: ' + error, 'error');
            });
        }

        function sendCustomLinuxCommand() {
            const command = document.getElementById('customLinuxCommand').value;
            if (command) {
                sendCommand(command);
                document.getElementById('customLinuxCommand').value = '';
            } else {
                showMessage('⚠️ Please enter a custom command', 'error');
            }
        }

        // Handle Enter key in custom command input
        document.getElementById('customLinuxCommand').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendCustomLinuxCommand();
            }
        });
    </script>
</body>
</html>
