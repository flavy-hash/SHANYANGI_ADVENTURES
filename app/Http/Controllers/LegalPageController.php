<?php

namespace App\Http\Controllers;

use App\Support\LegalPages;
use Illuminate\View\View;

/**
 * Privacy policy, terms & conditions and refund policy (text edited in Pages → Legal pages).
 */
class LegalPageController extends Controller
{
    public function privacy(): View
    {
        return $this->show('privacy');
    }

    public function terms(): View
    {
        return $this->show('terms');
    }

    public function refund(): View
    {
        return $this->show('refund');
    }

    protected function show(string $page): View
    {
        return view('pages.legal', [
            'key' => $page,
            'page' => LegalPages::PAGES[$page],
            'content' => LegalPages::render($page),
            'updatedOn' => LegalPages::updatedOn($page),
        ]);
    }
}
