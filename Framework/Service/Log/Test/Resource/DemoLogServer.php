<?php
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ( false === $socket ) {
    throw new Exception('failed to create socket');
}
if ( false === socket_bind($socket, '127.0.0.1', 55555) ) {
    throw new Exception('failed to bind address : '.socket_strerror(socket_last_error()));
}
if ( false === socket_listen($socket) ) {
    throw new Exception('failed to listen : '.socket_strerror(socket_last_error()));
}
do {
    $client = socket_accept($socket);
    if ( false === $client ) {
        continue;
    }
    socket_write($client,"CONNECTED !\n");
    $contentStr = socket_read($client, 4096);
    echo $contentStr;
} while ( true );
socket_close($socket);