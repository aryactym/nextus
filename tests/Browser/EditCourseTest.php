<?php

namespace Tests\Browser;

use App\Domain\Catalog\Models\Course;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EditCourseTest extends DuskTestCase
{
    public function test_edit_course_and_save()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage courses');

        $course = Course::factory()->create([
            'organization_id' => $user->organization_id,
            'title' => ['id' => 'Test Course', 'en' => 'Test Course'],
        ]);

        $this->browse(function (Browser $browser) use ($user, $course) {
            $browser->loginAs($user)
                ->visit("/admin/courses/{$course->id}/edit")
                ->waitForText('Ubah kursus')
                ->type('title[id]', 'Updated Course Title')
                ->click('button[type="submit"]')
                ->waitForNavigation()
                ->assertPathIs('/admin/courses');
        });
    }
}
