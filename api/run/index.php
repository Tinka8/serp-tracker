<?php

$domain = $_POST['domain'];
$phrase = $_POST['phrase'];
$number = $_POST['number'] ?? 100;

$script = realpath(dirname(__FILE__) . "/../../main.py");

// which python
$python = '/opt/homebrew/opt/python@3/libexec/bin/python';

$output = shell_exec(strtr(':python :script -d :domain -s ":phrase" -n :number', [
    ':python' => $python,
    ':script' => $script,
    ':domain' => $domain,
    ':phrase' => $phrase,
    ':number' => $number,
]));

// Přesměrovat zpátky
header('Location: /?message=' . urlencode($output));