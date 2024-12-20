PHPQA_TAG = php8.2
PHP_CS_FIXER_CONFIGURATION_FILE ?= ./lint/.php-cs-fixer.php

docker := $(shell which docker)
qa := $(docker) run --rm -t -v `pwd`:/project --workdir="/project" jakzal/phpqa:$(PHPQA_TAG)
phpcsfixer := $(qa) php-cs-fixer

fixcs:
	@$(phpcsfixer) fix --config=$(PHP_CS_FIXER_CONFIGURATION_FILE)
