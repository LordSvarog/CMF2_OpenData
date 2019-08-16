<?php

namespace Deployer;

use Deployer\Task\Context;

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

// Tasks

task('deploy:multiplex', function () {
    $host = Context::get()->getHost();

    if ($host->isMultiplexing() === null ? get('ssh_multiplexing', false) : $host->isMultiplexing()) {
        writeln('ssh multiplexing initialization');
        run('ls -la');
    }
})->desc(
    'Initialize multiplexing'
);

// If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
