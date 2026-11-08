<?php

namespace Tests\Feature;

use App\Domain\Catalog\Models\Course;
use App\Models\User;
use Tests\TestCase;

class EditCourseRequirementTest extends TestCase
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
            $course->requirement()->forceCreate([
                'prerequisite_course_id' => null,
                'min_grade' => null,
                'pendidikan_id' => 2,
                'min_age' => null,
                'max_age' => null,
                'notes' => 'Test requirement',
            ]);
        }

        $originalRequirementId = $course->requirement->id ?? null;
        $this->assertNotNull($originalRequirementId);

        // Edit course TANPA mengubah requirement field
        $response = $this->actingAs($user)
            ->put(route('admin.courses.update', $course->uuid), [
                'title' => ['id' => $course->getTranslation('title', 'id'), 'en' => $course->getTranslation('title', 'en')],
                'slug' => $course->slug,
                'category_id' => $course->category_id,
                'level' => $course->level,
                'duration_minutes' => $course->duration_minutes,
                'summary' => $course->getTranslations('summary'),
                'description' => $course->getTranslations('description'),
                'status' => $course->status,
                'access_type' => $course->access_type,
                'certificate_type' => $course->certificate_type,
                'passing_grade' => $course->passing_grade,
                // TIDAK KIRIM requirement field
            ]);

        $response->assertRedirect(route('admin.courses.index'))
                ->assertSessionHas('success', 'Kursus berhasil diperbarui.');

        // Verifikasi requirement masih ada dengan data sama
        $course->refresh();
        $this->assertNotNull($course->requirement);
        $this->assertEquals($originalRequirementId, $course->requirement->id);
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

        // Edit course & ubah requirement field
        $response = $this->actingAs($user)
            ->put(route('admin.courses.update', $course->uuid), [
                'title' => ['id' => $course->getTranslation('title', 'id'), 'en' => $course->getTranslation('title', 'en')],
                'slug' => $course->slug,
                'category_id' => $course->category_id,
                'level' => $course->level,
                'duration_minutes' => $course->duration_minutes,
                'summary' => $course->getTranslations('summary'),
                'description' => $course->getTranslations('description'),
                'status' => $course->status,
                'access_type' => $course->access_type,
                'certificate_type' => $course->certificate_type,
                'passing_grade' => $course->passing_grade,
                // KIRIM requirement field
                'prerequisite_course_id' => '',
                'min_grade' => '',
                'pendidikan_id' => 3,
                'min_age' => '',
                'max_age' => '',
                'notes' => 'Memerlukan pendidikan minimal S1',
            ]);

        $response->assertRedirect(route('admin.courses.index'))
                ->assertSessionHas('success', 'Kursus berhasil diperbarui.');

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

        // Edit course dengan checkbox "clear_requirement" di-cek
        $response = $this->actingAs($user)
            ->put(route('admin.courses.update', $course->uuid), [
                'title' => ['id' => $course->getTranslation('title', 'id'), 'en' => $course->getTranslation('title', 'en')],
                'slug' => $course->slug,
                'category_id' => $course->category_id,
                'level' => $course->level,
                'duration_minutes' => $course->duration_minutes,
                'summary' => $course->getTranslations('summary'),
                'description' => $course->getTranslations('description'),
                'status' => $course->status,
                'access_type' => $course->access_type,
                'certificate_type' => $course->certificate_type,
                'passing_grade' => $course->passing_grade,
                // Checkbox clear_requirement di-cek (kirim sebagai string '1')
                'clear_requirement' => '1',
            ]);

        $response->assertRedirect(route('admin.courses.index'))
                ->assertSessionHas('success', 'Kursus berhasil diperbarui.');

        // Verifikasi requirement sudah dihapus (soft delete)
        $course->refresh();
        $this->assertNull($course->requirement);
    }
}
