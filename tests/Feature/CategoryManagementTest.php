<?php

declare(strict_types=1);

use App\Domain\Catalog\Models\Category;
use App\Domain\Organization\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Helper untuk setup user dengan permission manage categories.
 *
 * @return array{user: User, organization: Organization}
 */
function categoryUserWithOrganization(): array
{
    $organization = Organization::create([
        'name' => 'Org Kategori',
        'slug' => 'org-kategori',
    ]);
    Session::put('organization_id', $organization->id);
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $user->givePermissionTo(Permission::firstOrCreate([
        'name' => 'manage categories',
        'guard_name' => 'web',
    ]));

    return ['user' => $user, 'organization' => $organization];
}

it('shows the category index page', function () {
    ['user' => $user] = categoryUserWithOrganization();
    $this->actingAs($user)
        ->get(route('admin.categories.index'))
        ->assertOk();
});

it('stores a new category', function () {
    ['user' => $user, 'organization' => $organization] = categoryUserWithOrganization();

    $response = $this->actingAs($user)->post(route('admin.categories.store'), [
        'name' => ['id' => 'Dasar Ziswaf', 'en' => 'Basic Ziswaf'],
        'slug' => 'dasar-ziswaf',
        'sort' => 1,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.categories.index'));
    $this->assertDatabaseHas('categories', ['slug' => 'dasar-ziswaf']);
    $category = Category::withoutGlobalScopes()->where('slug', 'dasar-ziswaf')->first();
    expect($category)->not->toBeNull()
        ->and($category->organization_id)->toBe($organization->id)
        ->and($category->getTranslation('name', 'id'))->toBe('Dasar Ziswaf')
        ->and($category->getTranslation('name', 'en'))->toBe('Basic Ziswaf');
});

it('rejects storing a category with duplicate slug in the same organization', function () {
    ['user' => $user, 'organization' => $organization] = categoryUserWithOrganization();
    Category::create([
        'organization_id' => $organization->id,
        'name' => ['id' => 'Existing'],
        'slug' => 'existing-slug',
    ]);

    $response = $this->actingAs($user)->post(route('admin.categories.store'), [
        'name' => ['id' => 'Duplikat'],
        'slug' => 'existing-slug',
    ]);

    $response->assertSessionHasErrors('slug');
});

it('updates an existing category', function () {
    ['user' => $user, 'organization' => $organization] = categoryUserWithOrganization();
    $category = Category::create([
        'organization_id' => $organization->id,
        'name' => ['id' => 'Lama'],
        'slug' => 'lama',
    ]);

    $response = $this->actingAs($user)->put(route('admin.categories.update', $category->uuid), [
        'name' => ['id' => 'Baru', 'en' => 'New'],
        'slug' => 'baru',
        'sort' => 2,
    ]);

    $response->assertRedirect(route('admin.categories.index'));
    $category->refresh();
    expect($category->getTranslation('name', 'id'))->toBe('Baru')
        ->and($category->slug)->toBe('baru');
});

it('deletes a category', function () {
    ['user' => $user, 'organization' => $organization] = categoryUserWithOrganization();
    $category = Category::create([
        'organization_id' => $organization->id,
        'name' => ['id' => 'Hapus'],
        'slug' => 'hapus',
    ]);

    $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $category->uuid));

    $response->assertRedirect(route('admin.categories.index'));
    $this->assertSoftDeleted('categories', ['id' => $category->id]);
});

it('returns 403 when updating a category without permission', function () {
    $organization = Organization::create(['name' => 'Org', 'slug' => 'org-403']);
    Session::put('organization_id', $organization->id);
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $category = Category::create([
        'organization_id' => $organization->id,
        'name' => ['id' => 'Test'],
        'slug' => 'test-403',
    ]);

    $this->actingAs($user)
        ->put(route('admin.categories.update', $category->uuid), [
            'name' => ['id' => 'Ubah'],
            'slug' => 'ubah',
        ])
        ->assertForbidden();
});

it('returns 404 when updating a category from another organization', function () {
    ['user' => $user] = categoryUserWithOrganization();

    $otherOrg = Organization::create(['name' => 'Lain', 'slug' => 'lain-cat']);
    $otherCategory = Category::create([
        'organization_id' => $otherOrg->id,
        'name' => ['id' => 'Milik Lain'],
        'slug' => 'milik-lain',
    ]);

    $this->actingAs($user)
        ->put(route('admin.categories.update', $otherCategory->uuid), [
            'name' => ['id' => 'Coba Ubah'],
            'slug' => 'coba-ubah',
        ])
        ->assertNotFound();
});
