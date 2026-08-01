<?php

namespace Concrete\Package\MillbrookTurnstile\Captcha;

use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Http\Client\Client;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;

class TurnstileController extends AbstractController implements CaptchaInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function getLoggerChannel(): string
    {
        return Channels::CHANNEL_SPAM;
    }

    public function display(): void
    {
        $siteKey = (string) $this->app->make('config')->get('captcha.turnstile.site_key');
        if ($siteKey === '') {
            $this->logger?->warning('Cloudflare Turnstile is active but no site key has been configured.');

            return;
        }

        echo '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
        echo '<div class="millbrook-turnstile-honeypot" aria-hidden="true">';
        echo '<label for="turnstile-website">Leave this field empty</label>';
        echo '<input id="turnstile-website" type="text" name="website" tabindex="-1" autocomplete="off">';
        echo '</div>';
        echo '<div class="cf-turnstile" data-sitekey="' . h($siteKey) . '" data-theme="light" data-size="flexible" data-action="contact_enquiry" data-appearance="interaction-only"></div>';
    }

    public function showInput(): void
    {
        // Turnstile is a single widget, unlike image CAPTCHAs with a separate input.
    }

    public function label(): string
    {
        return '';
    }

    public function saveOptions($data): void
    {
        $data = is_array($data) ? $data : [];
        $config = $this->app->make('config');

        $config->save('captcha.turnstile.site_key', trim((string) ($data['site_key'] ?? '')));
        $config->save('captcha.turnstile.secret_key', '');
    }

    public function check(): bool
    {
        $request = $this->app->make('request');
        if (trim((string) $request->request->get('website')) !== '') {
            return false;
        }

        $secretKey = trim((string) getenv('TURNSTILE_SECRET'));
        $token = trim((string) $request->request->get('cf-turnstile-response'));
        if ($secretKey === '' || $token === '') {
            $this->logger?->notice('Cloudflare Turnstile verification could not start because the token or TURNSTILE_SECRET was missing.');

            return false;
        }

        try {
            $client = $this->app->make(Client::class);
            $response = $client->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'headers' => ['Accept' => 'application/json'],
                'form_params' => [
                    'secret' => $secretKey,
                    'response' => $token,
                    'remoteip' => (string) $request->getClientIp(),
                ],
                'http_errors' => false,
                'timeout' => 10,
            ]);
            $result = json_decode((string) $response->getBody(), true);
        } catch (\Throwable $exception) {
            $this->logger?->error('Cloudflare Turnstile verification failed: ' . $exception->getMessage());

            return false;
        }

        $expectedHostname = strtolower((string) $request->getHost());
        $valid = $response->getStatusCode() === 200
            && is_array($result)
            && !empty($result['success'])
            && ($result['action'] ?? '') === 'contact_enquiry'
            && strtolower((string) ($result['hostname'] ?? '')) === $expectedHostname;

        if (!$valid) {
            $errorCodes = is_array($result) && is_array($result['error-codes'] ?? null) ? implode(', ', $result['error-codes']) : 'unknown error';
            $this->logger?->notice('Cloudflare Turnstile rejected a Contact form submission: ' . $errorCodes);
        }

        return $valid;
    }
}
