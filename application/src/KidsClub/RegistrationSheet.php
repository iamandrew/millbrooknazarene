<?php

namespace Application\KidsClub;

use RuntimeException;

class RegistrationSheet
{
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const SCOPE = 'https://www.googleapis.com/auth/spreadsheets';

    private string $spreadsheetId;
    private string $range;
    private ?string $credentialsPath;
    private ?array $credentials = null;

    public function __construct(string $spreadsheetId, string $range = 'Sheet1!A1:Z', ?string $credentialsPath = null)
    {
        $this->spreadsheetId = $spreadsheetId;
        $this->range = $range;
        $this->credentialsPath = $credentialsPath;
    }

    public function append(array $row): void
    {
        $token = $this->getAccessToken();
        $url = sprintf(
            'https://sheets.googleapis.com/v4/spreadsheets/%s/values/%s:append?valueInputOption=RAW&insertDataOption=INSERT_ROWS',
            rawurlencode($this->spreadsheetId),
            rawurlencode($this->range)
        );

        $response = $this->postJson($url, ['values' => [$row]], [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ]);

        if ($response['status'] < 200 || $response['status'] >= 300) {
            throw new RuntimeException('Google Sheets append failed with status ' . $response['status'] . ': ' . $response['body']);
        }
    }

    private function getAccessToken(): string
    {
        $credentials = $this->getCredentials();
        $now = time();
        $claimSet = [
            'iss' => $credentials['client_email'],
            'scope' => self::SCOPE,
            'aud' => self::TOKEN_URL,
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $jwt = $this->createJwt(['alg' => 'RS256', 'typ' => 'JWT'], $claimSet, $credentials['private_key']);
        $response = $this->postForm(self::TOKEN_URL, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response['status'] < 200 || $response['status'] >= 300) {
            throw new RuntimeException('Google token request failed with status ' . $response['status'] . ': ' . $response['body']);
        }

        $payload = json_decode($response['body'], true);
        if (!is_array($payload) || empty($payload['access_token'])) {
            throw new RuntimeException('Google token response did not include an access token.');
        }

        return (string) $payload['access_token'];
    }

    private function getCredentials(): array
    {
        if ($this->credentials !== null) {
            return $this->credentials;
        }

        $json = getenv('MILLBROOK_GOOGLE_SERVICE_ACCOUNT_JSON') ?: '';
        if ($json === '') {
            $path = $this->credentialsPath ?: (getenv('GOOGLE_APPLICATION_CREDENTIALS') ?: '');
            if ($path === '' && defined('DIR_BASE')) {
                $path = DIR_BASE . '/service-account.json';
            }

            if ($path === '' || !is_readable($path)) {
                throw new RuntimeException('Google service account credentials are not configured.');
            }

            $json = (string) file_get_contents($path);
        }

        $credentials = json_decode($json, true);
        if (!is_array($credentials)) {
            throw new RuntimeException('Google service account credentials are not valid JSON.');
        }

        foreach (['client_email', 'private_key'] as $key) {
            if (empty($credentials[$key]) || !is_string($credentials[$key])) {
                throw new RuntimeException('Google service account credentials are missing ' . $key . '.');
            }
        }

        $this->credentials = $credentials;

        return $this->credentials;
    }

    private function createJwt(array $header, array $claimSet, string $privateKey): string
    {
        $unsigned = $this->base64UrlEncode(json_encode($header)) . '.' . $this->base64UrlEncode(json_encode($claimSet));
        $signature = '';

        if (!openssl_sign($unsigned, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Could not sign Google service account request.');
        }

        return $unsigned . '.' . $this->base64UrlEncode($signature);
    }

    private function postForm(string $url, array $fields): array
    {
        return $this->request($url, [
            CURLOPT_POSTFIELDS => http_build_query($fields, '', '&'),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
    }

    private function postJson(string $url, array $payload, array $headers): array
    {
        return $this->request($url, [
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => $headers,
        ]);
    }

    private function request(string $url, array $options): array
    {
        $handle = curl_init($url);
        if (!$handle) {
            throw new RuntimeException('Could not initialise HTTP request.');
        }

        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
        ] + $options);

        $body = curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        if ($body === false) {
            throw new RuntimeException('HTTP request failed: ' . $error);
        }

        return [
            'status' => $status,
            'body' => (string) $body,
        ];
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
