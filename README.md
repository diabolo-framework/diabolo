# Diabolo
core of the framework ~

## Document
online doc : https://diabolo.readthedocs.io

document is wrote by [Sphinx](http://www.sphinx-doc.org/en/master/), and now, we only support html doc in make file,

each module or service has an folder named `Document` to contains documents, 
and then setup doc source by Sphinx in this folder :

```
$ sphinx-quickstart
````

after that, we got `index.rst` and we need to fix the title without any welcome message, cause it would 
become the entry's name when we build the full doc of this framework.

document can be generate by make command, and here it is :

```
$ make doc
```
