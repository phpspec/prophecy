language: php

php: [5.3, 5.4, 5.5, 5.6, 7, hhvm]

sudo: false

cache:
  directories:
    - $HOME/.composer/cache

branches:
  except:
    - /^bugfix\/.*$/
    - /^feature\/.*$/
    - /^optimization\/.*$/

before_script: travis_retry composer install --no-interaction

script: vendor/bin/phpspec run -fpretty -v
