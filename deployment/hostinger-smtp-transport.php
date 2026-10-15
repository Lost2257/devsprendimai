<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

final class HostingerSmtpTransport extends EsmtpTransport
{
    // Symfony exposes capabilities to subclasses, not to callers. Only return
    // the boolean needed by setup; never expose the SMTP debug transcript.
    public function supportsAuthentication(): bool
    {
        return array_key_exists('AUTH', $this->getCapabilities());
    }
}
