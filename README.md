# Diabolo
core of the framework ~

## Installation

Diabolo has been published on [Packagist](https://packagist.org/packages/diabolo/diabolo-framework), so you can install it by composer command. 
```
composer require diabolo/diabolo-framework
```

Also you are albe to clone the source code from github, and here is the command 
```
git clone git@github.com:diabolo-framework/diabolo.git
```

If you don't have the git or composer, you can download the source manually, here is the link for the downloading.
https://github.com/diabolo-framework/diabolo/releases

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
