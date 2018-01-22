<?php
namespace X\Service\XRouter\Router;
use X\Service\XRouter\Service;
interface RouterInterface {
    function __construct( Service $service ); 
    function route($url);
    function format($url);
}