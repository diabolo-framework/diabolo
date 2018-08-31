# we use makefile to management doc and tests, here is the directory
# struct :
# /
# |- Framework : the framework work path
# |- Document : generated html document 
# |- ClassReference : genereate html class document
# |- Test : all test cases

.PHONY: all doc public-doc class-ref public-class-ref test install uninstall \
        clean-doc

all: doc public-doc class-ref public-class-ref test

doc:
	@echo "making documents ..."
	mkdir -p Document/source
	mkdir -p Document/build
	mkdir -p Document/source/_static
	echo "Welcome to Diabolo's documentation!" >> Document/source/index.rst
	echo "===================================" >> Document/source/index.rst
	echo "" >> Document/source/index.rst
	echo ".. toctree::" >> Document/source/index.rst
	echo "" >> Document/source/index.rst
	@for SERVICE_NAME in `ls Framework/Service`; \
	do \
		SERVICE_DOC_DIR="Framework/Service/$$SERVICE_NAME/Document/source"; \
		if [ -d $$SERVICE_DOC_DIR ]; then \
			ROOT_SERVICE_DOC_DIR="Document/source/$$SERVICE_NAME"; \
			mkdir -p $$ROOT_SERVICE_DOC_DIR ;\
			cp -r $$SERVICE_DOC_DIR/* $$ROOT_SERVICE_DOC_DIR ; \
			echo "  $$SERVICE_NAME/index.rst" >> Document/source/index.rst ; \
			echo "copied service $$SERVICE_NAME"; \
		fi; \
	done
	cp doc-conf.py Document/source/conf.py
	sphinx-build -M html "Document/source" "Document/build"

public-doc:
class-ref:
public-class-ref:
test:
install:
uninstall:
clean: clean-doc

clean-doc:
	@echo "clean documents ..."
	rm -fr Document/
