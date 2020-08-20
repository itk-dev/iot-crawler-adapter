# Smartcitizen IoT Crawler Adapter Proxy (SCICAP)

## Installation

Copy `config/local.json.dist` to `config/local.json` and edit appropriately
(Note: You can use [JSON5](https://json5.org/) in the config).

```sh
yarn install
```

## Start the show with `supervisor`

```conf
[program:scicap]
directory=/data/www/iot-crawler-adapter/htdocs/smartcitizen/proxy
command=/usr/bin/node /data/www/iot-crawler-adapter/htdocs/smartcitizen/proxy/app.js
autostart=true
autorestart=true
environment=NODE_ENV=production
stderr_logfile=/data/www/iot-crawler-adapter/htdocs/smartcitizen/proxy/scicap.err.log
stdout_logfile=/data/www/iot-crawler-adapter/htdocs/smartcitizen/proxy/scicap.out.log
user=deploy
```

```sh
sudo service supervisor restart
sudo supervisorctl status
```

## Start the show with `pm2`

Install [pm2](https://pm2.keymetrics.io/).

For development, run

```sh
pm2 start ecosystem.config.js --env=development
```

For deployment, run

```sh
pm2 start ecosystem.config.js
```

After updates, run

```sh
pm2 reload ecosystem.config.js
```

## Coding standards

```sh
yarn coding-standards-check
```

```sh
yarn coding-standards-apply
```
