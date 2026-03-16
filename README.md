<h1 align="center">Mini Vault</h1>
<h3 align="center">Your home lab password manager</h3>

---

<p align="center">
<img src="https://raw.githubusercontent.com/MediaBitLtd/mini_vault/refs/heads/main/public/img/logo.svg?sanitize=true" width="256" height="256" />
<br />
<br />
<img alt="license" src="https://img.shields.io/github/license/MediaBitLtd/mini_vault" />
<img alt="license" src="https://img.shields.io/github/release/MediaBitLtd/mini_vault" />
<img alt="license" src="https://img.shields.io/github/issues/MediaBitLtd/mini_vault" />
</p>

---

MiniVault is a free password manager for your home lab setup. It's intended to provide basic storage of your passwords
and secrets, alongside OAuth2 authentication for your server applications. It supports various types of secrets
including two-factor authentication, all secured using password hash encryption.

**Warning** - Use this at your own risk and do not expose this app to the public, as it increases the chances of your
own setup being targeted by an attack.

It works by encrypting the user's main password against any record and vault created. Your passwords (and any
information stored in MiniVault) are secured using bcrypt encryption and never stored in plain text. No data is
accessible without the user's main password. This also means that if you lose your main password, you will **never** be
able to recover your data — don't forget your main password!

## Setup

Installation instructions will depend on your own environment and it's not recommended for beginners to install
MiniVault from scratch. Please make sure you have all prerequisites for a
<a href="https://laravel.com/docs/12.x/deployment">Laravel</a> web app to run, and set up your application accordingly.
You should then copy `.env.example` to `.env` and modify accordingly, making sure to do it according to your
setup.

Once you have your application set up, run the migrations using `php artisan migrate:fresh --seed`. The default user
will be `admin@example.com` with `password` as the password. You can change these on the admin panel (see below).

Alternatively, and **recommended**, you can use Docker to install it by simply copying the following to
`docker-compose.yml` in an empty folder:

```YML
services:
  miniv:
    image: mediab/miniv:latest
    restart: unless-stopped
    ports:
      - 9500:8000
    volumes:
        - ./data:/var/www/data
    environment:
      APP_URL: 'https://miniv.myawesomelab.local' # This will be your url for your new password manager. Don't use vault.* as browsers tend to block it for security
      ADMIN_EMAIL: 'admin@example.com' # Your admin email
      ADMIN_PASSWORD: 'password' # Your admin password | recommended to change after
      USER_EMAIL: 'user@example.com' # Your first user email. If not supplied, it will not create this user
      USER_PASSWORD: 'password' # Your initial password. You will be asked to change it on first login
```

#### After installation

Once your app is installed and running, log in using the credentials you set up for admin.
<small>Default is `admin@example.com` : `password`</small>

In the admin panel, you should change the password of the admin user by editing it. You can then create a `non-admin`
user by clicking `New User`. The password you select will be a temporary one, as the user will be asked to change the
password again on first login.

You can also set up any OAuth2.0 clients in the admin panel, providing a single authentication system for your home lab.

## Issues

There are some known issues to be solved; you can follow them
<a href="https://github.com/MediaBitLtd/mini_vault/issues">here</a>. Please report any you find — any contribution is
much appreciated 😊
