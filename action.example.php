<?php

namespace Action;

function index()
{
    \Occam\render();
}

function page404()
{
    header('HTTP/1.1 404 not found');
    echo "no page";
}
