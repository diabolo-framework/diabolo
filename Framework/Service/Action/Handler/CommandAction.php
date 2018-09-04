<?php
namespace X\Service\Action\Handler;
/**
 * 命令行动作基类
 * @author Michael Luthor <michaelluthor@163.com>
 */
abstract class CommandAction extends ActionBase {
    /**
     * @param unknown $message
     * @param unknown $option
     * @return int
     */
    protected function select( $message, $options, $default=null ) {
        do {
            $this->writeChars($message." : \n");
            foreach ( $options as $index => $option ) {
                $this->writeChars("%d) %s\n", $index+1, $option);
            }
            
            $selectTip = 'your selection ';
            if ( null !== $default ) {
                $selectTip .= '['.$default.']';
            }
            $selected = $this->readLine($selectTip.' : ');
            if ( empty($selected) && null !== $default ) {
                return array_search($default, $options);
            }
            
            $selected = intval($selected);
            $selected -= 1;
            if ( isset($options[$selected]) ) {
                return $selected;
            }
        } while ( true );
    }
    
    /**
     * @param unknown $message
     * @return NULL|string
     */
    protected function confirm($message, $default=null) {
        $message .=  ' (y/n)';
        if ( null !== $default ) {
            $message .= ' ['.($default ? 'y' : 'n').']';
        }
        
        $map = ['y'=>true,'n'=>false];
        $answer = null;
        do {
            $answerChar = $this->readChar($message.' : ');
            if ( null === $answerChar && null !== $default ) {
                $answer = $default;
                break;
            }
            $answerChar = strtolower($answerChar);
            if ( isset($map[$answerChar]) ) {
                $answer = $map[$answerChar];
            }
        } while ( null === $answer );
        return $answer;
    }
    
    /**
     * @param unknown $message
     * @return string
     */
    protected function prompt($message, $default='') {
        if ( !empty($default) ) {
            $message .= ' ['.$default.']';
        }
        $content = $this->readLine($message.' : ');
        if ( empty($content) ) {
            $content = $default;
        }
        return $content;
    }
    
    /** @return string */
    protected function readChar( $tip=null ) {
        $content = $this->readLine($tip);
        return empty($content) ? null : $content[0];
    }
    
    /** @return string */
    protected function readLine( $tip=null ) {
        if ( null !== $tip ) {
            $this->writeChars($tip);
        }
        return rtrim(fgets(STDIN));
    }
    
    /** @return void */
    protected function writeChars( $content ) {
        call_user_func_array('printf', func_get_args());
        ob_flush();
        flush();
    }
}