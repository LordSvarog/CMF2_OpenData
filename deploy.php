<?php

namespace Deployer;

use Deployer\Task\Context;

require 'recipe/rsync.php';
require 'recipe/cmf2.php';

desc('Prepare release. Clean up unfinished releases and prepare next release');
task('deploy:release', function () {
    cd('{{deploy_path}}');

    $previousReleaseExist = test('[ -h release ]');

    if ($previousReleaseExist) {
        run('rm release');
    }

    $releasePath = parse("{{deploy_path}}/releases/{{release_name}}");

    $date = run('date +"%Y%m%d%H%M%S"');

    run("echo '$date,{{release_name}}' > .dep/releases");

    $releaseExist = test('[ -h current ]');
    if (!$releaseExist) {
        run("mkdir $releasePath");
    }
    run("{{bin/symlink}} $releasePath {{deploy_path}}/release");
});


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

set('shared_files', [
    '.env',
    'html/.env.local',
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

task('deploy:vendors', function () {
    runLocally('make composer/install');
})->desc(
    'Installing vendors'
);

task('deploy:supervisor:start', function () {
    run('cd {{current_path}} && make supervisor/start');
})->desc(
    'Supervisor start'
);

task('deploy:supervisor:stop', function () {
    run('cd {{current_path}} && make supervisor/stop');
})->desc(
    'Supervisor stop'
);

task('deploy:mysql:backup', function () {
    run('cd {{current_path}} && make mysql/backup');
})->desc(
    'MySQL backup'
);

task('deploy:cron', function () {
    run('cd {{current_path}} && make docker cmd="crontab /var/spool/cron/config/crontab"');
})->desc(
    'Installing cron'
);

task('deploy:application', function () {
    run('cd {{current_path}} && make yii/up');
})->desc(
    'Installing application'
);

task('deploy:environment', function () {
    invoke('deploy:vendors');
    invoke('deploy:prepare');
    invoke('deploy:lock');
    invoke('deploy:release');
    invoke('rsync');
    invoke('deploy:shared');
    invoke('deploy:symlink');
    invoke('deploy:unlock');
    invoke('success');
})->desc(
    'Prepare deploy your project'
);

task('deploy:develop', function () {
    invoke('deploy:vendors');
    invoke('deploy:prepare');
    invoke('deploy:lock');
    invoke('deploy:release');
    invoke('deploy:supervisor:stop');
    invoke('rsync');
    invoke('deploy:mysql:backup');
    invoke('deploy:cron');
    invoke('deploy:application');
    invoke('deploy:shared');
    invoke('deploy:symlink');
    invoke('deploy:supervisor:start');
    invoke('deploy:unlock');
    invoke('success');
})->desc(
    'Deploy your project'
);

task('deploy:production', function () {
    invoke('deploy:vendors');
    invoke('deploy:prepare');
    invoke('deploy:lock');
    invoke('deploy:release');
    invoke('rsync');
    invoke('deploy:shared');
    invoke('deploy:symlink');
    invoke('deploy:unlock');
    invoke('success');
})->desc(
    'Deploy your project'
);

// If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
