<?php
namespace X\Module\Syscmd\Action;
use X\Core\X;
use X\Service\XAction\Handler\CommandAction;
use X\Core\Component\Directory;
use X\Core\Component\ClassHelper;
/**
 * Display help infomation about the commands.
 * @author Michael Luthor <michaelluthor@163.com>
 */
class Help extends CommandAction {
    /**
     * @param string $target command name, e.g. `service/x-database/migrate/up`.
     * @see \X\Service\XAction\Util\Action::runAction()
     */
    public function runAction( $target=null ) {
        if ( null === $target ) {
            $this->listAllCommands();
        } else {
            $this->showTargetHelp($target);
        }
    }

    /**
     * @param string $target
     */
    private function showTargetHelp( $target ) {
        $targetInfo = $this->getTargetInfo($target);
        $actionClass = null;
        if ( 'module' === $targetInfo['type'] ) {
            $actionClass = "X\\Module\\{$targetInfo['target']}\\Action\\{$targetInfo['path']}";
        } else {
            $actionClass = "X\\Service\\{$targetInfo['target']}\\Command\\{$targetInfo['path']}";
        }
        
        printf("\n  Command : {$target}\n");
        printf("  %s\n", $this->getClassDescription($actionClass));
        
        $handleInfo = new \ReflectionMethod($actionClass, 'runAction');
        $handleComment = $handleInfo->getDocComment();
        $docInfo = ClassHelper::parseDocComment($handleComment);
        
        if ( isset($docInfo['description']) && !empty($docInfo['description']) ) {
            printf("  %s\n",$docInfo['description'][0]);
        }
        
        $this->showTargetParameters($actionClass);
        $this->showTargetOption($actionClass);
        
        if ( isset($docInfo['link']) && !empty($docInfo['link']) ) {
            printf("\n  Link :  %s\n",$docInfo['link'][0]);
        }
        
        if ( isset($docInfo['example']) && !empty($docInfo['example']) ) {
            printf("\n  Examples : \n");
            foreach ( $docInfo['example'] as $example ) {
                printf("  %s\n", $example);
            }
        }
    }
    
    /**
     * display parameters of target
     * @param unknown $actionClass
     */
    private function showTargetParameters($actionClass) {
        $handleInfo = new \ReflectionMethod($actionClass, 'runAction');
        $handleComment = $handleInfo->getDocComment();
        $docInfo = ClassHelper::parseDocComment($handleComment);
        
        if ( !isset($docInfo['param']) || empty($docInfo['param']) ) {
            return;
        }
        
        $paramDetails = array();
        $classParams = $handleInfo->getParameters();
        foreach ( $classParams as $classParam ) {
            /** @var $classParam \ReflectionParameter */
            $paramDetails[$classParam->getName()] = array(
                'optional' => $classParam->isOptional(),
                'default' => $classParam->isOptional() ? $classParam->getDefaultValue() : null,
            );
        }
        
        $paramMaxLength = 0;
        foreach ( $docInfo['param'] as $param ) {
            $paramMaxLength = (strlen($param['name'])>$paramMaxLength) ? strlen($param['name']) : $paramMaxLength;
        }
        
        printf("\n  Parameters : \n");
        foreach ( $docInfo['param'] as $param ) {
            if ( isset($paramDetails[$param['name']]) && $paramDetails[$param['name']]['optional'] ) {
                printf("    %-{$paramMaxLength}s     (%s) [default = %s] %s\n",
                $param['name'],
                $param['type'],
                $paramDetails[$param['name']]['default'],
                $param['description']
                );
            } else {
                printf("    %-{$paramMaxLength}s     (%s) %s\n", $param['name'],$param['type'],$param['description']);
            }
        }
    }
    
    /**
     * display options for target
     * @return void
     */
    private function showTargetOption($actionClass) {
        $actionClassInfo = new \ReflectionClass($actionClass);
        $options = $actionClassInfo->getProperties(\ReflectionProperty::IS_PROTECTED);
        if ( empty( $options ) ) {
            return;
        }
        
        $maxOptionNameLength = 0;
        $optionItems = array();
        
        printf("\n  Options : \n");
        foreach ( $options as $option ) {
            /** @var $option \ReflectionProperty */
            $option->setAccessible(true);
            $optionDoc = ClassHelper::parseDocComment($option->getDocComment());
            $name = $option->getName();
            $optionItems[] = array(
                'name' => $name,
                'type' => @$optionDoc['var'][0],
                'description' => @$optionDoc['description'][0],
                'default' => $option->getValue(new $actionClass('','')),
            );
            $maxOptionNameLength = (strlen($name)>$maxOptionNameLength) ? strlen($name) : $maxOptionNameLength;
        }
        
        foreach ( $optionItems as $optionItem ) {
            $display = array();
            $display[] = sprintf("--%-{$maxOptionNameLength}s   ", $optionItem['name']);
            if ( !empty($optionItem['type']) ) {
                $display[] = "({$optionItem['type']})";
            }
            if ( !empty($optionItem['default']) ) {
                $display[] = "[default = {$optionItem['default']}]";
            }
            $display[] = $optionItem['description'];
            echo "    ", implode(" ", $display), "\n";
        }
        
        echo "\n";
    }
    
