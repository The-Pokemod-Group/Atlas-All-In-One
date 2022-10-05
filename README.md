# Pokemod Atlas All-In-One

An all-in-one package with the minimum third-party requirements to get started as quickly as possible with [Pokemod Atlas](https://pokemod.dev/atlas).

**❤️ PULL REQUESTS ARE WELCOME ❤️**

---

This will help you setup the following third party services:
- RealDeviceMap
- MariaDB database for RDM
- PhpMyAdmin for the database
- RDM Tools (optional, custom dockerized version)

This still requires technical skills in computers and servers. Please, do not ask for support nor open issues about the services themselves.

> ## __**WARNING!**__
> By default, this package is intended exclusively for testing and local environments.
>
> **DO NOT EXPOSE THE DEFAULT SETUP TO THE INTERNET**.
>
> If you really want the convenience and use it for production purposes, you need at the very least to perform [these steps](#minimum-changes-for-remote-access), but **YOU'RE ON YOUR OWN!**. You have been warned.
___

## Getting started (Linux/iOS)

- Install `docker` and `docker-compose`.

- Login to GitHub registry with your GitHub credentials (unfortunately RDM requires a GitHub account, you can [create one here](https://github.com/join)):
    <!-- echo $CR_PAT | docker login docker.pkg.github.com -u USERNAME --password-stdin
    # if that doesn't work, you can use: -->
    ```bash
    docker login docker.pkg.github.com -u USERNAME --password PASSWORD
    ```

- Open a shell (terminal) at the directory you extracted the zip.

- Run `docker-compose up -d`. This will download the images and perform the first time initialization setup for each one of the services.

- If no errors appear, the containers should be running. Open your browser and [check `localhost:9000`](http://localhost:9000) to confirm.

## Getting started (Windows)

- Install [Docker Desktop](https://docs.docker.com/desktop/windows/install/)

- Login to GitHub registry with your GitHub credentials (unfortunately RDM requires a GitHub account, you can [create one here](https://github.com/join)):
    - Open up a PowerShell Terminal
        - Press Super+R (Super is the "Windows" key)
        - Type powershell and press OK
        - Log into your GitHub account with the docker command below (Replace USERNAME and PASSWORD with your own)
            <!-- echo $CR_PAT | docker login docker.pkg.github.com -u USERNAME --password-stdin
            # if that doesn't work, you can use: -->
            ```bash
            docker login docker.pkg.github.com -u USERNAME --password PASSWORD
            ```
- Run `docker-compose up -d db` in your PowerShell from the Atlas-All-In-One folder (Shift+Right click on empty space of the file explorer)
- After it is done pulling run `docker-compose logs -f` in your Powershell terminal.
- Once you see these two lines on the logs

    `atlas-db  | 2022-04-30  6:46:02 0 [Note] mariadbd: ready for connections.`

    `atlas-db  | Version: '10.7.3-MariaDB-1:10.7.3+maria~focal'  socket: '/run/mysqld/mysqld.sock'  port: 3306  mariadb.org binary distribution`

    Press CTRL+C to close the logs.
- Now you may run `docker-compose up -d` to pull the rest of the files and startup RDM.
- You need to open up the port 9001 so devices can access it to connect to your Windows Computer.
    You can do that by opening an elevated command prompt or an elevated Powershell terminal and executing this command

    ```netsh advfirewall firewall add rule name="TCP Port 9001" dir=in action=allow protocol=TCP localport=9001```

## Basic RDM Setup

- To get the first time RDM's Access Token you need to look for it in the logs. Go back to your terminal and run:

      docker logs atlas-rdm

- In between the logs, look for a line like that looks like this:

      [INFO] [MAIN] Use this access-token to create the admin user: ACCESS_TOKEN

- [Open `localhost:9000`](http://localhost:9000) in your browser, and fill all the fields, using the token you got below as the Access Token.

  > If the URL doesn't work, try [this](http://0.0.0.0:9000) and [this](http://127.0.0.1:9000) one too for good measure.

- Make note of the local IP of the machine running RDM. You can use the shortcuts below:
    - Linux:

          ip -o route get to 8.8.8.8 | sed -n 's/.*src \([0-9.]\+\).*/\1/p'

    - Windows:
        - Press Super+R (Super is the "Windows" key)
        - Type `cmd`
        - Type `ipconfig` and press Enter
        - Look for the line that says `IPv4`. The IP is right next to it.

## Initial ReactMap Setup

- ReactMap requires you to manually create a database first:
    - Open the [PhpMyAdmin](http://localhost:9200) instance
    - Click at **New** in the left sidebar, to create a new database
    - Type `manual_db` in the field **Database name**
    - Click the button **Create**
    - Repeat with `reactmap_db`
- Wait a couple seconds and you should be able to access it at the [port 9300](http://localhost:9300).

## Entire Atlas Setup
- Install the Atlas APK and Pokémon GO in your device.
- Open Atlas and do the one-time initial setup:
    - RDM URL: `http://IP-YOU-GOT-ABOVE:9001` _(note this is 9001, not 9000)._
    - Auth Bearer: Fill in with the same string as Device Endpoint Secret in RDM's settings
    - Device Name: anything you want.
    - Email: the e-mail you used to register at [atlas.pokemod.dev](https://atlas.pokemod.dev)
    - Device auth token: this is the token you initially got after your first login on [atlas.pokemod.dev](https://atlas.pokemod.dev).
    > Tip: If you lost your token, you can click _Reset Device Token_ to get a new one, just make sure you save it somewhere safe this time around.

- That's all. You can now [set up RDM](https://realdevicemap.readthedocs.io/en/latest/realdevicemap/dashboard/index.html) and check the device at the [Devices Dashboard](https://localhost:9000/dashboard/devices).

> Tip: after the initial setup, any changes to the configs above can be done from [atlas.pokemod.dev](https://atlas.pokemod.dev). It's not necessary to manually change every device one by one.

___

## Tips and Tricks

### Default URLs

- RDM Web UI:  http://localhost:9000
- RDM Webhook: http://localhost:9001
- RDM Tools:   http://localhost:9100
- PHPMyAdmin:  http://localhost:9200
- ReactMap:    http://localhost:9300

### Checking the current status of the services

Every service is what's called a docker container. You can check the status of running containers with:

    $ docker ps

### RDM images and database

- The directory `./data` contains the database. You can use this to make a backup of your data or delete it to start from scratch, for example.

- To get images working in RDM, uncomment the images volume in your docker-compose.yml file.
    - Download any [UICONS repository](https://github.com/nileplumb/PkmnHomeIcons) and put it into a folder called images in your Atlas All In One folder
    - Go into the RDM settings and in the box `Icon Styles (one per line, Name;Folder)` type in whatever you want your icons named followed by the folder it's on ie. `UICONS;PkmnHomeIcons/UICONS` PkmnHomeIcons is the root folder of the repo and UICONS would be the folder where the index.json is at.
    - After this `docker-compose down && docker-compose up -d` and your images should start rendering into RDM. Be aware that this takes more storage because of the way RDM handles images is a bit odd.

### Starting from scratch and dealing with docker failures

You can cleanup everything, **except the data**, by stopping and removing the containers like below:

    $ docker-compose stop
    $ docker-compose rm

Starting the containers again with `docker-compose up -d` will still use `./data` directory and restore everything.

## Using a local database outside of Docker

- Edit `docker-compose.yaml`:
    - Uncomment this block:
        ```yml
            extra_hosts:
            - "host.docker.internal:host-gateway"
        ```

Comment out/delete this in both rdm and pma
```
    depends_on:
      - db
```

Also make sure to comment out or delete all of the below otherwise you will end up creating a database anyway

```
 db:
    image: mariadb:latest
    command: --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci --default-authentication-plugin=mysql_native_password --binlog-expire-logs-seconds=86400
    container_name: atlas-db
    networks:
      - atlas-network
    ports:
      - 3306:3306
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: pokemodrules
      MYSQL_DATABASE: rdmdb
      MYSQL_USER: rdmuser
      MYSQL_PASSWORD: pokemodrules
    volumes:
      - ./data:/var/lib/mysql
#     - /etc/localtime:/etc/localtime:ro
```


Your `db_host` needs to be `host.docker.internal` not `localhost` anywhere else you need to enter your DB host in docker always use `host.docker.internal`

The DB user you are using needs to have host permissions for either `%` or your docker bridge IP

You can find your docker bridge IP by using

```
docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' container_name_or_id
```

If you don't know your container name or id you can view all current containers with `docker ps`

To create a user with % permissions run the following query on your DB

```
CREATE USER 'USER'@'%' IDENTIFIED BY 'PASSWORD';
GRANT ALL PRIVILEGES ON DB.* TO 'USER'@'%';
FLUSH PRIVILEGES;
```

Your MySQL/MariaDB conf file needs to have either `bind-address = [DOCKERIP]` if you are only connecting from Docker to the DB and nothing else(including localhost!) or  `bind-address = 0.0.0.0` to enable access from any IP (This could open you up to access from any IP on the web so make sure your firewall is correctly configured)


## Minimum Changes For Remote Access
- Edit `docker-compose.yaml`:
  - Change the following variables accordingly:
      ```yaml
      DB_USERNAME: rdmuser
      DB_PASSWORD: pokemodrules
      DB_ROOT_USERNAME: root
      DB_ROOT_PASSWORD: pokemodrules
      [...]
      MYSQL_ROOT_PASSWORD: pokemodrules
      MYSQL_DATABASE: rdmdb
      MYSQL_USER: rdmuser
      MYSQL_PASSWORD: pokemodrules
      ```
  - **Delete** the following lines, _(if you keep them, your database will be accesible by everyone with the URL)._
      ```yaml
      PMA_USER: root
      PMA_PASSWORD: pokemodrules
      ```
- (RDM-Tools Only) Edit `tools/config/config.php` and change the lines below accordingly:
    ```php
    define('DB_USER', getenv("DB_USER") ?: "root");
    define('DB_PSWD', getenv("DB_PSWD") ?: "pokemodrules");
    define('DB_NAME', getenv("DB_NAME") ?: "rdmdb");
    ```
- Browse to [your router's IP](https://wiki.amahi.org/index.php/Find_Your_Gateway_IP) and use the power of Google to find out how do you forward the ports using NAT for your router's brand:

      MyRouterBrand MyRouterModel "Port Forwarding"|NAT

  > You need to expose the ports 9000 and 9001 (and 9100 if you want RDM-tools to be accesible from outside)
