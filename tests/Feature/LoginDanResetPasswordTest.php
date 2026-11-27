<?php

namespace Tests\Feature;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LoginDanResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * State factory User dan nama route dashboard yang diharapkan.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function roleDanDashboard(): array
    {
        return [
            'super_admin' => ['superAdmin', 'admin.dashboard'],
            'admin jenjang' => ['adminJenjang', 'admin.dashboard.jenjang'],
            'pengurus' => ['pengurus', 'admin.dashboard.pengurus'],
            'pembina' => ['pembina', 'admin.dashboard.pembina'],
            'wali santri' => ['waliSantri', 'portal.dashboard'],
            'alumni' => ['alumni', 'alumni.dashboard'],
        ];
    }

    #[DataProvider('roleDanDashboard')]
    public function test_login_berhasil_mengarahkan_ke_dashboard_per_role(
        string $stateFactory,
        string $ruteDashboard
    ): void {
        $user = User::factory()->{$stateFactory}()->create();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'agree' => '1',
        ])->assertRedirect(route($ruteDashboard));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_login_menolak_kata_sandi_salah(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'sandi-salah',
                'agree' => '1',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_menolak_persetujuan_syarat_yang_belum_dicentang(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('agree');

        $this->assertGuest();
    }

    public function test_akun_nonaktif_tidak_boleh_masuk(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'agree' => '1',
        ])->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->assertStringContainsString('dinonaktifkan', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_password_sementara_mewajibkan_ganti_password(): void
    {
        $user = User::factory()->adminJenjang()->create([
            'must_change_password' => true,
            'temp_password_expires_at' => now()->addDay(),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'agree' => '1',
        ])->assertRedirect(route('password.change'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_password_sementara_kedaluwarsa_ditolak(): void
    {
        $user = User::factory()->create([
            'must_change_password' => true,
            'temp_password_expires_at' => now()->subDay(),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'agree' => '1',
        ])->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_ganti_password_sementara_berhasil(): void
    {
        $user = User::factory()->superAdmin()->create([
            'must_change_password' => true,
            'temp_password_expires_at' => now()->addDay(),
        ]);

        $this->actingAs($user)
            ->get(route('password.change'))
            ->assertOk();

        $this->actingAs($user)
            ->put(route('password.change.update'), [
                'password_lama' => 'password',
                'password' => 'sandi-baru-123',
                'password_confirmation' => 'sandi-baru-123',
            ])
            ->assertRedirect(route('admin.dashboard'));

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertNull($user->temp_password_expires_at);
        $this->assertTrue(Hash::check('sandi-baru-123', $user->password));
    }

    public function test_permintaan_reset_mengirim_email_ke_akun_terdaftar(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Mail::assertSent(
            ResetPasswordMail::class,
            fn (ResetPasswordMail $mail) => $mail->hasTo($user->email)
        );

        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_permintaan_reset_email_tidak_terdaftar_tetap_memberi_pesan_sama(): void
    {
        Mail::fake();

        $this->post(route('password.email'), ['email' => 'tidak-terdaftar@example.test'])
            ->assertSessionHas(
                'status',
                fn ($pesan) => str_contains($pesan, 'Jika email tersebut terdaftar')
            );

        Mail::assertNothingSent();
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'tidak-terdaftar@example.test',
        ]);
    }

    public function test_reset_password_memakai_token_dari_email_secara_sekali_pakai(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email]);

        $token = null;
        Mail::assertSent(ResetPasswordMail::class, function (ResetPasswordMail $mail) use (&$token) {
            $token = $mail->token;

            return $mail->hasTo($mail->user->email);
        });

        $this->assertNotNull($token);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'kata-sandi-baru',
            'password_confirmation' => 'kata-sandi-baru',
        ])->assertRedirect(route('login'))->assertSessionHas('status');

        $this->assertTrue(Hash::check('kata-sandi-baru', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_token_reset_yang_salah_ditolak(): void
    {
        $user = User::factory()->create();
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make('token-asli'),
            'created_at' => now(),
        ]);

        $this->post(route('password.store'), [
            'token' => 'token-salah',
            'email' => $user->email,
            'password' => 'kata-sandi-baru',
            'password_confirmation' => 'kata-sandi-baru',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_logout_mengakhiri_sesi(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
