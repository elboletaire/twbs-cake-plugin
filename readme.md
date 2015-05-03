Bootstrap plugin for CakePHP 3.X
================================

[![Latest Stable Version](https://poser.pugx.org/elboletaire/twbs-cake-plugin/v/stable.svg)](https://packagist.org/packages/elboletaire/twbs-cake-plugin) [![Total Downloads](https://poser.pugx.org/elboletaire/twbs-cake-plugin/downloads.svg)](https://packagist.org/packages/elboletaire/twbs-cake-plugin) [![Latest Unstable Version](https://poser.pugx.org/elboletaire/twbs-cake-plugin/v/unstable.svg)](https://packagist.org/packages/elboletaire/twbs-cake-plugin) [![License](https://poser.pugx.org/elboletaire/twbs-cake-plugin/license.svg)](https://packagist.org/packages/elboletaire/twbs-cake-plugin)

This plugin includes both
[lessjs](http://lesscss.org/#client-side-usage-browser-options) and
[less.php](https://github.com/oyejorge/less.php#lessphp) parsers and allows
you to easilly deploy CakePHP applications with (Twitter) Bootstrap.

Since version 3.0.2 this plugin dropped its own helpers and components and added
[friendsofcake/bootstrap-ui](https://github.com/FriendsOfCake/bootstrap-ui)
as a composer requirement, so you will use all their classes instead.

It also contains bake templates that will help you starting *twitter-bootstraped*
CakePHP webapps.

General Features
----------------

- Parses less files using less.js and/or less.php.
- LessHelper to easily parse files.
- Bake templates.
- Generic Bootstrap layout.
- All the included utilities from BootstrapUI plugin.

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

This will load the Less and BootstrapUI plugins for you.

If you preffer to do this manually, you can load them one by one:

```php
Plugin::load('Bootstrap');
Plugin::load('Less');
Plugin::load('BootstrapUI');
```

### Configuration

After adding the plugin you can add the desired utilities:

```php
// AppController.php
public $helpers = [
    'Less.Less', // required for parsing less files
    'BootstrapUI.Form',
    'BootstrapUI.Html',
    'BootstrapUI.Flash',
    'BootstrapUI.Paginator'
];
```

Or, if loading them in the AppView:

```php
// AppView.php
public function initialize()
{
    $this->loadHelper('Less', ['className' => 'Less.Less']);
    $this->loadHelper('Html', ['className' => 'BootstrapUI.Html']);
    $this->loadHelper('Form', ['className' => 'BootstrapUI.Form']);
    $this->loadHelper('Flash', ['className' => 'BootstrapUI.Flash']);
    $this->loadHelper('Paginator', ['className' => 'BootstrapUI.Paginator']);
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
[create your own layout](#creating-your-own-layout) and create a stylesheet like
the included one
`webroot/less/cakephp/styles.less`.

This file extends the default baked views'
styles so they have a *CakePHP-Bootstrapped* look and feel.

### Themes

On both cases you can use the layout included with this plugin as a theme
(right now there's only the `default` layout):

```php
// AppController or AppView
public $theme = 'Bootstrap';

// or...
public $layout = 'Bootstrap.default';
```

You can also specify it as a layout directly from your template files:

```php
// any .ctp Template file
$this->layout = 'Bootstrap.default';
```

> You should use the Bootstrap layout if you wanna use `less` files. If you rather
preffer using css files you may use the
[BootstrapUI](https://github.com/FriendsOfCake/bootstrap-ui/tree/master/src/Template/Layout)
layouts.

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
cake bake.bake [subcommand] --theme Bootstrap
```

Remember that you can also bake your views using
[BootstrapUI's bake templates](https://github.com/FriendsOfCake/bootstrap-ui/tree/master/src/Template/Bake).
Take a look to its readme for more details.

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

If you want to extend twitter bootstrap styles I recommend you to copy the
`bootstrap.less` file to your `less` folder and customize
it to your needs. For the `variables.less` create a `custom-variables.less` and
load it just after `variables.less` in `bootstrap.less` file. Any variable
defined in that file will overwrite the value defined in `variables.less` and
your code won't break when updating (Twitter) Bootstrap.

If you'd like to see an example of this you can check the files included in
`webroot/less/cakephp` specially made to extend the default CakePHP baked
templates.

Utilities
---------

This plugin "includes" the following utilities (all they come from other plugins):

- [Less](https://github.com/elboletaire/less-cake-plugin) [LessHelper](https://github.com/elboletaire/less-cake-plugin#usage)
- [BootstrapUI](https://github.com/FriendsOfCake/bootstrap-ui) [FormHelper](https://github.com/FriendsOfCake/bootstrap-ui#basic-form)
- [BootstrapUI](https://github.com/FriendsOfCake/bootstrap-ui) [HtmlHelper](https://github.com/FriendsOfCake/bootstrap-ui/blob/master/src/View/Helper/HtmlHelper.php)
- [BootstrapUI](https://github.com/FriendsOfCake/bootstrap-ui) [FlashHelper](https://github.com/FriendsOfCake/bootstrap-ui/blob/master/src/View/Helper/FlashHelper.php)
- [BootstrapUI](https://github.com/FriendsOfCake/bootstrap-ui) [PaginatorHelper](https://github.com/FriendsOfCake/bootstrap-ui/blob/master/src/View/Helper/PaginatorHelper.php)

### A note about Bootstrap's FlashComponent

The old Bootstrap FlashComponent used to have a `close` option that allowed you
to define whether the flash alert would have a close button or not.

With BootstrapUI FlashHelper this works different. It looks for an
`alert-dismissible` class (which is set by default) and, if defined, will show
the close button.

For disabling the close button for the current Flash alert you can do:

```php
$this->Flash->{whatever}("Hello World", ['params' => ['class' => ['alert']]]);

// where {whatever} is any of the Bootstrap alert classes (danger, info, warning...)
$this->Flash->success("Hello World", ['params' => ['class' => ['alert']]]);
```

> Note that the `class` param is defined as an array.

### LessHelper

Used on your template or view to parse and load the compressed CSS.

The LessHelper is part of the
[less cakephp plugin](https://github.com/elboletaire/less-cakephp). Check out
all its details there.

Dependencies
------------

- [elboletaire/less-cake-plugin](https://github.com/elboletaire/less-cake-plugin) version >= 1.6.1
- [FriendsOfCake/bootstrap-ui](https://github.com/FriendsOfCake/bootstrap-ui) version ~0.3

### Included dependencies

- [twbs/bootstrap](https://github.com/twbs/bootstrap):
  [version 3.3.4 (rev. 7b9f204c)](https://github.com/twbs/bootstrap/tree/7b9f204cb4b8fa5cb06b2a9233324997c093f629)

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

