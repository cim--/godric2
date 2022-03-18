#!/bin/bash -ex
npm install
composer install
npm run dev

rm -f godric.dist.tgz
tar --anchored \
            --exclude=node_modules \
            --exclude=phpcs \
            --exclude=prototypes \
            --exclude=resources/assets \
            --exclude=tests \
            --exclude=.cache \
            --exclude=.editorconfig \
            --exclude=.env \
            --exclude=.env.example \
            --exclude=.git \
            --exclude=.gitattributes \
            --exclude=.gitignore \
	    --exclude=*~ \
#	    --exclude=storage/debugbar \
#	    --exclude=storage/framework/views/* \
#	    --exclude=storage/framework/sessions/* \
	    --exclude=storage \
            --exclude=build.sh \
            --exclude=gulpfile.js \
            --exclude=package.json \
            --exclude=phpspec.yml \
	    --exclude=database\database.sqlite \
            --exclude=phpunit.xml \
            --exclude=readme.md \
            -czf godric.dist.tgz -- *
