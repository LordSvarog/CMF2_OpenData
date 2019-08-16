<?php

namespace Deployer;

require 'recipe/rsync.php';
require 'recipe/cmf2.php';

// Configuration

if (!has('ssh_directory')) {
    set('ssh_directory', getenv('SSH_DIRECTORY'));
}
if (!has('ssh_host')) {
    set('ssh_host', getenv('SSH_HOST'));
}
if (!has('ssh_port')) {
    set('ssh_port', getenv('SSH_PORT'));
}
if (!has('ssh_username')) {
    set('ssh_username', getenv('SSH_USERNAME'));
}

set('rsync', [
    'exclude' => [],
    'exclude-file' => 'exclude.txt',
    'include' => [],
    'include-file' => 'include.txt',
    'filter' => [],
    'filter-file' => false,
    'filter-perdir' => false,
    'flags' => 'rz',
    'options' => ['links', 'delete'],
    'timeout' => 3600,
]);

// Hosts

host(get('ssh_host'))
    ->user(get('ssh_username'))
    ->forwardAgent(false)
    ->multiplexing(true)
    ->sshFlags([
        '-p' => get('ssh_port', 22),
    ])
    ->set('deploy_path', '{{ssh_directory}}')
    ->set('rsync_src', '.')
    ->set('rsync_dest', '{{release_path}}');

task('deploy:develop', function () {
    invoke('deploy:prepare');
    invoke('deploy:lock');
    invoke('deploy:release');
    invoke('rsync');
    invoke('deploy:symlink');
    invoke('deploy:unlock');
    invoke('success');
})->desc(
    'Deploy your project'
);

task('deploy:production', function () {
    invoke('deploy:prepare');
    invoke('deploy:lock');
    invoke('deploy:release');
    invoke('rsync');
    invoke('deploy:symlink');
    invoke('deploy:unlock');
    invoke('success');
})->desc(
    'Deploy your project'
);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
