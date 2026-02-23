# =========================================
# Emissions Integrations Makefile
# =========================================

PHPUNIT=vendor/bin/phpunit

.PHONY: help install install-core install-lune install-dev \
        test test-core test-lune \
        analyse validate clean \
        dev-run dev-jwt

help:
	@echo ""
	@echo "Available commands:"
	@echo ""
	@echo "  make dev-run        Run interactive Lune generator (no Laravel required)"
	@echo "  make dev-jwt        Debug JWT (TOKEN required)"
	@echo ""
	@echo "  make install        Install all package dependencies"
	@echo "  make test           Run all tests"
	@echo "  make test-core      Run emissions-core tests"
	@echo "  make test-lune      Run lune-module tests"
	@echo "  make analyse        Run static analysis (if installed)"
	@echo "  make validate       Validate composer files"
	@echo "  make clean          Remove all vendor directories"
	@echo ""

# -----------------------------------------
# Dev Runner
# -----------------------------------------

dev-run:
	cd packages/dev-runner && composer install
	cd packages/dev-runner && php bin/lune generate

dev-jwt:
	cd packages/dev-runner && composer install
	cd packages/dev-runner && php bin/lune debug:jwt "$(TOKEN)"

# -----------------------------------------
# Install
# -----------------------------------------

install:
	$(MAKE) install-core
	$(MAKE) install-lune
	$(MAKE) install-dev

install-core:
	cd packages/emissions-core && composer install

install-lune:
	cd packages/lune-module && composer install

install-dev:
	cd packages/dev-runner && composer install

# -----------------------------------------
# Tests
# -----------------------------------------

test:
	$(MAKE) test-core
	$(MAKE) test-lune

test-core:
	cd packages/emissions-core && $(PHPUNIT)

test-lune:
	cd packages/lune-module && $(PHPUNIT)

# -----------------------------------------
# Quality
# -----------------------------------------

analyse:
	cd packages/emissions-core && vendor/bin/phpstan analyse || true
	cd packages/lune-module && vendor/bin/phpstan analyse || true

validate:
	cd packages/emissions-core && composer validate
	cd packages/lune-module && composer validate

# -----------------------------------------
# Cleanup
# -----------------------------------------

clean:
	rm -rf packages/emissions-core/vendor
	rm -rf packages/lune-module/vendor
	rm -rf packages/dev-runner/vendor