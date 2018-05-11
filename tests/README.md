Testing
=========

Mellivora is tested using [Codeception](http://codeception.com/). Builds on [TravisCI](https://travis-ci.org/Nakiami/mellivora) is used to verify before merging.

### Requirements

* [Docker](https://docs.docker.com/) and [docker-compose](https://docs.docker.com/compose/)

### Running tests locally

Running ``./tests/run_tests`` should do the trick.

If you are making changes to composer requirements, you will need to delete/rebuild the docker image ``composerdependencies``.