<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\LegalPages;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use UnitEnum;

/**
 * Edit the privacy policy, terms & conditions and refund policy pages.
 *
 * @property-read Schema $form
 */
class EditLegalPages extends Page
{
    protected static ?string $navigationLabel = 'Legal pages';

    protected static ?string $title = 'Legal pages';

    protected static ?string $slug = 'legal-pages';

    protected static string|UnitEnum|null $navigationGroup = 'Pages';

    protected static ?int $navigationSort = 9;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->form->fill(collect(LegalPages::PAGES)->mapWithKeys(fn ($page, $key) => [$key => ['body' => LegalPages::body($key)]])->all());
    }

    public function form(Schema $schema): Schema
    {
        $tabs = [];
        foreach (LegalPages::PAGES as $key => $page) {
            $tabs[] = Tab::make($page['title'])
                ->statePath($key)
                ->schema([
                    Placeholder::make('info')
                        ->hiddenLabel()
                        ->content(fn () => new HtmlString(
                            'Shown at <a href="'.e(route($page['route'])).'" target="_blank" class="underline font-semibold">'.e(route($page['route'])).'</a> · last updated '.e(LegalPages::updatedOn($key)->format('j F Y')).'.<br>'
                            .'Use <strong>## </strong> for section headings (they also build the "On this page" list). These words are filled in automatically: '
                            .'<code>{company}</code>, <code>{email}</code>, <code>{phone}</code>, <code>{contact_link}</code>, <code>{analytics_days}</code>, <code>{privacy_url}</code>, <code>{terms_url}</code>, <code>{refund_url}</code>.'
                        )),
                    MarkdownEditor::make('body')
                        ->hiddenLabel()
                        ->required()
                        ->toolbarButtons([
                            ['heading', 'bold', 'italic', 'link'],
                            ['bulletList', 'orderedList', 'table'],
                            ['undo', 'redo'],
                        ])
                        ->minHeight('28rem'),
                ]);
        }

        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Legal pages')->tabs($tabs)->persistTabInQueryString(),
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
                    Action::make('save')->label('Save changes')->submit('save')->keyBindings(['mod+s']),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('restore')
                ->label('Restore original text')
                ->icon(Heroicon::OutlinedArrowUturnLeft)
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Replaces your edits on all three pages with the original wording. This cannot be undone.')
                ->action(function () {
                    SiteSetting::put(LegalPages::KEY, []);
                    $this->fillForm();
                    Notification::make()->title('Original text restored')->success()->send();
                }),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $saved = SiteSetting::raw(LegalPages::KEY) ?? [];
        $today = now()->timezone(config('site.timezone'))->toDateString();

        $value = [];
        foreach (array_keys(LegalPages::PAGES) as $key) {
            $body = trim((string) ($state[$key]['body'] ?? ''));
            $unchanged = $body === trim(LegalPages::body($key));

            // Text equal to the default stays "unsaved", so improvements to the default still apply.
            if ($body === trim(LegalPages::defaultBody($key))) {
                continue;
            }

            $value[$key] = [
                'body' => $body,
                'updated_on' => $unchanged ? ($saved[$key]['updated_on'] ?? $today) : $today,
            ];
        }

        SiteSetting::put(LegalPages::KEY, $value);

        Notification::make()->title('Legal pages saved')->success()->send();
    }
}
