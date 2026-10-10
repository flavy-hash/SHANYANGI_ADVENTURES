<?php

namespace Tests\Feature;

use App\Support\ProtectedMedia;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProtectedMediaTest extends TestCase
{
    use RefreshDatabase;

    protected string $root;

    protected string $sessionId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = storage_path('framework/testing/media-'.Str::random(8));
        config(['media.root' => $this->root]);

        File::ensureDirectoryExists("{$this->root}/images");
        File::put("{$this->root}/images/hero-poster.jpg", 'jpeg-bytes');

        File::ensureDirectoryExists("{$this->root}/streams/hero");
        File::put("{$this->root}/streams/hero/enc.key", 'sixteen-byte-key');
        File::put("{$this->root}/streams/hero/seg_000.ts", 'encrypted-segment');
        File::put("{$this->root}/streams/hero/index.m3u8", implode("\n", [
            '#EXTM3U',
            '#EXT-X-KEY:METHOD=AES-128,URI="key",IV=0x00',
            '#EXTINF:4.0,',
            'seg_000.ts',
            '#EXT-X-ENDLIST',
        ]));

        // Media URLs are bound to a session: issue them for a known session id
        // and send that session's cookie with each request.
        $this->sessionId = Str::random(40);
        $this->app['session']->setId($this->sessionId);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->root);

        parent::tearDown();
    }

    protected function fetch(string $url, array $headers = [])
    {
        return $this->withCookie(config('session.cookie'), $this->sessionId)
            ->withHeaders($headers + ['Sec-Fetch-Site' => 'same-origin', 'Sec-Fetch-Dest' => 'image'])
            ->get($url);
    }

    public function test_signed_image_url_is_served_to_the_same_session(): void
    {
        $response = $this->fetch(ProtectedMedia::imageUrl('hero-poster.jpg'))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'inline');

        // Cached copies must not be reused for a direct (document) navigation.
        $this->assertStringContainsString('Sec-Fetch-Dest', $response->headers->get('Vary'));
    }

    public function test_unsigned_or_tampered_urls_are_rejected(): void
    {
        $this->fetch('/media/image/hero-poster.jpg')->assertForbidden();
        $this->fetch(str_replace('hero-poster.jpg', 'other.jpg', ProtectedMedia::imageUrl('hero-poster.jpg')))->assertForbidden();
    }

    public function test_url_from_another_session_is_rejected(): void
    {
        $url = ProtectedMedia::imageUrl('hero-poster.jpg');

        $this->withCookie(config('session.cookie'), Str::random(40))->get($url)->assertForbidden();
    }

    public function test_opening_directly_in_a_tab_or_hotlinking_is_rejected(): void
    {
        $url = ProtectedMedia::imageUrl('hero-poster.jpg');

        $this->fetch($url, ['Sec-Fetch-Dest' => 'document'])->assertForbidden();
        $this->fetch($url, ['Sec-Fetch-Site' => 'cross-site'])->assertForbidden();
    }

    public function test_path_traversal_cannot_reach_the_stream_key(): void
    {
        $url = ProtectedMedia::sign('media.image', ['path' => '../streams/hero/enc.key']);

        $this->fetch($url)->assertNotFound();
    }

    public function test_playlist_points_key_and_segments_at_signed_urls(): void
    {
        $playlist = $this->fetch(ProtectedMedia::streamUrl('hero'), ['Sec-Fetch-Dest' => 'empty'])
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.apple.mpegurl')
            ->getContent();

        preg_match('/URI="([^"]+)"/', $playlist, $key);
        preg_match('/^(\/media\/stream\/hero\/seg_000\.ts\?.+)$/m', $playlist, $segment);

        $this->assertStringContainsString('signature=', $key[1]);
        $this->assertNotEmpty($segment);

        $this->assertSame('sixteen-byte-key', $this->fetch(html_entity_decode($key[1]), ['Sec-Fetch-Dest' => 'empty'])->assertOk()->getContent());
        $this->fetch($segment[1], ['Sec-Fetch-Dest' => 'empty'])->assertOk()->assertHeader('Content-Type', 'video/mp2t');
    }

    public function test_key_is_not_reachable_through_the_segment_route(): void
    {
        $this->get('/media/stream/hero/enc.key')->assertNotFound();
    }

    public function test_home_page_uses_the_protected_stream_and_poster(): void
    {
        $this->withCookie(config('session.cookie'), $this->sessionId)->get('/')
            ->assertOk()
            ->assertSee('data-stream="/media/stream/hero/index.m3u8?', false)
            ->assertSee('/media/image/hero-poster.jpg', false)
            ->assertDontSee('.mp4', false);
    }
}
