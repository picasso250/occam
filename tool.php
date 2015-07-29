<?php

if (!is_dir('public')) {
    mkdir('public');
}
copy(__DIR__.'/index.example.php', 'public/index.php');
copy(__DIR__.'/action.example.php', 'action.php');

mkdir('view');
copy(__DIR__.'/layout.example.html', 'view/layout.html');
file_put_contents('view/index.html', 'hello');
