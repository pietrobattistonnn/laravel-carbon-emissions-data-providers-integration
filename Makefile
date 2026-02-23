# =========================================
# Ceedbox Emissions Ecosystem Makefile
# =========================================

PHPUNIT=vendor/bin/phpunit

.PHONY: help install install-core install-lune test test-core test-lune \
        analyse clean validate

.PHONY: dev-quick-test


help:
	@echo ""
	@echo "Available commands:"
	@echo "  make dev-quick-test Run a quick local test without Laravel"
	
	@echo "  make install        Install all package dependencies"
	@echo "  make test           Run all tests"
	@echo "  make test-core      Run emissions-core tests"
	@echo "  make test-lune      Run lune-module tests"
	@echo "  make analyse        Run static analysis (if installed)"
	@echo "  make validate       Validate composer files"
	@echo "  make clean          Remove vendor directories"
	@echo ""

dev-quick-test:
	cd packages/dev-runner && composer install
	cd packages/dev-runner && php bin/lune generate

install:
	cd packages/emissions-core && composer install
	cd packages/lune-module && composer install

install-core:
	cd packages/emissions-core && composer install

install-lune:
	cd packages/lune-module && composer install

test:
	$(MAKE) test-core
	$(MAKE) test-lune

test-core:
	cd packages/emissions-core && $(PHPUNIT)

test-lune:
	cd packages/lune-module && $(PHPUNIT)

analyse:
	cd packages/emissions-core && vendor/bin/phpstan analyse || true
	cd packages/lune-module && vendor/bin/phpstan analyse || true

validate:
	cd packages/emissions-core && composer validate
	cd packages/lune-module && composer validate

clean:
	rm -rf packages/emissions-core/vendor
	rm -rf packages/lune-module/vendor