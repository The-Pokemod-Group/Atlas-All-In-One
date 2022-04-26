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
- Wait a couple seconds and you should be able to access it at the [port 9300](http://localhost:9300).

## Entire Atlas Setup
- Install the Atlas APK and Pokémon GO in your device.
- Open Atlas and do the one-time initial setup:
    - RDM URL: `http://IP-YOU-GOT-ABOVE:9001` _(note this is 9001, not 9000)._
    - Auth Bearer: leave empty.
    - Device Name: anything you want.
    - Email: the e-mail you used to register at [atlas.pokemod.dev](https://atlas.pokemod.dev)
    - Device auth token: this is the token you initially got after your first login on [atlas.pokemod.dev](https://atlas.pokemod.dev).
    > Tip: If you lost your token, you can click _Reset Device Token_ to get a new one, just make sure you save it somewhere safe this time around.

- That's all. You can now [set up RDM](https://realdevicemap.readthedocs.io/en/latest/realdevicemap/dashboard/index.html) and check the device at the [Devices Dashboard](https://localhost:9000/dashboard/devices).

> Tip: after the initial setup, any changes to the configs above can be done from [atlas.pokemod.dev](https://atlas.pokemod.dev). It's not necessary to manually change every device one by one.

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

- RDM recently changed the way images are handled. Until this repository is updated, use [the official documentation](https://github.com/RealDeviceMap/RealDeviceMap/wiki/3.-Map-Images) for setting up images and check [RDM Discord](https://discord.gg/SQshbfrSzT) for more information.

### Starting from scratch and dealing with docker failures

You can cleanup everything, **except the data**, by stopping and removing the containers like below:

    $ docker-compose stop
    $ docker-compose rm

Starting the containers again with `docker-compose up -d` will still use `./data` directory and restore everything.

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
