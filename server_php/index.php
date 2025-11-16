<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raspberry Pi Zero HID Control Server</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 50px 40px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 3em;
            margin-bottom: 15px;
            font-weight: 300;
        }
        
        .header p {
            font-size: 1.3em;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .status-bar {
            background: #34495e;
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2ecc71;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .dashboard {
            padding: 40px;
        }
        
        .section-title {
            font-size: 2em;
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 300;
        }
        
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #3498db, #2ecc71);
        }
        
        .card.windows::before {
            background: linear-gradient(135deg, #0078d4, #00bcff);
        }
        
        .card.linux::before {
            background: linear-gradient(135deg, #e44d26, #f16529);
        }
        
        .card.basic::before {
            background: linear-gradient(135deg, #2ecc71, #3498db);
        }
        
        .card-icon {
            font-size: 3em;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .card h3 {
            font-size: 1.5em;
            color: #2c3e50;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .card p {
            color: #7f8c8d;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .features-list {
            list-style: none;
            margin-bottom: 25px;
        }
        
        .features-list li {
            padding: 8px 0;
            border-bottom: 1px solid #ecf0f1;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .features-list li:last-child {
            border-bottom: none;
        }
        
        .features-list li::before {
            content: '✓';
            color: #2ecc71;
            font-weight: bold;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            text-align: center;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .btn.windows {
            background: linear-gradient(135deg, #0078d4, #106ebe);
        }
        
        .btn.linux {
            background: linear-gradient(135deg, #e44d26, #c53727);
        }
        
        .btn.basic {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
        }
        
        .info-section {
            background: #f8f9fa;
            padding: 40px;
            border-radius: 15px;
            margin-top: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .info-item {
            text-align: center;
            padding: 20px;
        }
        
        .info-item i {
            font-size: 2.5em;
            color: #3498db;
            margin-bottom: 15px;
        }
        
        .info-item h4 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        
        .info-item p {
            color: #7f8c8d;
            font-size: 0.95em;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 30px;
            margin-top: 50px;
        }
        
        .footer p {
            opacity: 0.8;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }
            
            .header p {
                font-size: 1.1em;
            }
            
            .cards-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <h1>🖥️ Raspberry Pi Zero HID Server</h1>
            <p>Remote Keyboard & Command Control Interface</p>
        </div>
        
        <!-- Status Bar -->
        <div class="status-bar">
            <div class="status-item">
                <div class="status-indicator" style="background: #2ecc71;"></div>
                <span>HID Device: Active & Ready</span>
            </div>
            <div class="status-item">
                <div class="status-indicator" style="background: #2ecc71;"></div>
                <span>Web Server: Running</span>
            </div>
            <div class="status-item">
                <div class="status-indicator" style="background: #2ecc71;"></div>
                <span>HID Service: Enabled</span>
            </div>
            <div class="status-item">
                <span>Device: /dev/hidg0</span>
            </div>
        </div>
        
        <!-- Main Dashboard -->
        <div class="dashboard">
            <h2 class="section-title">Control Interfaces</h2>
            
            <div class="cards-grid">
                <!-- Basic HID Control Card -->
                <div class="card basic">
                    <div class="card-icon">⌨️</div>
                    <h3>Basic HID Control</h3>
                    <p>Simple keyboard input and text automation for general purposes</p>
                    
                    <ul class="features-list">
                        <li>Send custom text messages</li>
                        <li>Basic keyboard shortcuts</li>
                        <li>Auto-typing functionality</li>
                        <li>Real-time text input</li>
                        <li>Special key commands</li>
                        <li>Custom automation scripts</li>
                    </ul>
                    
                    <a href="hid_control.php" class="btn basic">Open Basic Control Panel</a>
                </div>
                
                <!-- Windows Commands Card -->
                <div class="card windows">
                    <div class="card-icon">🪟</div>
                    <h3>Windows Commands</h3>
                    <p>Windows-specific keyboard shortcuts and system commands</p>
                    
                    <ul class="features-list">
                        <li>Windows key shortcuts</li>
                        <li>System navigation commands</li>
                        <li>Application management</li>
                        <li>File Explorer controls</li>
                        <li>Task Manager access</li>
                        <li>Virtual desktop controls</li>
                        <li>Security screen commands</li>
                        <li>Screenshot shortcuts</li>
                    </ul>
                    
                    <a href="hid_windows_commands.php" class="btn windows">Open Windows Commands</a>
                </div>
                
                <!-- Linux Commands Card -->
                <div class="card linux">
                    <div class="card-icon">🐧</div>
                    <h3>Linux Commands</h3>
                    <p>Linux terminal shortcuts and desktop environment controls</p>
                    
                    <ul class="features-list">
                        <li>Terminal emulator shortcuts</li>
                        <li>GNOME desktop commands</li>
                        <li>Process control keys</li>
                        <li>Window management</li>
                        <li>System lock/logout</li>
                        <li>Screenshot controls</li>
                        <li>Browser shortcuts</li>
                        <li>Custom shell commands</li>
                    </ul>
                    
                    <a href="hid_linux_commands.php" class="btn linux">Open Linux Commands</a>
                </div>
            </div>
            
            <!-- Information Section -->
            <div class="info-section">
                <h2 class="section-title">How It Works</h2>
                
                <div class="info-grid">
                    <div class="info-item">
                        <i>🔌</i>
                        <h4>HID Device Setup</h4>
                        <p>Raspberry Pi Zero acts as a USB Human Interface Device (keyboard) when connected to target computers</p>
                    </div>
                    
                    <div class="info-item">
                        <i>🌐</i>
                        <h4>Web Interface</h4>
                        <p>Access the control panel from any device on the network through a web browser</p>
                    </div>
                    
                    <div class="info-item">
                        <i>⌨️</i>
                        <h4>Remote Control</h4>
                        <p>Send keyboard commands and shortcuts to the connected computer remotely</p>
                    </div>
                    
                    <div class="info-item">
                        <i>⚡</i>
                        <h4>Real-time Execution</h4>
                        <p>Commands are executed instantly on the target machine with visual feedback</p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Start Guide -->
            <div class="info-section">
                <h2 class="section-title">Quick Start Guide</h2>
                
                <div class="info-grid">
                    <div class="info-item">
                        <i>1</i>
                        <h4>Connect Pi to Target</h4>
                        <p>Connect your Raspberry Pi Zero to the target computer via USB cable</p>
                    </div>
                    
                    <div class="info-item">
                        <i>2</i>
                        <h4>Access Web Interface</h4>
                        <p>Open a browser and navigate to this Pi's IP address</p>
                    </div>
                    
                    <div class="info-item">
                        <i>3</i>
                        <h4>Choose Control Panel</h4>
                        <p>Select the appropriate control panel based on the target OS</p>
                    </div>
                    
                    <div class="info-item">
                        <i>4</i>
                        <h4>Send Commands</h4>
                        <p>Click buttons or enter custom commands to control the target computer</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Raspberry Pi Zero HID Server | Built for Remote Keyboard Control</p>
            <p>OS: Raspbian GNU/Linux 11 (bullseye) | Device: /dev/hidg0</p>
        </div>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            const buttons = document.querySelectorAll('.btn');
            
            // Add click effects to buttons
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Add ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.6);
                        transform: scale(0);
                        animation: ripple 600ms linear;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                    `;
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
            
            // Add staggered animation to cards
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
        
        // Add CSS for ripple effect
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            .btn {
                position: relative;
                overflow: hidden;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
