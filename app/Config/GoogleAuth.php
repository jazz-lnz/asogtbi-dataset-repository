<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class GoogleAuth extends BaseConfig
{
    public string $clientId = '';
    public string $clientSecret = '';
    public string $redirectUri = '';
    public string $allowedDomain = 'my.cspc.edu.ph';
    public string $caBundle = '';
    public array $scopes = ['openid', 'email', 'profile'];
    public string $authEndpoint = 'https://accounts.google.com/o/oauth2/v2/auth';
    public string $tokenEndpoint = 'https://oauth2.googleapis.com/token';
    public string $userInfoEndpoint = 'https://openidconnect.googleapis.com/v1/userinfo';

    public function __construct()
    {
        parent::__construct();

        $this->clientId = $this->envString('google.clientId');
        $this->clientSecret = $this->envString('google.clientSecret');
        $this->redirectUri = $this->envString('google.redirectUri');
        $this->allowedDomain = strtolower($this->envString('google.allowedDomain', $this->allowedDomain));
        $this->caBundle = $this->envString('google.caBundle');
    }

    public function isConfigured(): bool
    {
        return $this->clientId !== '' && $this->clientSecret !== '';
    }

    private function envString(string $key, string $default = ''): string
    {
        return trim((string) env($key, $default), " \t\n\r\0\x0B'\"");
    }
}
