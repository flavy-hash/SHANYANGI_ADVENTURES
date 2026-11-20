<?php

namespace App\Filament\Pages;

use App\Filament\Support\MediaUpload;
use App\Models\SiteSetting;
use App\Support\HomePage;
use App\Support\Navigation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

/**
 * Edit the home page hero: headlines, subtitle, buttons, background photo and video.
 *
 * @property-read Schema $form
 */
class EditHomePage extends Page
{
    protected static ?string $navigationLabel = 'Home page';

    protected static ?string $title = 'Home page';

    protected static ?string $slug = 'home-page';

    protected static string|UnitEnum|null $navigationGroup = 'Pages';

    protected static ?int $navigationSort = 0;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->form->fill(HomePage::content());
    }

    /** Button link: a page on this site, a section, or another website. */
    protected static function linkInput(string $name): TextInput
    {
        return TextInput::make($name)
            ->label('Button link')
            ->maxLength(255)
            ->regex(Navigation::PATH_PATTERN)
            ->validationMessages(['regex' => 'Start with "/" for a page on this site, "#" for a section, or https:// for another website.']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Hero headline')
                    ->description('The big text over the video at the top of the home page.')
                    ->columns(2)
                    ->schema([
                        Repeater::make('hero_phrases')
                            ->label('Headlines')
                            ->helperText('Typed out one after another. The longest one is shown first so the page loads smoothly.')
                            ->simple(TextInput::make('text')->required()->maxLength(70))
                            ->minItems(1)
                            ->maxItems(4)
                            ->addActionLabel('Add headline')
                            ->reorderableWithButtons()
                            ->columnSpanFull(),
                        TextInput::make('hero_subtitle')
                            ->label('Subtitle (optional)')
                            ->maxLength(160)
                            ->placeholder('Private safaris, Kilimanjaro climbs and Zanzibar escapes')
                            ->helperText('A short line under the headline. Leave empty to show none.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Buttons')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_cta_label')->label('Main button text')->required()->maxLength(40),
                        static::linkInput('hero_cta_url')->required()->placeholder('/safaris'),
                        TextInput::make('hero_cta2_label')->label('Second button text (optional)')->maxLength(40)->placeholder('Plan My Trip'),
                        static::linkInput('hero_cta2_url')
                            ->placeholder('/contact-us#request')
                            ->required(fn (Get $get) => filled($get('hero_cta2_label'))),
                    ]),

                Section::make('Background')
                    ->columns(2)
                    ->schema([
                        MediaUpload::make('hero_poster', 'home')
                            ->label('Background photo')
                            ->helperText('Shown while the video loads, on slow connections and to visitors who prefer less motion. Leave empty to use the current photo.')
                            ->columnSpanFull(),
                        FileUpload::make('hero_video')
                            ->label('Background video')
                            ->disk('media')
                            ->visibility('private')
                            ->directory('uploads/home')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                            ->maxSize(102400) // KB: 100 MB
                            ->helperText(fn () => 'MP4 works best: 15–40 seconds, landscape, no sound needed (it plays muted). Up to 100 MB. '
                                .(HomePage::canConvertVideos()
                                    ? 'It is converted into an encrypted stream when you save, which can take a minute.'
                                    : 'Your server has no video converter (ffmpeg), so the video plays as uploaded: keep it small (under 15 MB) for fast loading.')
                                .' Remove the uploaded video to go back to the original one.')
                            ->columnSpanFull(),
                        Toggle::make('hero_show_video')
                            ->label('Play the background video')
                            ->helperText('Turn off to show only the photo.')
                            ->default(true),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([$this->getFormContentComponent()]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')->label('Save home page')->submit('save')->keyBindings(['mod+s']),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->label('View home page')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(url('/'), shouldOpenInNewTab: true),
            Action::make('restoreVideo')
                ->label('Restore original video')
                ->icon(Heroicon::OutlinedArrowUturnLeft)
                ->color('gray')
                ->visible(fn () => filled(HomePage::content()['hero_video']))
                ->requiresConfirmation()
                ->modalDescription('Removes the uploaded video and plays the original one again.')
                ->action(function () {
                    $saved = SiteSetting::raw(HomePage::KEY) ?? [];
                    $this->deleteVideo($saved['hero_video'] ?? null, $saved['hero_stream'] ?? null);
                    SiteSetting::put(HomePage::KEY, [...$saved, 'hero_video' => null, 'hero_stream' => null]);
                    $this->fillForm();
                    Notification::make()->title('Original video restored')->success()->send();
                }),
        ];
    }

    protected function deleteVideo(?string $path, ?string $stream): void
    {
        HomePage::deleteStream($stream);
        if ($path && str_starts_with($path, 'uploads/home/')) {
            Storage::disk('media')->delete($path);
        }
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $saved = SiteSetting::raw(HomePage::KEY) ?? [];

        $state['hero_phrases'] = array_values(array_filter(array_map(fn ($phrase) => trim((string) $phrase), $state['hero_phrases'] ?? [])));

        // A new video: replace the old one and try to turn it into an encrypted stream.
        $video = $state['hero_video'] ?? null;
        $oldVideo = $saved['hero_video'] ?? null;
        $state['hero_stream'] = $saved['hero_stream'] ?? null;
        $message = 'The website shows the changes straight away.';

        if ($video !== $oldVideo) {
            $this->deleteVideo($oldVideo, $state['hero_stream']);
            $state['hero_stream'] = null;

            if ($video) {
                $state['hero_stream'] = HomePage::convertVideo($video);
                $message = $state['hero_stream']
                    ? 'The new video was converted into an encrypted stream and is live.'
                    : 'The new video is live. It plays as uploaded because the server cannot convert videos.';
            }
        }

        SiteSetting::put(HomePage::KEY, $state);
        $this->fillForm();

        Notification::make()->title('Home page saved')->body($message)->success()->send();
    }
}
