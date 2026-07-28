import subprocess
import time
import sys
import os

def run_app():
    print("Starting Laravel backend...")
    # Using php built-in server as artisan serve was having port issues
    backend = subprocess.Popen(["php", "-S", "127.0.0.1:8000", "-t", "public"], shell=True)
    
    print("Starting Vite frontend...")
    # Using cmd /c to bypass potential PowerShell execution policy restrictions
    frontend = subprocess.Popen(["cmd", "/c", "npm", "run", "dev"], shell=True)

    print("\nApplication running!")
    print("Backend: http://127.0.0.1:8000")
    print("Frontend: http://localhost:5173")
    print("\nPress Ctrl+C to stop both servers.")

    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("\nStopping servers...")
        backend.terminate()
        frontend.terminate()
        sys.exit(0)

if __name__ == "__main__":
    run_app()
