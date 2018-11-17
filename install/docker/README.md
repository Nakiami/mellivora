Mellivora and Docker
=========

Mellivora is easy to use with docker-compose.
Mellivora comes with an included docker-compose configuration intended for development use.
If you're looking to run Mellivora using Docker(-compose) in production, a good place to start might be to copy the provided docker-compose config and changing it to suit your needs.

### Preliminary

This assumes you have [Docker](https://docs.docker.com/) and [docker-compose](https://docs.docker.com/compose/) installed.

### Run Mellivora

Run

```
docker-compose -f docker-compose.dev.yml up
```

to start with dev mode settings.

Run

```
sudo chown -R www-data:www-data writable/
```

in the Mellivora home directory to give Apache the permissions necessary for challenge file upload and caching.

#### Create an admin user

- Visit [http://localhost/](http://localhost/) which should now display the Mellivora landing page.
- Register a new user. You will probably get an error about emails not working. The user is created and functional despite the error.
- Go to [http://localhost:18080](http://localhost:18080) where Adminer should be running (assuming you're running in dev mode). Log in with 
```
Server: db
Username: root
Password: password
Database: mellivora
```

- To make your user an administrator, go to "SQL command" in the menu and run

```sh
UPDATE users SET class = 100 WHERE id = 1;
```

- Log in at [http://localhost/](http://localhost/). Done!

### Tips

- The ``dev`` docker-compose profile mounts to use files directly from the host. Making changes to files on disk will result in changes to the running instance without rebuilding the container.
- If you are making changes to composer requirements, you will need to delete/rebuild the docker image ``composerdependencies``.
- Call ``docker-compose -f docker-compose.dev.yml up --build`` to rebuild and start.
- Copy ``include/config/config.default.inc.php`` to ``include/config/config.inc.php`` to make your configuration changes.