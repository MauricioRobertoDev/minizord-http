<?php

use Doctum\Doctum;
use Doctum\RemoteRepository\GitHubRemoteRepository;
use Symfony\Component\Finder\Finder;

$dir      = './src';
$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Tests')
    ->in($dir);

return new Doctum($iterator, [
    'title'                => 'Minizord - Http',
    'language'             => 'en',
    'build_dir'            => __DIR__ . '/docs',
    'cache_dir'            => __DIR__ . '/cache',
    'source_dir'           => dirname($dir) . '/',
    'remote_repository'    => new GitHubRemoteRepository('yourorg/yourlib', dirname($dir)),
]);
