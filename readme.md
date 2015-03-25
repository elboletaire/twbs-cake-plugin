Bootstrap plugin for CakePHP 3.X
================================

[![Latest Stable Version](https://poser.pugx.org/elboletaire/twbs-cake-plugin/v/stable.svg)](https://packagist.org/packages/elboletaire/twbs-cake-plugin) [![Total Downloads](https://poser.pugx.org/elboletaire/twbs-cake-plugin/downloads.svg)](https://packagist.org/packages/elboletaire/twbs-cake-plugin) [![Latest Unstable Version](https://poser.pugx.org/elboletaire/twbs-cake-plugin/v/unstable.svg)](https://packagist.org/packages/elboletaire/twbs-cake-plugin) [![License](https://poser.pugx.org/elboletaire/twbs-cake-plugin/license.svg)](https://packagist.org/packages/elboletaire/twbs-cake-plugin)

This plugin includes both
[lessjs](http://lesscss.org/#client-side-usage-browser-options) and
[less.php](https://github.com/oyejorge/less.php#lessphp) parsers and allows
you to easilly deploy CakePHP applications with (Twitter) Bootstrap.

With a component and some helpers it automatically replaces cakePHP's elements
like form inputs and flash messages to be displayed with twitter bootstrap.

It also contains bake templates that will help you starting *twitter-bootstraped*
CakePHP webapps.

General Features
----------------

- Parses less files using less.js or less.php.
- LessHelper to easily parse files.
- FormHelper to automatically style forms.
- FlashComponent to replace alerts.
- Bake templates.

Installation
------------

### Adding the plugin

You can easily install this plugin using composer as follows:

```bash
composer require elboletaire/twbs-cake-plugin
```

After doing it, composer will ask you for a version. Checkout the
[package on packagist](https://packagist.org/packages/elboletaire/twbs-cake-plugin)
to know every available version.

Latest version currently is `3.0.0-rc1`, but you can use `dev-master` to use
the latest `master HEAD` version.

### Enabling the plugin

After adding the plugin remember to load it in your `config/bootstrap.php` file:

```php
Plugin::load('Bootstrap', ['bootstrap' => true]);
```

If, for any reason, the `psr-4` addition to the `composer.json` file does not
work, try setting `autoload` to `true` when loading the plugin:

```php
Plugin::load('Bootstrap', ['autoload' => true, 'bootstrap' => true]);
```

### Configuration

After adding the plugin you can add the desired utilities:

```php
// AppController.php
public $helpers = [
    'Less.Less', // required for parsing less files
    'Bootstrap.Form'
];

public function initialize() {
    $this->loadComponent('Bootstrap.Flash');
}
```

Usage
-----

There are two common usage ways when using twitter bootstrap and less:

- Directly using twitter bootstrap classes on your views.
- Using custom classes on your views and then extending that classes to twitter
  bootstrap components.

For the first case you can directly [load the layout included](#themes) with
this plugin and [bake your views](#baking-views) with the also included bake
templates.

For the second case you'll need to
[create your own layout](#creating-your-own-layout) and load the included
`webroot/less/cakephp/styles.less` file. It will extend the default baked views'
styles so they have a twitter bootstrap look and feel.

### Themes

On both cases you can use the layout included with this plugin as a theme
(right now there's only the `default` layout):

```php
// AppController
public $theme = 'Bootstrap';

// or...
public $layout = 'Bootstrap.default';
```

You can also specify it as a layout directly from your template files:

```php
// any .ctp Template file
$this->layout = 'Bootstrap.default';
```

Last but not least, you can also copy that template to your `Template/Layout`
folder and then extend the template from your view.

[Read more about views on the CakePHP Cookbook](http://book.cakephp.org/3.0/en/views.html)

> BTW it's recommended that you copy all the required files to your src folder
(specially for assets), even if you won't modify them.

Take in mind that if you're loading this plugin in a fresh CakePHP installation
and you try to see the layout change in the home page, you won't see nothing.
The `home.ctp` overwrites the layout to `false`, to ensure it's loaded as it has
been designed.

### Baking views

You can bake your views using the twitter bootstrap templates bundled with this
plugin. To do so, simply specify the `bootstrap` template when baking your files:

```bash
cake bake all articles --theme Bootstrap
```

### Creating your own layout

Create a `styles.less` file on your `webroot/less` folder (also create
that folder if it does not exist) containing this line:

```less
@import '../bootstrap/less/bootstrap.less';
```

Finally, load the less file from your view or layout:

```php
echo $this->Less->less('less/styles.less');
```

If you want to extend twitter bootstrap styles I recommend you to copy both
`bootstrap.less` and `variables.less` files to your `less` folder and customize
them to your needs.

If you'd like to see an example of this you can check the files included in
`webroot/less/cakephp` specially made to extend the default CakePHP baked
templates.

Utilities
---------

### FlashComponent

The **FlashComponent** replaces all flash messages set with `$this->Flash` to
automatically load the bootstrap flash message template located at
`src/Template/Element/flash.ctp`.

By default the flash messages will show a close button. If you want to disable
just specify it as param when setting the flash message:

```php
$this->Flash->danger('Fatal error', ['params' => ['close' => false]]);
```

### LessHelper

Used on your template or view to parse and load the compressed CSS.

The LessHelper is part of the
[less cakephp plugin](https://github.com/elboletaire/less-cakephp). Check out
all its details there.

### FormHelper

Automatically adds some CSS classes to your form elements. Some of the input
replacements can be found at `src/Config/forms.php`, but many need to be done
directly from the FormHelper.

All the form elements' classes have been replaced with the twitter bootstrap
form classes.

> \* Buttons always have the `.btn` class added. If you want to remove the class
you can pass an additional `btnClass` param set to `false` to the button's
`$options`.

Dependencies
------------

- [elboletaire/less-cake-plugin](https://github.com/elboletaire/less-cake-plugin) version >= 1.6.1

### Included dependencies

- [twbs/bootstrap](https://github.com/twbs/bootstrap):
  [version 3.3.4 (rev. 2f3076f2)](https://github.com/twbs/bootstrap/tree/2f3076f20acb6b34279b1ef77063a8fff33f756e)

About versioning
----------------

This project started using the same versioning as CakePHP 3.X during its
development stage. For this reason, I'll continue using this but reserving the
latest version number for my versions.

This means that any version of this plugin with `3.0.X` version number should be
compatible with any CakePHP `3.0` version.

License
-------

    The MIT License (MIT)

    Copyright 2013-2015 Òscar Casajuana (a.k.a. elboletaire)

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.

