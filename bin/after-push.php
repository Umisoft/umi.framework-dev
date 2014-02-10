<?php
chdir(__DIR__ . '/..');
$time = time();

//todo auto-read subdirs of library/ for listing
$libsMap = [
    'acl'            => 'git@gl.umisoft.ru:umisoft/umi-acl.git',
    'authentication' => 'git@gl.umisoft.ru:umisoft/umi-authentication.git',
    'hmvc'           => 'git@gl.umisoft.ru:umisoft/umi-hmvc.git',
    'form'           => 'git@gl.umisoft.ru:umisoft/umi-form.git',
    'filter'         => 'git@gl.umisoft.ru:umisoft/umi-filter.git',
    'event'          => 'git@gl.umisoft.ru:umisoft/umi-event.git',
    'dbal'           => 'git@gl.umisoft.ru:umisoft/umi-dbal.git',
    'config'         => 'git@gl.umisoft.ru:umisoft/umi-config.git',
    'cache'          => 'git@gl.umisoft.ru:umisoft/umi-cache.git',
    'orm'            => 'git@gl.umisoft.ru:umisoft/umi-orm.git',
    'log'            => 'git@gl.umisoft.ru:umisoft/umi-log.git',
    'i18n'           => 'git@gl.umisoft.ru:umisoft/umi-i18n.git',
    'http'           => 'git@gl.umisoft.ru:umisoft/umi-http.git',
    'pagination'     => 'git@gl.umisoft.ru:umisoft/umi-pagination.git',
    'rbac'           => 'git@gl.umisoft.ru:umisoft/umi-rbac.git',
    'route'          => 'git@gl.umisoft.ru:umisoft/umi-route.git',
    'session'        => 'git@gl.umisoft.ru:umisoft/umi-session.git',
    'spl'            => 'git@gl.umisoft.ru:umisoft/umi-spl.git',
    'syntax'         => 'git@gl.umisoft.ru:umisoft/umi-syntax.git',
    'templating'     => 'git@gl.umisoft.ru:umisoft/umi-templating.git',
    'toolkit'        => 'git@gl.umisoft.ru:umisoft/umi-toolkit.git',
    'validation'     => 'git@gl.umisoft.ru:umisoft/umi-validation.git',
];
$extMap = [
    'twig' => 'git@gl.umisoft.ru:umisoft/umi-ext-twig.git',
];

$branches = "master 1.0";

$shPath = realpath(__DIR__) . "/git-subsplit.sh";
foreach ($libsMap as $subtree => $repo) {
    $subtreePath = "library/$subtree";
    $logFile = __DIR__ . "/{$subtree}.log";
    $command = "\"$shPath\" \"$subtreePath\" $repo --branches \"$branches\" > $logFile";
    print "\n\n===$subtree=== \nRun $command \n";
    $result = system($command);
    if ($result === false) {
        print "$repo failed \n";
    } else {
        print "$repo synced with result $result \n";
    }
}
foreach ($extMap as $subtree => $repo) {
    $subtreePath = "extension/$subtree";
    $logFile = __DIR__ . "/ext-{$subtree}.log";
    $command = "\"$shPath\" \"$subtreePath\" $repo --branches \"$branches\" > $logFile";
    print "\n\n===$subtree=== \nRun $command \n";
    $result = system($command);
    if ($result === false) {
        print "$repo failed \n";
    } else {
        print "$repo synced with result $result \n";
    }
}

$min = number_format((time() - $time) / 60, 2);
print "\n === finished in $min minutes\n\n";
