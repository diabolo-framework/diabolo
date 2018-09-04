Service
=======
Action service use to execute action by name, action could be web request, ajax request, api request
or command in cli.

action can be grouped by module or something else, so, it's ok to have same action name.

Configuration
-------------
service configuration : ::

    use X\Service\Action\Service as ActionService;
    return array(
        'document_root' => __DIR__,
        'module_path' => array(),
        'service_path' => array(),
        'library_path' => array(),
        'modules' => array(),
        'params' => array(),
        'services' => array(
            'Action' => array(
                'class' => ActionService::class,
                'enable' => true,
                'delay' => true,
                'params' => array(
                    'actionParamName' => 'action', # optional
                    'globalViewPath' => 'View', # optional
                ),
            ),
        ),
    );

- ``actionParamName`` : tells service where to find action name
- ``globalViewPath`` : tells service where to find global views

Basic Usage
-----------
to use action service, we have to setup action groups, mostly we setup
action group by module, here is an example to do that : ::

    <?php
    namespace X\Module\Demo;
    use X\Core\Module\XModule;
    use X\Service\Action\Service as ActionService;
    class Module extends XModule {
        public function run($parameters = array()) {
             
             $service = ActionService::getService();
             
             # set request parameters
             $service->setParams($parameters);
             
             # set group by module name
             $group = $service->addGroup($this->getName(), '\\X\\Module\\Demo\\Action');
             $group->setDefaultAction('index');
             $group->setViewPath($this->getPath('View/'));
             
             return $service->runGroup($group->getName());
        }
    }

