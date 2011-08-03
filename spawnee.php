#!/usr/bin/php
<?php

declare(ticks = 1);

// This is the number of threads what we will launch
if (5 == $argc && '-id' == $argv[1] && '-modulo' == $argv[3]) {
    // Exec shit :)

    echo 'doing stuff';

    sleep(40);
}
