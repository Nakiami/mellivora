Mellivora and Docker
=========

Mellivora is easy to use with docker-compose.

### Preliminary

This assumes you have [Docker](https://docs.docker.com/) and [docker-compose](https://docs.docker.com/compose/) installed.

### Running Mellivora

Run

``./bin/run_docker_dev``, which is the same as running ``docker-compose -f docker-compose.yml -f docker-compose.dev.override.yml up``

to start with dev mode settings (recommended).

Or
 * ``docker-compose up`` to start, or
 * ``docker-compose up --build`` to rebuild and start

#### Creating an admin user

- Visit [http://localhost/](http://localhost/) which should now display the Mellivora landing page.
- Register a new user. You will probably get an error about emails not working. The user is created and functional despite the error.

Go to [http://localhost:18080](http://localhost:18080) where Adminer should be running (assuming you're running in dev mode).

Log in with 
```
Server: db
Username: meldbuser
Password: password
Database: mellivora
```

Go to "SQL command" in the menu and run

```sh
UPDATE users SET class = 100 WHERE id = 1;
```

Log in. Done!