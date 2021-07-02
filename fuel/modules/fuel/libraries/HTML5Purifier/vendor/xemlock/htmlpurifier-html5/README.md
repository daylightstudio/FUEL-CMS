# HTML5 Definitions for HTML Purifier

![Maintenance](https://img.shields.io/maintenance/yes/2019.svg)
[![Build Status](https://travis-ci.org/xemlock/htmlpurifier-html5.svg?branch=master)](https://travis-ci.org/xemlock/htmlpurifier-html5)
[![Coverage Status](https://coveralls.io/repos/github/xemlock/htmlpurifier-html5/badge.svg?branch=master)](https://coveralls.io/github/xemlock/htmlpurifier-html5?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/xemlock/htmlpurifier-html5.svg)](https://packagist.org/packages/xemlock/htmlpurifier-html5)
[![Total Downloads](https://img.shields.io/packagist/dt/xemlock/htmlpurifier-html5.svg)](https://packagist.org/packages/xemlock/htmlpurifier-html5/stats)
[![License](https://img.shields.io/packagist/l/xemlock/htmlpurifier-html5.svg)](https://packagist.org/packages/xemlock/htmlpurifier-html5)

This library provides HTML5 element definitions for [HTML Purifier](https://github.com/ezyang/htmlpurifier).

It is the most complete HTML5-compliant solution among all based on HTML Purifier. Apart from providing the most extensive set of element definitions, it provides tidy/sanitization rules for transforming the input into a valid HTML5 output.


## Installation

Install with [Composer](https://getcomposer.org/) by running the following command:

```
composer require xemlock/htmlpurifier-html5
```


## Usage

The most basic usage is similar to the original HTML Purifier. Create a HTML5-compatible config
using `HTMLPurifier_HTML5Config::createDefault()` factory method, and then pass it to an `HTMLPurifier` instance:

```php
$config = HTMLPurifier_HTML5Config::createDefault();
$purifier = new HTMLPurifier($config);
$clean_html5 = $purifier->purify($dirty_html5);
```

To modify the config you can either instantiate the config with a configuration array passed to
`HTMLPurifier_HTML5Config::create()`, or by calling `set` method on an already existing config instance.

For example, to allow `IFRAME`s with Youtube videos you can do the following:

```php
$config = HTMLPurifier_HTML5Config::create(array(
  'HTML.SafeIframe' => true,
  'URI.SafeIframeRegexp' => '%^//www\.youtube\.com/embed/%',
));
```

or equivalently:

```php
$config = HTMLPurifier_HTML5Config::createDefault();
$config->set('HTML.SafeIframe', true);
$config->set('URI.SafeIframeRegexp', '%^//www\.youtube\.com/embed/%');
```

## Configuration

Apart from HTML Purifier's built-in [configuration directives](http://htmlpurifier.org/live/configdoc/plain.html), the following new directives are also supported:

* __HTML.IframeAllowFullscreen__

  Version added: 0.1.11\
  Type: [Boolean](http://htmlpurifier.org/live/configdoc/plain.html#type-bool)\
  Default: `false`

  Whether or not to permit `allowfullscreen` attribute on `iframe` tags. It requires either [%HTML.SafeIframe](http://htmlpurifier.org/live/configdoc/plain.html#HTML.SafeIframe) or [%HTML.Trusted](http://htmlpurifier.org/live/configdoc/plain.html#HTML.Trusted) to be `true`.


## Supported HTML5 elements

Aside from HTML elements supported originally by HTML Purifier, this library
adds support for the following HTML5 elements:

`<article>`, `<aside>`, `<audio>`, `<bdi>`, `<details>`, `<dialog>`, `<figcaption>`, `<figure>`, `<footer>`, `<header>`, `<hgroup>`, `<main>`, `<mark>`, `<nav>`, `<picture>`, `<progress>`, `<section>`, `<source>`, `<summary>`, `<time>`, `<track>`, `<video>`, `<wbr>`

as well as HTML5 attributes added to existing HTML elements, such as:

`<a>`, `<del>`, `<fieldset>`, `<ins>`, `<script>`


## License

The MIT License (MIT). See the LICENSE file.
