<?php

namespace App\Filament\Pages;

use App\Models\Company;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Actions\Action;
use App\Models\SubscriptionPlan;
use Laravel\Cashier\Checkout;


class ProfileSetting extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-s-user-circle';
    protected static ?string $title = 'Profile Settings';
    protected static string $view = 'filament.pages.profile-setting';
    protected static ?string $navigationGroup = 'Account';


    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $company = Company::where('user_id', $user->id)->first();

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'contact_number' => $user->contact_number,
            'country' => $user->country,
            'image' => $user->image,
            'company' => [
                'company_name' => $company?->name,
                'company_country' => $company?->country,
                'company_no' => $company?->company_no,
                'staff_invitation_link' => $company?->staff_invitation_link,
                'company_logo' => $company?->company_logo,
                'subscription_plan_id' => $company?->subscription_plan_id,
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make('Profile Information')
                            ->schema([
                                Grid::make(1)->schema([
                                    TextInput::make('name')->required()->label('Full Name'),
                                    TextInput::make('email')->email()->required()->label('Email'),
                                    TextInput::make('contact_number')->label('Contact Number'),
                                    TextInput::make('country')->label('Country'),
                                    FileUpload::make('image')
                                        ->image()
                                        ->directory('profile-images')
                                        ->disk('public')
                                        ->visibility('public')
                                        ->label('Profile Image')
                                        ->imagePreviewHeight('150')
                                        ->preserveFilenames(),
                                ]),
                            ])
                            ->columnSpan(1),

                       Section::make()
                       ->schema([
 Section::make('Company Information')
                            ->schema([
                                Grid::make(1)->schema([
                                TextInput::make('company.company_name')
                                    ->label('Company Name')
                                    ->required(),

                                TextInput::make('company.company_country')
                                    ->label('Company Country')
                                    ->required(),

                                // TextInput::make('company.company_no')
                                //     ->label('Company No')
                                //     ->disabled()
                                //     ->dehydrated(false),

                                // TextInput::make('company.staff_invitation_link')
                                //     ->label('Staff Invitation Link')
                                //     ->disabled()
                                //     ->dehydrated(false),

                                FileUpload::make('company.company_logo')
                                    ->image()
                                    ->directory('company-logos')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->label('Company Logo')
                                    ->imagePreviewHeight('150')
                                    ->preserveFilenames(),
                            ])
                             ])
                            ->columnSpan(1),


                            
                        Section::make('Subscriptions')
                                ->schema([
                                    Grid::make(1)->schema([
                                        Select::make('company.subscription_plan_id')
                                            ->label('Subscription Plans')
                                            ->options(
                                                SubscriptionPlan::pluck('name', 'id')
                                            )
                                            ->disabled(fn() => auth()->user()->company?->is_subscribed),
                                    ]),
                                ])
                                ->headerActions([
                                    Forms\Components\Actions\Action::make('cancelSubscription')
                                        ->label('Cancel Subscription')
                                        ->icon('heroicon-o-x-circle')
                                        ->color('danger')
                                        ->requiresConfirmation()
                                        ->modalHeading('Cancel Subscription?')
                                        ->modalSubheading('Are you sure you want to cancel your active subscription? This will deactivate access immediately.')
                                        ->modalButton('Yes, Cancel It')
                                        ->visible(fn() => auth()->user()->company?->is_subscribed)
                                        ->action(function () {
                                            $user = auth()->user();
                                            $company = $user->company;

                                            if ($company && $company->is_subscribed && $company->subscription_plan_id) {
                                                try {
                                                    // Cancel the Stripe subscription
                                                    if ($user->subscribed('default')) {
                                                        $user->subscription('default')->cancel();
                                                    }

                                                    // Update company table
                                                    $company->update([
                                                        'is_subscribed' => 0,
                                                        'subscription_plan_id' => null,
                                                    ]);

                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Subscription Cancelled')
                                                        ->success()
                                                        ->body('Your subscription has been cancelled successfully.')
                                                        ->send();

                                                } catch (\Exception $e) {
                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Stripe Error')
                                                        ->danger()
                                                        ->body('Unable to cancel subscription: ' . $e->getMessage())
                                                        ->send();
                                                }
                                            } else {
                                                \Filament\Notifications\Notification::make()
                                                    ->title('No Active Subscription')
                                                    ->warning()
                                                    ->body('You do not have an active subscription to cancel.')
                                                    ->send();
                                            }
                                        }),
                                ])
                                ->columnSpan(1),

                       ])->columnSpan(1)
                    ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;']),
                    ]),
            ])
            ->statePath('data');
    }


public function getActions(): array
{
    return []; // ❌ This disables the top-right actions
}


public function saveAll(): void
{
    $user = Auth::user();
    $state = $this->form->getState();

    // Save user info
    $user->update([
        'name' => $state['name'],
        'email' => $state['email'],
        'contact_number' => $state['contact_number'],
        'country' => $state['country'],
        'image' => $state['image'],
    ]);

    // Save or create company
    $company = Company::where('user_id', $user->id)->first();

    if ($company) {
        $company->update([
            'name' => $state['company']['company_name'],
            'country' => $state['company']['company_country'],
            'subscription_plan_id' => $state['company']['subscription_plan_id'],
            'is_subscribed' => !empty($state['company']['subscription_plan_id']),
            'company_logo' => $state['company']['company_logo'],
        ]);
    } else {
        $companyNo = 'CN#' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        $company = Company::create([
            'company_no' => $companyNo,
            'user_id' => $user->id,
            'name' => $state['company']['company_name'],
            'country' => $state['company']['company_country'],
            'subscription_plan_id' => $state['company']['subscription_plan_id'],
            'company_logo' => $state['company']['company_logo'],
            'is_subscribed' => !empty($state['company']['subscription_plan_id']),
            'staff_invitation_link' => $this->generateStaffInvitationLink(
                $state['company']['company_name'],
                $user->email
            ),
        ]);
    }

// 🧩 Stripe subscription creation logic
if (!empty($state['company']['subscription_plan_id'])) {
    $plan = \App\Models\SubscriptionPlan::find($state['company']['subscription_plan_id']);

    if ($plan && $plan->stripe_price_id) {
        try {
            // Create checkout session
            $checkout = $user->newSubscription('default', $plan->stripe_price_id)
                ->checkout([
                    'success_url' => route('subscription.success'),
                    'cancel_url' => route('subscription.cancel'),
                ]);

            // ✅ Simply redirect to Stripe checkout page
            redirect($checkout->url)->send();
            exit; // stop further execution

        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Stripe Error')
                ->body('Unable to start subscription: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}


    Notification::make()
        ->title('Profile & Company info saved successfully!')
        ->success()
        ->send();
}




    protected function generateStaffInvitationLink(string $companyName, string $managerEmail): string
    {
        $baseUrl = config('app.url') . '/staff/register';
        $encodedCompanyName = urlencode($companyName);
        $encodedEmail = base64_encode($managerEmail);

        return "{$baseUrl}?company_name={$encodedCompanyName}&manager_email={$encodedEmail}";
    }
}
