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

        echo '<div class="millbrook-turnstile-honeypot" aria-hidden="true">';
        echo '<label for="turnstile-website">Leave this field empty</label>';
        echo '<input id="turnstile-website" type="text" name="website" tabindex="-1" autocomplete="off">';
        echo '</div>';
        echo '<input class="millbrook-turnstile-response" type="hidden" name="cf-turnstile-response" value="">';
        echo '<div class="millbrook-turnstile-widget" aria-live="polite"></div>';
        echo '<script>(function(){var mount=document.currentScript.previousElementSibling;var response=mount.previousElementSibling;var render=function(){window.turnstile.render(mount,{sitekey:' . json_encode($siteKey) . ',theme:"light",size:"flexible",action:"contact_enquiry",appearance:"interaction-only","response-field":false,callback:function(token){response.value=token;document.dispatchEvent(new CustomEvent("millbrook:turnstile-success"));},"expired-callback":function(){response.value="";document.dispatchEvent(new CustomEvent("millbrook:turnstile-expired"));},"error-callback":function(){response.value="";document.dispatchEvent(new CustomEvent("millbrook:turnstile-error"));return true;}});};if(window.turnstile){render();return;}if(!window.millbrookTurnstileApi){window.millbrookTurnstileApi=new Promise(function(resolve,reject){var script=document.createElement("script");script.src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit";script.async=true;script.onload=resolve;script.onerror=reject;document.head.appendChild(script);});}window.millbrookTurnstileApi.then(render).catch(function(){response.value="";document.dispatchEvent(new CustomEvent("millbrook:turnstile-error"));});})();</script>';
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
            return $this->fail('The security check could not be completed. Please refresh the page and try again.');
        }

        $secretKey = $this->getEnvironmentValue('TURNSTILE_SECRET');
        $token = trim((string) $request->request->get('cf-turnstile-response'));
        if ($secretKey === '') {
            $this->logger?->notice('Cloudflare Turnstile verification could not start because TURNSTILE_SECRET was missing.');

            return $this->fail('The security check is not configured on the server yet. Please contact us by email instead.');
        }

        if ($token === '') {
            $this->logger?->notice('Cloudflare Turnstile verification could not start because the response token was missing.');

            return $this->fail('The security check did not complete in your browser. Please refresh the page and try again.');
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

            return $this->fail('We could not verify the security check just now. Please try again in a moment.');
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

            if ($response->getStatusCode() !== 200) {
                return $this->fail('The security check service returned an unexpected response. Please try again in a moment.');
            }

            if (!empty($result['success'])) {
                return $this->fail('The security check was completed for a different site or form. Please refresh the page and try again.');
            }

            return $this->fail('Cloudflare could not verify the security check (' . $errorCodes . '). Please refresh the page and try again.');
        }

        return $valid;
    }

    private function fail(string $message): bool
    {
        $this->app->make('session')->getFlashBag()->add('millbrook_turnstile_error', $message);

        return false;
    }

    private function getEnvironmentValue(string $name): string
    {
        $value = getenv($name);
        if (is_string($value) && trim($value) !== '') {
            return trim($value);
        }

        // Apache with PHP-FPM may pass SetEnv values through the request instead.
        foreach ([$name, 'REDIRECT_' . $name] as $serverName) {
            $value = $_SERVER[$serverName] ?? '';
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return '';
    }
}
