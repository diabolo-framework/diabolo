Command Action
==============
command action use to handle cli action.

Class Defination
----------------
example : ::

    <?php
    namespace X\Service\Action\Test\Resource\Action;
    use X\Service\Action\Handler\CommandAction;
    class MyCmd extends CommandAction {
        protected function run( ) {
            $string = $this->readLine("input some text : ");
            $char = $this->readChar("input a character : ");
            $promit = $this->prompt("input your account', 'admin');
            $isYes = $this->confirm('are you sure to delete this file', true);
            $color = $this->select('what\'s your favourite color', array(
                 'red',
                 'blue',
                 'yellow'
             ), 'blue');
            return $data;
        }
    }

- ``readLine()`` : read string from stdin ::

    input some text : _

- ``readChar()`` : read sigle char from stdin ::

    input a character : _

- ``prompt()`` : display message and get input content ::

    input your account [admin] : _

- ``confirm()`` : display message and get yes/no ::

    are you sure to delete this file (y/n) [y] : _

- ``select()`` : display message and get user selection ::

    what's your favourite color
    1) red
    2) blue
    3) yellow
    your selection [blue] : _

