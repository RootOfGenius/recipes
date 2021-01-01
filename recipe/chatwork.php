<?php
/* (c) LouisGin <hieuleadergin@gmail.com>
 * Configuration:
    1. Create chatwork account by any manual in the internet
    2. Take chatwork token (Like: b29a700e2d15bef3f26ae6a5c142d1ea) and set `chatwork_token` parameter
    3. Take chatwork room id from url after clicked on the room, and set `chatwork_room_id` parameter
    4. If you want, you can edit `chatwork_notify_text`, `chatwork_success_text` or `chatwork_failure_text`
    5. Profit!
 */
namespace Deployer;
use Deployer\Utility\Httpie;

// Chatwork settings
set('chatwork_token', function () {
    throw new \RuntimeException('Please configure "chatwork_token" parameter.');
});
set('chatwork_room_id', function () {
    throw new \RuntimeException('Please configure "chatwork_room_id" parameter.');
});
set('chatwork_api', function () {
   return 'https://api.chatwork.com/v2/rooms/' . get('chatwork_room_id') . '/messages';
});

// The Messages
set('chatwork_notify_text', "[info]\n[title](*) Deployment Status: Deploying[/title]\nRepo: {{repository}}\nBranch: {{branch}}\nServer: {{hostname}}\nRelease Path: {{release_path}}\nCurrent Path: {{current_path}}\n[/info]");
set('chatwork_success_text', "[info]\n[title](*) Deployment Status: Successfully[/title]\nRepo: {{repository}}\nBranch: {{branch}}\nServer: {{hostname}}\nRelease Path: {{release_path}}\nCurrent Path: {{current_path}}\n[/info]");
set('chatwork_failure_text', "[info]\n[title](*) Deployment Status: Failed[/title]\nRepo: {{repository}}\nBranch: {{branch}}\nServer: {{hostname}}\nRelease Path: {{release_path}}\nCurrent Path: {{current_path}}\n[/info]");

// Helpers
task('chatwork_send_message', function() {
    Httpie::post(get('chatwork_api'))
        ->query(['body' => get('chatwork_message'),])
        ->header("X-ChatWorkToken: ". get('chatwork_token'))
        ->send();
});

// Tasks
desc('Notifying Chatwork');
task('chatwork:notify', function () {
    if (!get('chatwork_token', false)) {
        return;
    }
    
    if (!get('chatwork_room_id', false)) {
        return;
    }
    set('chatwork_message', get('chatwork_notify_text'));
    invoke('chatwork_send_message');
})
    ->once()
    ->shallow()
    ->setPrivate();

desc('Notifying Chatwork about deploy finish');
task('chatwork:notify:success', function () {
    if (!get('chatwork_token', false)) {
        return;
    }
      
    if (!get('chatwork_room_id', false)) {
        return;
    }

    set('chatwork_message', get('chatwork_success_text'));
    invoke('chatwork_send_message');
})
    ->once()
    ->shallow()
    ->setPrivate();

desc('Notifying Chatwork about deploy failure');
task('chatwork:notify:failure', function () {
    if (!get('chatwork_token', false)) {
        return;
    }
      
    if (!get('chatwork_room_id', false)) {
        return;
    }

    set('chatwork_message', get('chatwork_failure_text'));
    invoke('chatwork_send_message');
})
    ->once()
    ->shallow()
    ->setPrivate();
