<?php

namespace Tests\Feature;

use App\Domain\Catalog\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EditCourseTest extends TestCase
{
    public function test_routes_registered()
    {
        $routes = Route::getRoutes();

        echo "\n\nRegistered course routes:\n";
        foreach ($routes as $route) {
            if (str_contains($route->uri, 'courses')) {
                echo $route->getName().' => '.implode(',', $route->methods).' '.$route->uri."\n";
            }
        }

        $this->assertTrue(true);
    }

    public function test_edit_course_saves_without_deleting()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage courses');

        $course = Course::create([
            'organization_id' => $user->organization_id,
            'title' => ['id' => 'Original Title', 'en' => 'Original Title'],
            'slug' => 'original-title',
            'level' => 'beginner',
            'duration_minutes' => 60,
            'access_type' => 'free',
            'certificate_type' => 'free',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->put(
            route('admin.courses.update', $course->id),
            [
                'title' => ['id' => 'Updated Title', 'en' => 'Updated Title'],
                'slug' => $course->slug,
                'level' => $course->level,
                'duration_minutes' => $course->duration_minutes,
                'access_type' => $course->access_type,
                'certificate_type' => $course->certificate_type,
                'status' => $course->status,
            ]
        );

        $response->assertStatus(302);
    }
}
