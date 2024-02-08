<?php

namespace App\Listeners;

use App\Events\RegisterUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;

class SendEmailVerification implements ShouldQueue
{
  /**
   * Create the event listener.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Handle the event.
   *
   * @param  \App\Events\RegisterUser  $event
   * @return void
   */
  public function handle(RegisterUser $event)
  {
    $event->user->sendEmailVerificationNotification();
  }
}
