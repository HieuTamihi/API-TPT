<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| In this file, you may define all of the event broadcasting channels that
| your application supports. The given channel authorization callbacks
| are used to check if an authenticated user can listen to the channel.
|
*/

// Một ví dụ kênh để phát cho người dùng dựa trên ID
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
    