#!/usr/bin/php
<?php

declare(ticks = 1);

// This is the number of threads what we will launch
if (3 == $argc && '-max' == $argv[1]) {
    $maxThreads = (int) $argv[2];
} else {
    $maxThreads = 50;
}

// We can't have less that 10 crons running
if ($maxThreads < 10) {
    $maxThreads = 10;
}

// Ensure that we are going to launch only a modulo 10 number of threads
if (0 != $maxThreads % 10) {
    $maxThreads = ceil($maxThreads / 10) * 10;
}

// List of running threads
$threads = array();

// Get the current runnig threads
exec('ps ax | grep spawnee.php | grep -v grep', $threads);

// Check if all the threads are running
$currentThreads = count($threads);
if ($currentThreads == $maxThreads) {
    // We have nothing better to do so we rest in pieces :)
    die(0);
}

$i = 0;
// Launch threads
while ($i < $maxThreads) {
    // Is our child thread running?
    $isRunning = false;

    // Check if thread doesn't exist first
    for ($j=0; $j<$currentThreads; $j++) {
        // Try and match our thread
        if (false !== strpos($threads[$j], '-id ' . $i . ' -modulo ' . $maxThreads)) {
            $isRunning = true;
        } elseif (false !== strpos($threads[$j], ' -id ')) {
            // Check to see if there's any thread running with the same number
            $stuff = explode('-modulo ', $threads[$j]);
            $stuff = explode('-id ', $stuff[0]);

            // Is this our ID?
            if ($i == $stuff[1]) {
                $stuff = explode(' ', $stuff[0]);

                // Kill the mofo
                exec('kill -9 '. $stuff[0]);
            }

        }

        // If we are running
        if ($isRunning) {
            // Skip rest of the threadss
            break;
        }
    }

    // Launch thread since it's not running
    if (false === $isRunning) {
        shell_exec(__DIR__.'/spawnee.php -id ' . $i . ' -modulo ' . $maxThreads . ' >/dev/null &');
    }

    // Next!
    $i++;
}

// Check if we have older crons running
$j--;
if (false !== isset($maxThreads[$j])) {

    // Get the number of old threads that are running and shouldn't be anymore
    $oldCrons = explode('-modulo ', $maxThreads[$j]);
    $oldCronsNumber = $oldCrons[1];

    // Kill them
    for ($i = $maxThreads; $i < $oldCronsNumber; $i++) {
        // With fire
        exec('kill -9 '. $i);
    }
}

