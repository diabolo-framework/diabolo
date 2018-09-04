Web Action
==========
web action use to generate html page content or doing url jump stuff.

Classic Directory
-----------------
here is an common usage of action, but not only : ::

    Module
    | - Action
    |   | - Action001.php
    |   ` - Action002.php
    |     - Another
    |       | - Action003.php
    |       ` - DemoAction.php
    ` - View
        | - Layout
        |   | - Layout001.php
        |   ` - Layout002.php
        ` - Particle
            | - Particle001.php
            ` - Another
                | - Particle002.php
                | - DemoParticle.php
                ` - Particle003.php

then you wil get following actions :

- action001
- action002 
- another/action003
- another/demo-action

the layout name will be : 

- Layout001
- Layout002

and the particle name will be : 

- Particle001
- Another/Particle002
- Another/DemoParticle
- Another/Particle003

Action Defination
-----------------
example : ::

    <?php
    namespace X\Module\Demo\Action;
    use X\Service\Action\Handler\WebAction;
    class MyWeb extends WebAction {
        /** @var string */
        protected $layout = 'Layout001';
        /** @var string */
        protected $title = 'TESTING';
        
        protected function run( ) {
            $webView = $this->getView();
            
            # add data to view
            $this->addDataToView('globalKey001', 'globalKey001');
            
            # add particle view and set category to demo
            $this->addParticle('Particle001', array('key001'=>'value001'), 'demo');
            
            # display view content
            $this->display();
        }
    }

- ``$layout`` : 

the name of layout file, as the example, this action is under module ``Demo``,
and we just configed the view path to ``$this->getPath('View/')`` in prev doc, so, the layout
``Layout001`` will be locationed in `Path/To/Module/View/Layout/Layout001.php`. 

- ``$title`` : 

the title of web page, will be puted into ``<title>``

Layout
------
layout use to generate the content of body, if no layout be set, no content will be generated.
the layout is an simple php file that wirite html code in it, here is an example to display all
particles : ::

    <body> 
      <?php echo $this->getParticleViewManager()->toString(); ?>
    </body>

``$this`` is the instance of Html view class, so you can call the methods defined in it.

Particle View
-------------
particle view is part of view, the layout contains partcles, and place them into right place.
here is an example of a partcle view : ::

    <div>
    GLOBAL KEY 001 : <?php echo $globalKey001; ?>
    KEY 001 : <?php echo $key001; ?>
    </div>

Widget
------
widget is a kind of componet, here is the struces of it : ::

    Widget
    ` - Hello
        | - Widget.php
        ` - View
            ` - Hello.php

example of widget class defination : ::

    <?php
    namespace X\Service\Action\Test\Resource\Widget\Hello;
    use X\Service\Action\Component\WebView\WidgetBase;
    class Widget extends WidgetBase {
        /** @var string */
        protected $user = null;
        /** @var string */
        protected $widgetViewName = 'Hello';
    
        /** @return string */
        public function getUser() {
            return $this->user;
        }
    }

usage example in layout : ::

    <?php use X\Service\Action\Test\Resource\Widget\Hello\Widget as HelloWidget;?>
    <body>
    <?php HelloWidget::setup(array('user'=>'diabolo'), $this)->display(); ?>
    </body>

