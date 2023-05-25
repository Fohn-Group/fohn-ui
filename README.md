# Fohn-ui
 PHP Framework using Tailwind Css.

[Demo site](https://fohn-ui.com)

[Join Discord Channel](https://discord.gg/VwrPA8Nb3t)

## History

Fohn-ui is a direct evolution of Agile Toolkit (atk4/ui), thus you will find a lot 
of similarity with atk4/ui when using Fohn-ui. That being said, Fohn-ui cannot be used
as a direct upgrade to your atk4/ui project.

Here are the main differences:

- Fohn-ui Views are decoupled from Data model. Some views, like Form or Table, will consume model data via ModelController class.
- Fohn-ui used its own javascript integration.
- Fohn-ui used Tailwind Css utilities in order to define Views look and feel.
- Fohn-ui Components are defined in a minimalistic way.

For example, you will not find a Crud component in Fohn-ui.
In order to build a Crud, you need to define your own Crud class using the Table component. This is a little
more work at first but allow greater flexibility in customization and better portability in future release of Fohn-ui.

## Installation

To create a project using Fohn-ui simply install it using Composer.

```
composer require fohn-group/fohn-ui
```

### Setting up Ui::service

Ui::service class is the heart of Fohn-ui and therefore require to be setup properly in order to display page content.

Here is a minimal setup:

```
<?php

declare(strict_types=1);

// Create and boot service.
Ui::service()->boot(function (Ui $ui) {
    $ui->setApp(new App());
    
    // Add default exception handler.
    $ui->setExceptionHandler(PageException::factory());
    
    // Set page.
    $page = Page::factory(['title' => 'My Fohn-ui Project']);
    $page->addLayout(SideNavigation::factory(['topBarTitle' => 'My Fohn-Ui App']));
    $ui->initAppPage($page);
});

View::addTo(Ui::layout())->setText('Hello World');
```

#### Why Ui::service()

Mainly View rely on Ui::service() to properly setup Html engine, javascript library and other utilities used
by View class. 

For example, Ui::service() will supply Theme class used by View. 
```
Ui::theme()::styleAs()
```

Therefore, you could customize pretty much everything used externally by `View::class` using your own implementation
by overriding `Ui::class` or simply setup proper property at boot time.

```
Ui::service()->boot(function (Ui $ui) {
    // ...code
    $ui->formLayoutSeed = [MyFormLayout::class]
});
```


## Running app-test using Docker

The app-test folder is set up as a Fohn-ui project within Fohn-ui. 
It is mainly use for testing various Fohn-ui Views and components.

You can easily run the app-test project if you have Docker install on your system by cloning 
this repository and build the Dockerfile.

```
git clone https://github.com/Fohn-Group/fohn-ui
cd fohn-ui/app-test
docker build -f Dockerfile .. -t fohn-ui-test
docker run --rm -p 80:80 -it fohn-ui-test
```
Once done, open your browser at: http://localhost/app-test

## Tailwind Css

Setting the look and feel of Views in Fohn-ui is done easily by using Tailwind Css utilities.

### Theme-able with PHP
Furthermore, because Tailwind Css is using utilities rather than css class name it has become possible
to create themes and apply a theme style to any View in Fohn-ui.

Button View are a good example:

```
 $btn = Button::addTo(Ui::layout(), ['type' => 'outline', 'color' => 'secondary']);
 Ui::theme()::styleAs(Fohn::BUTTON, [$btn]);
```

This will render in html a button with proper Tailwind Css utility set in Theme class used making it easy
to define your own utility color and style.

<img src="https://github.com/Fohn-Group/fohn-ui/blob/dev-develop/public/images/secondary-btn.png?raw=true" width="128">

## Javascript and Jquery

It is possible to apply Javascript or Jquery event on any Fohn-ui View. 

Example toggling a view display by clicking on a button using Jquery.

```
$view = View::addTo(Ui::layout())->setText('I will show/hide if you click button below');
$btn = Button::addTo(Ui::layout())->setLabel('Toggle View');

Jquery::addEventTo($btn, 'click')->execute(JQuery::withView($view)->toggle());
```

## Vue.js component

Some View in Fohn-ui are define using Vue js frameworks. To name a few, Form, Table, Modal use Vue integration in 
order to define their behaviors. But component define in Vue usually are renderless component, i.e. their template 
is defined by Fohn-ui html template engine. This allows developer great flexibility for controlling the look 
of the component itself but also its behaviour.

### Note when developing your own Vue component

Developing a component which use other components can be challenging because html template are split accross 
the number of components in uses. For example, Form use Form/Control component in order to render the final
html template.

Therefore, Fohn-ui provided a utility that will render the final html for the entire component before Vue.js 
render it to the DOM: `Ui::viewDump($form, 'form')`.

```
// pages/form.php

$form = Form::addTo(Ui::layout());
$form->addControl(new Form\Control\Range(['caption' => 'Range', 'controlName' => 'range']));

Ui::viewDump($form, 'form');
```

In order to display how $form is render in html prior to be rendered by Vue, simply append query param dump=form to the page url.

`http://localhost/form.php?dump=form`

## Html Template

Each Fohn-ui View class is associate with a html template. Each View is render in html and their html output are render
within their parent View template. In other word, when adding a View to a View, Fohn-ui is generating html content within
the parent html content.

This means that template are defined using Tag region in order to properly output rendering content.

Example of a view template using Tag region:
```
<div id="{$idAttr}" class="{$classAttr}" style="{$styleAttr}" {$attributes}>
    {$Content}
<div>
```

Template engine also use specific annotation in order to prevent using brace symbol as region tag.
When using this specific annotation, the template engine will render the content as is.

For example, use '@' or double brace '{{}}' in order to render brace content for Vue component.
```
<fohn-component #default=@{props}>
    <div>{{props}}</div>
</fohn-component>
```
Template engine will render it as below prior to be rendered by Vue js.
```
<fohn-component #default={props}>
    <div>{{props}}</div>
</fohn-component>
```
