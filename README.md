# ![Logo](https://user-images.githubusercontent.com/6297755/29741970-b4bd37a4-8ab1-11e7-9bd5-6151010ee9a4.png)YaongWiki

> Lightweight wiki engine for general use

YaongWiki is free and open source collaborative editing software that runs a lightweight  and scalable wiki. It is written in PHP with MySQL as a backend and well supports HTML5.

## Showcase
Visit [yaongwiki.org](http://yaongwiki.org)

## Features
- Markdown syntax (powered by [SimpleMDE](https://simplemde.com/))
- Language support (currently English and Korean)
- Hackable themes
- Fulltext search
- [reCAPTCHA](https://www.google.com/recaptcha) with easy settings
- Permission management
- Page processor model that brings modularity. 


## Required Environment
- Webserver (NGINX or Apache is recommended)
- PHP7
- MySQL (or MariaDB)

## Installation

Copy contents of `src` folder to your desired remote directory. After that, you can start installation process visiting the directory.

## Installation for developers (Using [Docker](https://www.docker.com/))
For contributors and testers, YaongWiki have ready-to-launch docker configuration. Access through `localhost:8001` is available after starting the docker container.

```
$ docker-compose up
```

## License
By the terms of [GPL-2.0](#) license, YaongWiki can be used by anyone. 