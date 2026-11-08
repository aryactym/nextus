<?php

namespace Tests\Browser;

use App\Domain\Catalog\Models\Course;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EditCourseRequirementTest extends DuskTestCase
{
    /**
     * Test: Edit course tanpa ubah requirement, requirement tetap ada
     */
    public function test_edit_course_without_changing_requirement_preserves_requirement()
    {
        $user = User::where('email', 'admin@example.com')->first();
        $course = Course::find(1); // Pengantar Zakat
        
        // Pastikan course punya requirement dulu
        if (!$course->requirement) {
            $course->requirement()->create([
                'prerequisite_course_id' => null,
                'min_grade' => null,
                'pendidikan_id' => 2,
                'min_age' => null,
                'max_age' => null,
                'notes' => 'Test requirement',
            ]);
        }

        $this->browse(function (Browser $browser) use ($user, $course) {
            $browser->loginAs($user)
                    ->visit('/admin/courses/' . $course->uuid . '/edit')
                    ->assertSee('Syarat kelulusan')
                    // Jangan ubah apa pun, cukup scroll & click simpan
                    ->scrollIntoView('@save-button')
                    ->click('button[type="submit"]')
                    // Tunggu redirect ke courses index
                    ->waitForLocation('/admin/courses')
                    ->assertSee('berhasil diperbarui');
        });

        // Verifikasi requirement masih ada
        $course->refresh();
        $this->assertNotNull($course->requirement);
        $this->assertEquals(2, $course->requirement->pendidikan_id);
        $this->assertEquals('Test requirement', $course->requirement->notes);
    }

    /**
     * Test: Edit course & ubah requirement, requirement berubah
     */
    public function test_edit_course_changing_requirement_updates_requirement()
    {
        $user = User::where('email', 'admin@example.com')->first();
        $course = Course::find(2); // Dasar Pelaporan Program

        $this->browse(function (Browser $browser) use ($user, $course) {
            $browser->loginAs($user)
                    ->visit('/admin/courses/' . $course->uuid . '/edit')
                    ->assertSee('Syarat kelulusan')
                    // Isi pendidikan minimal
                    ->select('pendidikan_id', '3')
                    ->type('notes', 'Memerlukan pendidikan minimal S1')
                    ->scrollIntoView('@save-button')
                    ->click('button[type="submit"]')
                    ->waitForLocation('/admin/courses')
                    ->assertSee('berhasil diperbarui');
        });

        // Verifikasi requirement ter-update
        $course->refresh();
        $this->assertNotNull($course->requirement);
        $this->assertEquals(3, $course->requirement->pendidikan_id);
        $this->assertEquals('Memerlukan pendidikan minimal S1', $course->requirement->notes);
    }

    /**
     * Test: Edit course & hapus requirement via checkbox
     */
    public function test_edit_course_clear_requirement_checkbox_deletes_requirement()
    {
        $user = User::where('email', 'admin@example.com')->first();
        $course = Course::find(3); // Dasar Fasilitasi

        // Buat requirement dulu
        if (!$course->requirement) {
            $course->requirement()->create([
                'prerequisite_course_id' => null,
                'min_grade' => 80,
                'pendidikan_id' => null,
                'min_age' => null,
                'max_age' => null,
                'notes' => null,
            ]);
        }

        $this->browse(function (Browser $browser) use ($user, $course) {
            $browser->loginAs($user)
                    ->visit('/admin/courses/' . $course->uuid . '/edit')
                    ->assertSee('Syarat kelulusan')
                    // Scroll ke requirement card
                    ->scrollIntoView('@requirement-card')
                    // Check checkbox "Hapus semua syarat kelulusan"
                    ->check('clear_requirement')
                    ->scrollIntoView('@save-button')
                    ->click('button[type="submit"]')
                    ->waitForLocation('/admin/courses')
                    ->assertSee('berhasil diperbarui');
        });

        // Verifikasi requirement sudah dihapus (soft delete)
        $course->refresh();
        $this->assertNull($course->requirement);
    }
}