    /**
     * get target information.
     * @param string $target
     * @return array
     */
    private function getTargetInfo( $command ) {
        if ( false !== strpos($command, '\\') ) {
            $command = explode('\\', $command);
        } else {
            $command = explode('/', $command);
        }
        
        if ( 1 === count($command) ) {
            array_unshift($command, 'syscmd');
        }
        if ( 'syscmd' === $command[0] ) {
            array_unshift($command, 'module');
        }
        
        $type =  array_shift($command);
        $target = $this->convertToUpperCamelBySeparator(array_shift($command), '-');
        $path = $this->convertToUpperCamelBySeparator(implode('/', $command), '-');
        $path = $this->convertToUpperCamelBySeparator($path, '/', '\\');
        $path = $this->convertToUpperCamelBySeparator($path, '\\', '\\');
        
        return array('type' => $type,'target' => $target,'path' => $path);
    }
    
    /**
     * List all available commands
     * @return void
     */
    private function listAllCommands() {
        $actions = array();
        
        $moduleManager = X::system()->getModuleManager();
        $modules = $moduleManager->getList();
        foreach ( $modules as $moduleName ) {
            $module = $moduleManager->get($moduleName);
            $actions[$moduleName] = $this->fetchActionNamesByPath('module', $moduleName, $module->getPath('Action'));
        }
        
        $serviceManager = X::system()->getServiceManager();
        $services = $serviceManager->getList();
        foreach ( $services as $serviceName ) {
            $service = $serviceManager->get($serviceName);
            $actions[$serviceName] = $this->fetchActionNamesByPath('service', $serviceName, $service->getPath('Command'));
        }
        
        $maxLengthOfCmdName = 0;
        foreach ( $actions as $targetName => $targetActions ) {
            foreach ( $targetActions as $action ) {
                $maxLengthOfCmdName = (strlen($action['name'])>$maxLengthOfCmdName) 
                ? strlen($action['name']) 
                : $maxLengthOfCmdName;
            }
        }
        $maxLengthOfCmdName += 5;
        
        printf("\n");
        foreach ( $actions as $targetName => $targetActions ) {
            if ( empty($targetActions) ) {
                continue;
            }
            printf("%s\n", $targetName);
            foreach ( $targetActions as $action ) {
                if ( 'Syscmd' === $targetName ) {
                    $action['name'] = substr($action['name'], 14);
                    if ( false !== strpos($action['name'], '/') ) {
                        $action['name'] = 'syscmd/'.$action['name'];
                    }
                }
                printf("    %-{$maxLengthOfCmdName}s %s\n",$action['name'],$action['description']);
            }
            printf("\n");
        }
    }
    
    /**
     * Fetch action name list by given path
     * @param string $path
     * @param string $actionClassTemplate
     * @return array
     */
    private function fetchActionNamesByPath( $targetType, $targetName, $actionPath ) {
        $actions = array();
        $actionFiles = Directory::listAllFiles($actionPath);
        foreach ( $actionFiles as &$actionFile ) {
            $actionFile = substr($actionFile, strlen($actionPath)+1, -4);
            if ( 'module' === $targetType ) {
                $actionClass = "X\\Module\\{$targetName}\\Action\\{$actionFile}";
            } else if ( 'service' === $targetType ){
                $actionClass = "X\\Service\\{$targetName}\\Command\\{$actionFile}";
            }
            if ( is_subclass_of($actionClass, CommandAction::class) ) {
                $commandName = $this->buildCommandPath($targetType, $targetName, $actionFile);
                $actions[] = array(
                    'type' => $targetType,
                    'name' => $commandName,
                    'description' => $this->getClassDescription($actionClass),
                );
            }
        }
        return $actions;
    }
    
    /**
     * get class's description
     * @param string $class
     * @return string
     */
    private function getClassDescription( $class ) {
        $classInfo = new \ReflectionClass($class);
        $comment = $classInfo->getDocComment();
        $docInfo = ClassHelper::parseDocComment($comment);
        return @$docInfo['description'][0];
    }
    
    /**
     * buidl command path .
     * @param string $type
     * @param string $target
     * @param string $action
     * @return string
     */
    private function buildCommandPath( $type, $target, $action ) {
        $path = array();
        $path[] = $type;
        $path[] = $this->convertCamelToMiddleSnake($target);
        
        $action = array_map(array($this,'convertCamelToMiddleSnake'), explode(DIRECTORY_SEPARATOR, $action));
        $path = array_merge($path, $action);
        return implode('/', $path);
    }
    
    /**
     * convert camel to middle snake
     * @param string $string
     * @return string
     */
    private function convertCamelToMiddleSnake( $string ) {
        $newString = array();
        $string = str_split ($string);
        foreach ( $string as $char ) {
            if ( ord($char)>64 && ord($char)<91 ) {
                $newString[] = '-';
                $newString[] = strtolower($char);
            } else {
                $newString[] = $char;
            }
        }
        return ltrim(implode('', $newString), '-');
    }
}