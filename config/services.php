<?php
return [
    'bitbucket' => [
        'auth_mode' => env('BITBUCKET_AUTH_MODE', 'ssh'),
        'user' => env('BITBUCKET_USERNAME'),
        'app_password' => env('BITBUCKET_APP_PASSWORD'),
        'workspace_a' => env('BITBUCKET_WORKSPACE_A'),
        'repo_a' => env('BITBUCKET_REPO_A'),
        'workspace_b' => env('BITBUCKET_WORKSPACE_B'),
        'repo_b' => env('BITBUCKET_REPO_B'),
        'ssh_key' => env('SSH_PRIVATE_KEY_PATH'),
        'ssh_passphrase' => env('SSH_PASSPHRASE'),
        'git_author_name' => env('GIT_AUTHOR_NAME', 'Code Merger Bot'),
        'git_author_email' => env('GIT_AUTHOR_EMAIL', 'codemerger@example.com'),
    ],
    'deploy' => [
        'ssh_host' => env('DEPLOY_SSH_HOST'),
        'ssh_user' => env('DEPLOY_SSH_USER', 'ubuntu'),
        'ssh_key_path' => env('DEPLOY_SSH_KEY_PATH', storage_path('keys/id_rsa')),
        'script_path' => env('DEPLOY_SCRIPT_PATH'),
        'app_path' => env('DEPLOY_APP_PATH', ''),
    ],
];
