<?php

namespace Application\Form;

use Concrete\Core\Application\Application;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Invisible form protection: honeypot, minimum fill time, IP rate limit, and Cyrillic spam.
 * Real visitors never see a challenge; bots get a silent fake success or a soft retry message.
 */
class SilentSpamGuard implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public const OUTCOME_ALLOW = 'allow';
    public const OUTCOME_SILENT = 'silent';
    public const OUTCOME_RATE_LIMITED = 'rate_limited';

    private const MIN_SECONDS = 3;
    private const MAX_SECONDS = 86400;
    private const MAX_SUBMISSIONS_PER_HOUR = 5;
    private const HONEYPOT_FIELD = 'website';
    private const STARTED_FIELD = 'millbrook_form_started';
    private const SIGNATURE_FIELD = 'millbrook_form_sig';

    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getLoggerChannel(): string
    {
        return Channels::CHANNEL_SPAM;
    }

    /**
     * @return array{started:string,signature:string}
     */
    public function issueTimingFields(string $formKey): array
    {
        $started = (string) time();

        return [
            'started' => $started,
            'signature' => $this->sign($formKey, $started),
        ];
    }

    public function inspect(Request $request, string $formKey): string
    {
        if ($this->honeypotFilled($request)) {
            $this->logRejection('honeypot', $request, $formKey);

            return self::OUTCOME_SILENT;
        }

        if ($this->isRateLimited($request, $formKey)) {
            $this->logRejection('rate_limit', $request, $formKey);

            return self::OUTCOME_RATE_LIMITED;
        }

        // Count real-looking attempts so retries cannot hammer the inbox checks.
        $this->rememberAttempt($request, $formKey);

        if (!$this->timingLooksHuman($request, $formKey)) {
            $this->logRejection('timing', $request, $formKey);

            return self::OUTCOME_SILENT;
        }

        if ($this->looksLikeCyrillicSpam($request)) {
            $this->logRejection('cyrillic', $request, $formKey);

            return self::OUTCOME_SILENT;
        }

        return self::OUTCOME_ALLOW;
    }

    public function rememberAttempt(Request $request, string $formKey): void
    {
        $cache = $this->app->make('cache/expensive');
        $item = $cache->getItem($this->rateLimitCacheKey($request, $formKey));
        $count = $item->isMiss() ? 0 : (int) $item->get();
        $cache->save($item->set($count + 1)->expiresAfter(3600));
    }

    public function honeypotFieldName(): string
    {
        return self::HONEYPOT_FIELD;
    }

    public function startedFieldName(): string
    {
        return self::STARTED_FIELD;
    }

    public function signatureFieldName(): string
    {
        return self::SIGNATURE_FIELD;
    }

    private function honeypotFilled(Request $request): bool
    {
        return trim((string) $request->request->get(self::HONEYPOT_FIELD, '')) !== '';
    }

    private function timingLooksHuman(Request $request, string $formKey): bool
    {
        $started = trim((string) $request->request->get(self::STARTED_FIELD, ''));
        $signature = trim((string) $request->request->get(self::SIGNATURE_FIELD, ''));

        if ($started === '' || $signature === '' || !ctype_digit($started)) {
            return false;
        }

        if (!hash_equals($this->sign($formKey, $started), $signature)) {
            return false;
        }

        $age = time() - (int) $started;

        return $age >= self::MIN_SECONDS && $age <= self::MAX_SECONDS;
    }

    private function looksLikeCyrillicSpam(Request $request): bool
    {
        $text = $this->submittedText($request);
        if ($text === '') {
            return false;
        }

        if (preg_match_all('/\p{Cyrillic}/u', $text, $matches) === false) {
            return false;
        }

        $cyrillicCount = count($matches[0]);
        if ($cyrillicCount < 3) {
            return false;
        }

        preg_match_all('/\p{L}/u', $text, $letterMatches);
        $letterCount = count($letterMatches[0]);
        if ($letterCount === 0) {
            return false;
        }

        return ($cyrillicCount / $letterCount) >= 0.25;
    }

    private function isRateLimited(Request $request, string $formKey): bool
    {
        $cache = $this->app->make('cache/expensive');
        $item = $cache->getItem($this->rateLimitCacheKey($request, $formKey));
        if ($item->isMiss()) {
            return false;
        }

        return (int) $item->get() >= self::MAX_SUBMISSIONS_PER_HOUR;
    }

    private function rateLimitCacheKey(Request $request, string $formKey): string
    {
        $ip = (string) $request->getClientIp();

        return 'millbrook.form.rate.' . sha1($formKey . '|' . $ip);
    }

    private function submittedText(Request $request): string
    {
        $parts = [];
        foreach ($request->request->all() as $key => $value) {
            $key = (string) $key;
            if (in_array($key, [self::HONEYPOT_FIELD, self::STARTED_FIELD, self::SIGNATURE_FIELD, 'cf-turnstile-response', 'ccm_token'], true)) {
                continue;
            }
            $parts[] = $this->stringify($value);
        }

        return trim(implode("\n", array_filter($parts)));
    }

    /**
     * @param mixed $value
     */
    private function stringify($value): string
    {
        if (is_array($value)) {
            return implode(' ', array_map([$this, 'stringify'], $value));
        }

        if (is_scalar($value) || $value === null) {
            return trim((string) $value);
        }

        return '';
    }

    private function sign(string $formKey, string $started): string
    {
        return hash_hmac('sha256', $formKey . '|' . $started, $this->secret());
    }

    private function secret(): string
    {
        foreach (['MILLBROOK_FORM_SECRET', 'TURNSTILE_SECRET'] as $name) {
            $value = getenv($name);
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
            foreach ([$name, 'REDIRECT_' . $name] as $serverName) {
                $value = $_SERVER[$serverName] ?? '';
                if (is_string($value) && trim($value) !== '') {
                    return trim($value);
                }
            }
        }

        $configSecret = (string) $this->app->make('config')->get('concrete.security.token.validation');
        if ($configSecret !== '') {
            return $configSecret;
        }

        return 'millbrook-form-fallback-secret';
    }

    private function logRejection(string $reason, Request $request, string $formKey): void
    {
        $this->logger?->notice(sprintf(
            'Silent spam guard rejected %s submission (%s) from %s',
            $formKey,
            $reason,
            (string) $request->getClientIp()
        ));
    }
}
