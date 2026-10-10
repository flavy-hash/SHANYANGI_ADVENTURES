<?php

namespace Tests\Feature;

use App\Filament\Widgets\ActivityTrendChart;
use App\Filament\Widgets\InquiryBudgetChart;
use App\Filament\Widgets\InquiryInterestsChart;
use App\Filament\Widgets\LatestInquiries;
use App\Filament\Widgets\PackagesByCategoryChart;
use App\Filament\Widgets\ReviewRatingsChart;
use App\Filament\Widgets\SiteOverview;
use App\Models\NewsletterSubscriber;
use App\Models\TripRequest;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use ReflectionMethod;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);
        $this->actingAs(User::factory()->create());
    }

    /**
     * Call a widget's protected getData() to check the numbers behind a chart.
     */
    protected function chartData(string $widget, array $props = []): array
    {
        $instance = new $widget;
        foreach ($props as $key => $value) {
            $instance->{$key} = $value;
        }

        return (new ReflectionMethod($instance, 'getData'))->invoke($instance);
    }

    protected function inquiry(array $attributes = []): TripRequest
    {
        return TripRequest::create($attributes + ['name' => 'Guest', 'email' => 'guest@example.com', 'adults' => 2, 'children' => 0]);
    }

    public function test_dashboard_and_every_widget_render(): void
    {
        $this->inquiry(['interests' => ['safari'], 'budget' => 'luxury']);

        $this->get('/admin')->assertOk();

        foreach ([SiteOverview::class, ActivityTrendChart::class, InquiryInterestsChart::class, InquiryBudgetChart::class,
            PackagesByCategoryChart::class, ReviewRatingsChart::class, LatestInquiries::class] as $widget) {
            Livewire::test($widget)->assertOk();
        }
    }

    public function test_widgets_render_with_no_data(): void
    {
        foreach ([ActivityTrendChart::class, InquiryInterestsChart::class, InquiryBudgetChart::class, LatestInquiries::class] as $widget) {
            Livewire::test($widget)->assertOk();
        }
    }

    public function test_trend_chart_counts_inquiries_and_signups_per_month(): void
    {
        $this->inquiry()->forceFill(['created_at' => now()->subMonths(2)])->save();
        $this->inquiry();
        $this->inquiry();
        NewsletterSubscriber::create(['email' => 'a@example.com']);

        $data = $this->chartData(ActivityTrendChart::class, ['filter' => '12m']);

        $this->assertCount(12, $data['labels']);
        $this->assertSame(now()->format('M Y'), end($data['labels']));
        $this->assertSame(2, end($data['datasets'][0]['data']));   // inquiries this month
        $this->assertSame(1, $data['datasets'][0]['data'][9]);    // two months ago
        $this->assertSame(1, end($data['datasets'][1]['data']));   // sign-ups this month

        $this->assertCount(30, $this->chartData(ActivityTrendChart::class, ['filter' => '30d'])['labels']);
    }

    public function test_interest_and_budget_charts_count_inquiries(): void
    {
        $this->inquiry(['interests' => ['safari', 'zanzibar'], 'budget' => 'luxury']);
        $this->inquiry(['interests' => ['safari'], 'budget' => null]);

        $interests = $this->chartData(InquiryInterestsChart::class);
        $byLabel = array_combine($interests['labels'], $interests['datasets'][0]['data']);
        $this->assertSame(2, $byLabel['Safari']);
        $this->assertSame(1, $byLabel['Zanzibar']);
        $this->assertSame(0, $byLabel['Kilimanjaro']);

        $budgets = $this->chartData(InquiryBudgetChart::class);
        $byBudget = array_combine($budgets['labels'], $budgets['datasets'][0]['data']);
        $this->assertSame(1, $byBudget['Luxury']);
        $this->assertSame(1, $byBudget['Not given']);
    }

    public function test_review_chart_ignores_sample_reviews(): void
    {
        // Seeded reviews are all samples.
        $this->assertSame([0, 0, 0, 0, 0], $this->chartData(ReviewRatingsChart::class)['datasets'][0]['data']);
    }

    public function test_package_chart_counts_published_packages_by_category(): void
    {
        $data = $this->chartData(PackagesByCategoryChart::class);

        $this->assertSame(8, array_sum($data['datasets'][0]['data']));
        $this->assertContains('Kilimanjaro', $data['labels']);
    }
}
