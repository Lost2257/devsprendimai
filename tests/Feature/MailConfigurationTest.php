<?php

namespace Tests\Feature;

use Dotenv\Repository\Adapter\ArrayAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Mail;
use ReflectionProperty;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;
use Tests\TestCase;

class MailConfigurationTest extends TestCase
{
    public function test_empty_mail_url_keeps_the_smtp_driver_and_builds_the_actual_runtime_transport(): void
    {
        $mail = $this->mailConfiguration(['MAIL_URL' => '']);

        $this->assertNull($mail['mailers']['smtp']['url']);
        $this->assertSame('smtp', $mail['mailers']['smtp']['transport']);

        $transport = $this->buildRuntimeTransport($mail);

        $this->assertSame('fixture-user', $transport->getUsername());
        $this->assertSame('fixture-password', $transport->getPassword());
        $this->assertSame('smtp.example.test', $transport->getStream()->getHost());
        $this->assertSame(465, $transport->getStream()->getPort());
        $this->assertTrue($transport->getStream()->isTLS());
    }

    public function test_missing_or_dotenv_null_mail_url_uses_the_individual_smtp_settings(): void
    {
        foreach ([[], ['MAIL_URL' => 'null'], ['MAIL_URL' => '(null)']] as $environment) {
            $mail = $this->mailConfiguration($environment);

            $this->assertNull($mail['mailers']['smtp']['url']);

            $transport = $this->buildRuntimeTransport($mail);

            $this->assertSame('smtp.example.test', $transport->getStream()->getHost());
            $this->assertSame('fixture-user', $transport->getUsername());
        }
    }

    public function test_nonempty_mail_url_remains_supported_by_laravels_runtime_mail_manager(): void
    {
        $url = 'smtp://url-user:url-password@smtp-url.example.test:2465';
        $mail = $this->mailConfiguration(['MAIL_URL' => $url]);

        $this->assertSame($url, $mail['mailers']['smtp']['url']);

        $transport = $this->buildRuntimeTransport($mail);

        $this->assertSame('smtp-url.example.test', $transport->getStream()->getHost());
        $this->assertSame(2465, $transport->getStream()->getPort());
        $this->assertSame('url-user', $transport->getUsername());
        $this->assertSame('url-password', $transport->getPassword());
    }

    private function mailConfiguration(array $overrides): array
    {
        // Isolate env() from real credentials without changing .env, $_ENV,
        // $_SERVER, or process environment variables. Restore even on failure.
        $originalRepository = Env::getRepository();
        $repositoryProperty = new ReflectionProperty(Env::class, 'repository');
        $repository = RepositoryBuilder::createWithNoAdapters()
            ->addAdapter(ArrayAdapter::class)
            ->make();

        foreach (array_merge([
            'MAIL_MAILER' => 'smtp',
            'MAIL_SCHEME' => 'smtps',
            'MAIL_HOST' => 'smtp.example.test',
            'MAIL_PORT' => '465',
            'MAIL_USERNAME' => 'fixture-user',
            'MAIL_PASSWORD' => 'fixture-password',
            'MAIL_FROM_ADDRESS' => 'sender@example.test',
            'MAIL_FROM_NAME' => 'Test sender',
            'APP_URL' => 'https://example.test',
        ], $overrides) as $key => $value) {
            $repository->set($key, $value);
        }

        $repositoryProperty->setValue(null, $repository);

        try {
            return require config_path('mail.php');
        } finally {
            $repositoryProperty->setValue(null, $originalRepository);
        }
    }

    private function buildRuntimeTransport(array $mail): EsmtpTransport
    {
        config(['mail' => $mail]);
        Mail::purge('smtp');

        // Resolve through the same manager path used by real application mail.
        // Construction is offline: never call start(), send(), or connect().
        $transport = Mail::mailer()->getSymfonyTransport();

        $this->assertInstanceOf(EsmtpTransport::class, $transport);
        $this->assertInstanceOf(SocketStream::class, $transport->getStream());

        return $transport;
    }
}
