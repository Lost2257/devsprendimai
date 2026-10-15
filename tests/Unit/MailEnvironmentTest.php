<?php

namespace Tests\Unit;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../deployment/mail-environment.php';

class MailEnvironmentTest extends TestCase
{
    public function test_passwords_round_trip_without_interpolation_or_losing_other_settings(): void
    {
        $original = "# Keep this comment\r\nAPP_NAME=DevSprendimai\r\nAPP_KEY=original\r\nMAIL_MAILER=log\r\nMAIL_PASSWORD=null\r\nMAIL_URL=smtp://old.example\r\nMAIL_PASSWORD=duplicate\r\n";
        foreach (['normal-pass', 'quo"te', "apost'rophe", 'back\\slash', '${APP_KEY}$literal', ' spaces # = ; ! ', 'null', 'false', '(true)', 'empty', '"wrapped"', "'wrapped'"] as $password) {
            $result = hostingerMailEnvironment($original, $password);
            $parsed = Dotenv::parse($result);
            $this->assertSame('"'.$password.'"', $parsed['MAIL_PASSWORD']);
            $_ENV['DEVSPRENDIMAI_SMTP_TEST_PASSWORD'] = $parsed['MAIL_PASSWORD'];
            try {
                $this->assertSame($password, \Illuminate\Support\Env::get('DEVSPRENDIMAI_SMTP_TEST_PASSWORD'));
            } finally {
                unset($_ENV['DEVSPRENDIMAI_SMTP_TEST_PASSWORD']);
            }
            $this->assertSame('original', $parsed['APP_KEY']);
            $this->assertSame('smtp', $parsed['MAIL_MAILER']);
            $this->assertSame('smtps', $parsed['MAIL_SCHEME']);
            $this->assertSame('', $parsed['MAIL_URL']);
            $this->assertSame('465', $parsed['MAIL_PORT']);
            $this->assertSame('info@devsprendimai.lt', $parsed['MAIL_USERNAME']);
            $this->assertStringContainsString("# Keep this comment\r\n", $result);
            $this->assertSame(1, substr_count($result, 'MAIL_PASSWORD='));
            $this->assertSame($result, hostingerMailEnvironment($result, $password));
        }
    }

    public function test_rejects_empty_multiline_or_overlong_passwords_without_echoing_them(): void
    {
        foreach (['', "secret\nINJECT=value", "secret\rvalue", "secret\0value", str_repeat('x', 1025)] as $password) {
            try {
                hostingerMailEnvironment('APP_KEY=original', $password);
                $this->fail('Invalid password was accepted.');
            } catch (\RuntimeException $exception) {
                $this->assertSame('Invalid mailbox password input.', $exception->getMessage());
            }
        }
    }

    public function test_dotenv_parser_errors_do_not_leak_configuration(): void
    {
        $this->expectExceptionMessage('Environment configuration validation failed.');
        hostingerMailEnvironment('SECRET="unclosed-secret', 'not-a-real-password');
    }
}
