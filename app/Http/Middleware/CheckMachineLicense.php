<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class CheckMachineLicense
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedHost = env('LICENSE_ALLOWED_HOST', 'DESKTOP-L4L3K81');
        $alertEmail = env('LICENSE_ALERT_EMAIL', 'kirolossamer6@gmail.com');
        $currentHost = gethostname() ?: php_uname('n');

        if ($currentHost !== $allowedHost) {
            $ip = $request->ip();
            $url = $request->fullUrl();
            $userAgent = (string) $request->userAgent();

            // Avoid sending email on every single request from the same machine/IP.
            $cacheKey = 'license_alert_sent:' . sha1($currentHost . '|' . $ip);

            if (Cache::add($cacheKey, true, now()->addMinutes(30))) {
                try {
                    Mail::raw(
                        "License violation detected.\n\n"
                        . "Allowed Host: {$allowedHost}\n"
                        . "Current Host: {$currentHost}\n"
                        . "IP: {$ip}\n"
                        . "URL: {$url}\n"
                        . "User Agent: {$userAgent}\n"
                        . 'Time: ' . now()->toDateTimeString(),
                        function ($message) use ($alertEmail, $currentHost): void {
                            $message->to($alertEmail)
                                ->subject("License Error - Unauthorized Host ({$currentHost})");
                        }
                    );
                } catch (\Throwable $e) {
                    Log::error('Failed to send license alert email.', [
                        'error' => $e->getMessage(),
                        'host' => $currentHost,
                        'ip' => $ip,
                    ]);
                }
            }

            abort(403, 'License Error');
        }

        return $next($request);
    }
}
