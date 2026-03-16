<?php

namespace App\Console\Commands;

use Google\Client as GoogleClient;
use Google\Service\Drive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GoogleOAuthSetup extends Command
{
    protected $signature = 'backup:google-oauth-setup';

    protected $description = 'One-time setup: obtain a Google OAuth2 refresh token and save it to .env';

    private const CALLBACK_PORT = 8099;

    public function handle(): int
    {
        $this->info('=== Google Drive OAuth2 One-Time Setup ===');
        $this->newLine();

        $clientId = config('services.google_drive.client_id');
        $clientSecret = config('services.google_drive.client_secret');

        if (blank($clientId) || blank($clientSecret)) {
            $this->error('GOOGLE_DRIVE_CLIENT_ID and GOOGLE_DRIVE_CLIENT_SECRET must be set in .env first.');
            $this->line('Create OAuth client in Google Cloud Console and set redirect URI: http://127.0.0.1:'.self::CALLBACK_PORT);

            return self::FAILURE;
        }

        $redirectUri = 'http://127.0.0.1:'.self::CALLBACK_PORT;

        $client = new GoogleClient();
        $client->setApplicationName(config('app.name', 'Laravel').' Backup Setup');
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($redirectUri);
        $client->setScopes([Drive::DRIVE_FILE]);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();

        $this->line('Open this URL to authorize:');
        $this->line($authUrl);
        $this->line('Waiting for callback on '.$redirectUri.' ...');

        if (PHP_OS_FAMILY === 'Windows') {
            popen('start "" "'.addslashes($authUrl).'"', 'r');
        }

        $code = $this->captureAuthCode();

        if (blank($code)) {
            $this->error('No authorization code received (timeout).');

            return self::FAILURE;
        }

        try {
            $token = $client->fetchAccessTokenWithAuthCode($code);
        } catch (\Throwable $e) {
            $this->error('Failed to exchange authorization code: '.$e->getMessage());

            return self::FAILURE;
        }

        if (isset($token['error'])) {
            $this->error('Google returned an error: '.($token['error_description'] ?? $token['error']));

            return self::FAILURE;
        }

        $refreshToken = $token['refresh_token'] ?? null;

        if (blank($refreshToken)) {
            $this->error('No refresh token returned. Revoke previous consent and retry.');

            return self::FAILURE;
        }

        $this->saveToEnv('GOOGLE_DRIVE_REFRESH_TOKEN', $refreshToken);

        $this->info('Refresh token saved to .env');
        $this->line('Now run: php artisan backup:diagnose-drive');

        return self::SUCCESS;
    }

    protected function captureAuthCode(): ?string
    {
        $socket = @stream_socket_server('tcp://127.0.0.1:'.self::CALLBACK_PORT, $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN);

        if (! $socket) {
            $this->error("Could not open port ".self::CALLBACK_PORT.": {$errstr} ({$errno})");

            return null;
        }

        stream_set_timeout($socket, 120);
        $connection = stream_socket_accept($socket, 120);
        fclose($socket);

        if (! $connection) {
            return null;
        }

        $request = '';
        while (! feof($connection) && ! str_contains($request, "\r\n\r\n")) {
            $request .= fread($connection, 1024);
        }

        $html = '<html><body style="font-family:sans-serif;text-align:center;padding:50px"><h2 style="color:#28a745">Authorized</h2><p>You can close this tab now.</p></body></html>';

        fwrite($connection, "HTTP/1.1 200 OK\r\nContent-Type: text/html\r\nContent-Length: ".strlen($html)."\r\nConnection: close\r\n\r\n".$html);
        fclose($connection);

        if (preg_match('/GET \/\?(.+) HTTP/i', $request, $matches)) {
            parse_str($matches[1], $params);

            return $params['code'] ?? null;
        }

        return null;
    }

    protected function saveToEnv(string $key, string $value): void
    {
        $envPath = base_path('.env');
        $contents = File::get($envPath);
        $escapedKey = preg_quote($key, '/');

        if (preg_match("/^{$escapedKey}=.*$/m", $contents)) {
            $contents = preg_replace("/^{$escapedKey}=.*$/m", $key.'='.$value, $contents);
        } else {
            $contents .= PHP_EOL.$key.'='.$value.PHP_EOL;
        }

        File::put($envPath, $contents);
    }
}
