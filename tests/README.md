Testing
=========

Mellivora is tested using [Codeception](http://codeception.com/). Builds on [TravisCI](https://travis-ci.org/Nakiami/mellivora) are used to verify before merging.

### Requirements

* [Docker](https://docs.docker.com/) and [docker-compose](https://docs.docker.com/compose/)

### Running tests locally

Running ``./tests/run_tests`` should do the trick.

If you are making changes to composer requirements, you will need to delete/rebuild the docker image ``composerdependencies``.
You can do this by calling ``docker-compose -f docker-compose.test.yml build``.

### Tips

- Call ``docker-compose -f docker-compose.test.yml build`` to rebuild all containers.
- HTML output and screenshots of failing acceptance tests can be found in ``tests/codeception/_output/``.
- Your local settings in ``include/config/config.inc.php`` are used when running tests. Some settings will cause tests to fail, like enabling caching, captcha, etc. It is recommended to remove any non-default configuration while running acceptance tests locally.
