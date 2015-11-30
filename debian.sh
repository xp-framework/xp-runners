#!/bin/sh

TRAVIS_TAG="v5.6.2"

sudo apt-get install -qq checkinstall

RELEASENUMBER=${TRAVIS_TAG:1}

sudo checkinstall \
  --pakdir=build/bin \
  --type=debian \
  --pkgname=xp-runners \
  --pkgversion=$RELEASENUMBER \
  --pkggroup=devel \
  --pkglicense=BSD \
  --pkgarch all -y \
  --requires=php5-cli,realpath \
  --backup=no \
  --install=no \
  --nodoc \
  --reset-uids=yes \
  make unix.install

PACKAGE="{
  \"package\": {
    \"name\": \"xp-runners\",
    \"repo\": \"deb\",
    \"subject\": \"mikey179\"
  },
  \"version\": {
    \"name\": \"$RELEASENUMBER\",
    \"desc\": \"XP Runners release $TRAVIS_TAG\",
    \"released\": \"$DATE\",
    \"vcs_tag\": \"$TRAVIS_TAG\",
    \"gpgSign\": true
  },"
FILES="\"files\": [
      {
        \"includePattern\": \"build/bin/(.*\.deb)\", \"uploadPattern\": \"\$1\",
        \"matrixParams\": {
            \"deb_distribution\": \"vivid\",
            \"deb_component\": \"main\",
            \"deb_architecture\": \"i386,amd64\"
        }
     }
   ],
   \"publish\": false"

BINTRAY_CONFIG=$PACKAGE$FILES"}"

echo Generated Bintray config:
echo $BINTRAY_CONFIG

echo $BINTRAY_CONFIG >> bintray.config
