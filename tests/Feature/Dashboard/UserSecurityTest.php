<?php

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['super-user', 'admin', 'user'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    private function account(string $role): User
    {
        $user = User::factory()->create(['website_password' => 'client-secret-fixture']);
        $user->assignRole($role);

        return $user;
    }

    public function test_client_credentials_are_encrypted_and_not_shared_or_listed(): void
    {
        $admin = $this->account('admin');
        $stored = DB::table('users')->where('id', $admin->id)->value('website_password');
        $this->assertNotSame('client-secret-fixture', $stored);
        $this->assertSame('client-secret-fixture', Crypt::decryptString($stored));
        $this->assertArrayNotHasKey('website_password', $admin->toArray());

        $this->actingAs($admin)->get('/dashboard/users')->assertInertia(fn (Assert $page) => $page
            ->missing('auth.user.website_password')->missing('auth.user.website_login')
            ->missing('users.0.website_password'));
        $this->get('/dashboard/users/'.$admin->id.'/edit')->assertInertia(fn (Assert $page) => $page
            ->missing('user.website_password'));
        $this->get('/dashboard/users/'.$admin->id)->assertRedirect('/confirm-password');
        $this->withSession(['auth.password_confirmed_at' => time()])->get('/dashboard/users/'.$admin->id)
            ->assertInertia(fn (Assert $page) => $page->where('user.website_password', 'client-secret-fixture'));
    }

    public function test_admin_cannot_promote_themselves_or_modify_an_owner(): void
    {
        $admin = $this->account('admin');
        $owner = $this->account('super-user');
        $this->actingAs($admin)->put('/dashboard/users/'.$admin->id, [
            'name' => $admin->name, 'email' => $admin->email, 'role' => 'super-user',
        ])->assertSessionHasErrors('role');
        $this->get('/dashboard/users/'.$owner->id.'/edit')->assertForbidden();
        $this->delete('/dashboard/users/'.$owner->id)->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $owner->id]);
    }

    public function test_blank_secret_preserves_it_and_last_owner_cannot_be_demoted(): void
    {
        $owner = $this->account('super-user');
        $this->actingAs($owner)->put('/dashboard/users/'.$owner->id, [
            'name' => $owner->name, 'email' => $owner->email, 'role' => 'super-user', 'website_password' => '',
        ])->assertRedirect('/dashboard/users');
        $this->assertSame('client-secret-fixture', $owner->fresh()->website_password);
        $this->put('/dashboard/users/'.$owner->id, [
            'name' => $owner->name, 'email' => $owner->email, 'role' => 'user',
        ])->assertForbidden();
    }

    public function test_legacy_credentials_migrate_without_losing_their_value(): void
    {
        $user = $this->account('user');
        DB::table('users')->where('id', $user->id)->update(['website_password' => 'legacy-fixture']);
        $migration = require database_path('migrations/2026_10_15_000001_encrypt_client_credentials.php');
        $migration->up();
        $migration->up();
        $this->assertSame('legacy-fixture', $user->fresh()->website_password);
    }

    public function test_private_history_is_encrypted_and_cleared_on_logout(): void
    {
        $admin = $this->account('admin');
        $this->actingAs($admin)->get('/dashboard/users')->assertViewHas('page', fn ($page) => $page['encryptHistory'] === true);
        $this->post('/logout')->assertRedirect('/');
        $this->get('/')->assertViewHas('page', fn ($page) => $page['clearHistory'] === true && $page['encryptHistory'] === false);
    }
}
