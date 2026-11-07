<?php

declare(strict_types=1);

use App\Domain\Admin\Models\Page;
use App\Domain\Organization\Models\Organization;
use App\Models\User;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PageSeeder::class);
});

function makePageUser(string $slug): User
{
    $organization = Organization::create([
        'name' => 'Org '.$slug,
        'slug' => 'org-'.$slug,
    ]);
    Session::put('organization_id', $organization->id);

    return User::factory()->create(['organization_id' => $organization->id]);
}

function pageManager(): User
{
    $user = makePageUser('halaman');
    $user->givePermissionTo(Permission::firstOrCreate([
        'name' => 'manage pages',
        'guard_name' => 'web',
    ]));

    return $user;
}

it('shows the page index to an authorized admin', function () {
    $this->actingAs(pageManager())
        ->get(route('admin.pages.index'))
        ->assertOk()
        ->assertSee('Syarat dan ketentuan')
        ->assertSee('Kebijakan privasi');
});

it('filters pages by keyword and publication status', function () {
    Page::create([
        'slug' => 'panduan',
        'title' => 'Panduan komunitas',
        'content' => '<p>Panduan.</p>',
        'is_published' => false,
    ]);

    $user = pageManager();

    $this->actingAs($user)
        ->get(route('admin.pages.index', ['q' => 'syarat']))
        ->assertOk()
        ->assertSee('Syarat dan ketentuan')
        ->assertDontSee('Kebijakan privasi');

    $this->actingAs($user)
        ->get(route('admin.pages.index', ['status' => 'draft']))
        ->assertOk()
        ->assertSee('Panduan komunitas')
        ->assertDontSee('Syarat dan ketentuan');

    $this->actingAs($user)
        ->get(route('admin.pages.index', ['status' => 'published']))
        ->assertOk()
        ->assertSee('Syarat dan ketentuan')
        ->assertDontSee('Panduan komunitas');
});

it('updates page content and reflects it on the public page', function () {
    $page = Page::query()->where('slug', 'syarat')->firstOrFail();

    $this->actingAs(pageManager())
        ->put(route('admin.pages.update', $page->uuid), [
            'title' => 'Syarat layanan',
            'meta_description' => 'Ketentuan terbaru.',
            'content' => '<h2>Ketentuan baru</h2><p>Isi syarat yang sudah diperbarui.</p>',
            'is_published' => '1',
        ])
        ->assertRedirect(route('admin.pages.index'));

    $page->refresh();
    expect($page->title)->toBe('Syarat layanan')
        ->and($page->content)->toContain('Ketentuan baru');

    $this->get(route('syarat'))
        ->assertOk()
        ->assertSee('Syarat layanan')
        ->assertSee('Ketentuan baru')
        ->assertSee('Isi syarat yang sudah diperbarui.');
});

it('hides an unpublished page from the public', function () {
    $page = Page::query()->where('slug', 'privasi')->firstOrFail();

    $this->actingAs(pageManager())
        ->put(route('admin.pages.update', $page->uuid), [
            'title' => $page->title,
            'meta_description' => $page->meta_description,
            'content' => $page->content,
        ])
        ->assertRedirect(route('admin.pages.index'));

    $this->get(route('privasi'))->assertNotFound();
});

it('forbids page management without permission', function () {
    $this->actingAs(makePageUser('pengguna'))
        ->get(route('admin.pages.index'))
        ->assertForbidden();
});

it('lets a super admin manage pages without an explicit permission', function () {
    $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    $user = makePageUser('super-admin');
    $user->assignRole($role);

    $this->actingAs($user)
        ->get(route('admin.pages.index'))
        ->assertOk();
});
