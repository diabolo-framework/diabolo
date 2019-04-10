<?php
namespace X\Module\Syscmd\Action\Service;
use X\Service\XAction\Handler\CommandAction;
class Exec extends CommandAction {
    /**
     * {@inheritDoc}
     * @see \X\Service\XAction\Util\Action::runAction()
     */
    public function runAction( $path ) {
        $params = $this->getParameter('params');
        $path = $this->convertToUpperCamelBySeparator(array_shift($params),'-');
        $path = $this->convertToUpperCamelBySeparator($path, '/','/');
        $path = explode('/', $path);
        
        array_shift($path);
        $service = array_shift($path);
        $actionPath = implode('\\', $path);
        
        $actionClass = sprintf('X\\Service\\%s\\Command\\%s', $service, $actionPath);
        $actionInstance = new $actionClass($service, implode('/', $path));
        $options = $this->getParameter('options');
        foreach ( $options as $optName => $optValue ) {
            $optName = $this->convertToUpperCamelBySeparator($optName, '-');
            $optName = lcfirst($optName);
            if ( property_exists($actionInstance, $optName) ) {
                $actionInstance->{$optName} = $optValue;
            }
        }
        try {
            $actionInstance->run(array(
                'params' => $params,
                'options' => $this->getParameter('options'),
            ));
        } catch ( \Exception $e ) {
            echo "Error: {$e->getMessage()}\n";
            echo "Location: {$e->getFile()} #{$e->getLine()}\n";
        }
    }
}