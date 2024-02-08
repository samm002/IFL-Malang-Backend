<?php

namespace App\Listeners;

use App\Events\ForgotPassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class SendPasswordResetEmail implements ShouldQueue
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
   * @param  \App\Events\ForgotPassword  $event
   * @return void
   */
  public function handle(ForgotPassword $event)
  {
    $status = Password::sendResetLink(['email' => $event->email]);

    if ($status !== Password::RESET_LINK_SENT) {
      Log::error("Failed to send password reset link for email: {$event->email}");
    }
  }
}
