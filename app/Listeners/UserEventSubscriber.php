<?php

namespace App\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Request;

class UserEventSubscriber
{
    public function handleUserLogin($event) {
        $event->user->update([
            'last_login' => Carbon::now(),
            'last_login_info' => 'IP : '.Request::ip() .'-'.request()->get('browserInfo', $event->user->last_login_info),
        ]);
        $event->user->login_sessions()->create([
            'model_id'=>$event->user->id,
            'model_type'=>$event->user,
            'data'=> 'IP : '.Request::ip() .'-'.request()->get('browserInfo', $event->user->last_login_info),
        ]);
    }

    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            [UserEventSubscriber::class, 'handleUserLogin']
        );
    }
}
