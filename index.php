<?php
if(!isset($_POST['input']) || empty($_POST['input']))
    die('DZCP-EE API Server 01');

server_api_decode::init();
server_api_decode::set_options('','');


file_put_contents(time().'.log', $_POST['input']);
die($_POST['input']);