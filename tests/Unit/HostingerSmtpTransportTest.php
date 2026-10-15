<?php

namespace Tests\Unit;

use HostingerSmtpTransport;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Transport\Smtp\Auth\PlainAuthenticator;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\AbstractStream;

require_once __DIR__.'/../../deployment/hostinger-smtp-transport.php';

class HostingerSmtpTransportTest extends TestCase
{
    public function test_successful_authentication_allows_public_capability_check(): void
    {
        [$transport, $stream] = $this->simulatedTransport([
            "220 smtp.example.test ready\r\n",
            "250-smtp.example.test\r\n",
            "250 AUTH PLAIN\r\n",
            "235 2.7.0 Authentication successful\r\n",
            "221 2.0.0 Bye\r\n",
        ]);

        $transport->start();

        // The original helper called the parent's protected method directly,
        // throwing Error after successful AUTH and reporting a bad password.
        $this->assertTrue($transport->supportsAuthentication());
        $transport->stop();
        $this->assertSame([
            "EHLO [127.0.0.1]\r\n",
            $this->fakeAuthenticationCommand(),
            "QUIT\r\n",
        ], $stream->commands);
        $this->assertSame([], $stream->responses);
        $this->assertTrue($stream->initialized);
        $this->assertTrue($stream->terminated);
    }

    public function test_started_transport_without_auth_is_not_considered_authenticated(): void
    {
        [$transport, $stream] = $this->simulatedTransport([
            "220 smtp.example.test ready\r\n",
            "250-smtp.example.test\r\n",
            "250 SIZE 100000\r\n",
            "221 2.0.0 Bye\r\n",
        ]);

        $transport->start();

        $this->assertFalse($transport->supportsAuthentication());
        $transport->stop();
        $this->assertSame(["EHLO [127.0.0.1]\r\n", "QUIT\r\n"], $stream->commands);
        $this->assertSame([], $stream->responses);
    }

    public function test_failed_authentication_still_throws_instead_of_reporting_success(): void
    {
        [$transport, $stream] = $this->simulatedTransport([
            "220 smtp.example.test ready\r\n",
            "250-smtp.example.test\r\n",
            "250 AUTH PLAIN\r\n",
            "535 5.7.8 Authentication failed\r\n",
            "250 2.0.0 Reset\r\n",
        ]);

        try {
            $transport->start();
            $this->fail('SMTP authentication failure must not be accepted.');
        } catch (TransportException $exception) {
            $this->assertSame(535, $exception->getCode());
        }

        $this->assertSame([
            "EHLO [127.0.0.1]\r\n",
            $this->fakeAuthenticationCommand(),
            "RSET\r\n",
        ], $stream->commands);
        $this->assertSame([], $stream->responses);
    }

    private function simulatedTransport(array $responses): array
    {
        $transport = new HostingerSmtpTransport('never-connect.invalid', 465, true);
        $transport->setUsername('test-user@example.test');
        $transport->setPassword('fake-test-password');
        $transport->setAuthenticators([new PlainAuthenticator()]);

        // Replace the private socket before start(): the actual Symfony SMTP
        // handshake runs against scripted responses, with no network access.
        $stream = new class($responses) extends AbstractStream
        {
            public array $commands = [];
            public bool $initialized = false;
            public bool $terminated = false;

            public function __construct(public array $responses) {}

            public function initialize(): void
            {
                $this->initialized = true;
            }

            public function write(string $bytes, bool $debug = true): void
            {
                $this->commands[] = $bytes;
            }

            public function readLine(): string
            {
                if ($this->responses === []) {
                    throw new \RuntimeException('Unexpected extra SMTP response read.');
                }

                return array_shift($this->responses);
            }

            public function isTLS(): bool
            {
                return true;
            }

            public function terminate(): void
            {
                $this->terminated = true;
            }

            protected function getReadConnectionDescription(): string
            {
                return 'simulated-smtp-no-network';
            }
        };

        (new \ReflectionProperty(SmtpTransport::class, 'stream'))->setValue($transport, $stream);

        return [$transport, $stream];
    }

    private function fakeAuthenticationCommand(): string
    {
        return 'AUTH PLAIN '.base64_encode("test-user@example.test\0test-user@example.test\0fake-test-password")."\r\n";
    }
}
