<?php

declare(strict_types=1);

namespace App\Domain\Admin\Http\Controllers;

use App\Domain\Admin\Models\Page;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function terms(): View
    {
        return $this->render('syarat');
    }

    public function privacy(): View
    {
        return $this->render('privasi');
    }

    private function render(string $slug): View
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        [$content, $toc] = $this->prepareContent($page->content);

        return view('pages.show', compact('page', 'content', 'toc'));
    }

    /**
     * @return array{0: string, 1: array<int, array{id: string, label: string}>}
     */
    private function prepareContent(?string $content): array
    {
        if (blank($content)) {
            return ['', []];
        }

        $toc = [];

        $prepared = preg_replace_callback(
            '/<h2\b[^>]*>(.*?)<\/h2>/is',
            function (array $matches) use (&$toc): string {
                $id = 'bagian-'.(count($toc) + 1);
                $label = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($matches[1]))));
                $toc[] = ['id' => $id, 'label' => $label];

                return '<h2 id="'.$id.'">'.$matches[1].'</h2>';
            },
            $content,
        );

        return [$prepared ?? $content, $toc];
    }
}
