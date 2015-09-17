<?php

$opt = (getopt('VP'));

$index = 'index.php';
if (!isset($opt['P']) && !is_dir('public')) {
    mkdir('public');
    $index = "public/$index";
}
copy(__DIR__.'/index.example.php', $index);
copy(__DIR__.'/action.example.php', 'action.php');

if (!isset($opt['V'])) {
    mkdir('view');
    copy(__DIR__.'/layout.example.html', 'view/layout.html');
    file_put_contents('view/index.html', 'hello');
}
