<?php

$all = isset($_POST['all']) && $_POST['all'] === '1' ? true : false;

$script = realpath(dirname(__FILE__) . '/../../main.py');

// which python
$python = '/opt/homebrew/opt/python@3/libexec/bin/python';

if (!$all) {
    $domain = $_POST['domain'];
    $phrase = $_POST['phrase'];
    $number = $_POST['number'] ?? 100;

    $output = shell_exec(
        strtr(':python :script -d :domain -s ":phrase" -n :number', [
            ':python' => $python,
            ':script' => $script,
            ':domain' => $domain,
            ':phrase' => $phrase,
            ':number' => $number,
        ]),
    );
} else {
    $output = shell_exec(
        strtr(':python :script -a', [
            ':python' => $python,
            ':script' => $script,
        ]),
    );
}

// Přesměrovat zpátky
header('Location: /?message=' . urlencode($output));