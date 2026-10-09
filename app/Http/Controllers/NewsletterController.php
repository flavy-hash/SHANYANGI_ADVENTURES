<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    protected const SUCCESS = "Thank you! You're on the list.";

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        // Honeypot: bots fill the hidden "website" field. Pretend success, store nothing.
        if (filled($request->input('website'))) {
            return $this->respond($request, self::SUCCESS);
        }

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        if ($validator->fails()) {
            return $this->respond($request, $validator->errors()->first('email'), 422);
        }

        // Same reply for new and existing addresses, so the form can't be used to
        // check who is subscribed. Re-subscribing clears a previous unsubscribe.
        NewsletterSubscriber::updateOrCreate(
            ['email' => mb_strtolower(trim($request->input('email')))],
            ['source' => $request->input('source', 'website'), 'unsubscribed_at' => null],
        );

        return $this->respond($request, self::SUCCESS);
    }

    protected function respond(Request $request, string $message, int $status = 200): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        // Without JavaScript: back to the form, with the message shown there.
        return redirect()->to(url()->previous().'#newsletter')
            ->with($status === 200 ? 'newsletter_success' : 'newsletter_error', $message)
            ->withInput($status === 200 ? [] : $request->only('email'));
    }
}
