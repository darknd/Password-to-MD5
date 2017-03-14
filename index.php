<?php

// Kickstart the framework
$f3=require('lib/base.php');

CONST _DB_NAME = 'database';
CONST _DB_USER = 'user';
CONST _DB_PASS = 'secret';

$f3->set('DEBUG',1);
if ((float)PCRE_VERSION<7.9)
    trigger_error('PCRE version is out of date');

// Load configuration
$f3->config('config.ini');

$db=new \DB\SQL('mysql:host=localhost;port=3306;dbname='._DB_NAME.','._DB_USER.','._DB_PASS);

$f3->route('GET /',
    function($f3) {
        global $f3;
        $view = new View;
        echo $view->render('menu.html');
    });

$f3->route('POST /',
    function($f3){
        if (empty($_POST['pass'])){
            $f3->reroute('/');
        }else{
            global $db, $f3;
            $pass = $_POST['pass'];
            $exists = $db->exec('SELECT * FROM passwords WHERE password=:password',array(':password'=>$pass));
            if ($exists){
                $f3->set('pass_md5', $exists[0]['md5']);
                $view = new View;
                echo $view->render('menu.html');
            }else{
                $pass_md5 = md5($pass);
                $insert = $db->exec('INSERT INTO passwords VALUES(:pass,:pass_md5);',array(':pass'=>$pass, ':pass_md5'=>$pass_md5));
                $f3->set('pass_md5', $pass_md5);
                $view = new View;
                echo $view->render('menu.html');
            }
        }

    });

$f3->run();
