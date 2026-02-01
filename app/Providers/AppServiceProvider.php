<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ClearEnrollQueue;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    // Laravel 12: Register route middleware alias here
    Route::aliasMiddleware('clear.enroll.queue', ClearEnrollQueue::class);
  }
}
