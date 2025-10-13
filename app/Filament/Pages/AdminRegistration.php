<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;

class AdminRegistration extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $title = 'Admin Registration';
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.admin-registration';

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // Step 1: Admin Info
                    Step::make('Admin Info')
                        ->schema([
                            TextInput::make('admin.name')
                                ->label('Full Name')
                                ->required(),
                            TextInput::make('admin.email')
                                ->email()
                                ->required(),
                            TextInput::make('admin.password')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->confirmed(),
                            TextInput::make('admin.password_confirmation')
                                ->password()
                                ->label('Confirm Password')
                                ->required(),
                        ]),

                    // Step 2: Company Info
                    Step::make('Company Info')
                        ->schema([
                            TextInput::make('company.name')
                                ->label('Company Name')
                                ->required(),
                            TextInput::make('company.country')
                                ->label('Company Country')
                                ->required(),
                            FileUpload::make('company.logo')
                                ->image()
                                ->directory('company-logos')
                                ->disk('public')
                                ->required()
                                ->visibility('public')
                                ->label('Company Logo')
                                ->imagePreviewHeight('150')
                                ->preserveFilenames(),
                        ]),

                    // Step 3: Subscription Plan
                    Step::make('Subscription Plan')
                        ->schema([
                            Select::make('subscription.plan_id')
                                ->label('Choose Subscription Plan')
                                ->options(SubscriptionPlan::pluck('name', 'id'))
                                ->required(),
                        ]),
                ])
                    ->submitAction(new HtmlString(Blade::render(<<<BLADE
                        <x-filament::button
                            type="submit"
                            size="sm"
                        >
                            Submit
                        </x-filament::button>
                    BLADE)))
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $state = $this->form->getState();

        try {
            // Step 1: Create Admin User
            $user = User::create([
                'name' => $state['admin']['name'],
                'email' => $state['admin']['email'],
                'password' => Hash::make($state['admin']['password']),
            ]);

            // Assign Admin Role (Spatie)
            $user->assignRole('Admin');

            // Step 2: Create Company
            $company = Company::create([
                'user_id' => $user->id,
                'name' => $state['company']['name'],
                'country' => $state['company']['country'],
                'company_logo' => $state['company']['logo'] ?? null,
                'company_no' => 'CN#' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
                'is_subscribed' => false,
                'staff_invitation_link' => $this->generateStaffInvitationLink(
                $state['company']['name'],
                $user->email
            ),
            ]);


            // Step 3: Handle Subscription Plan (Stripe)
            $plan = SubscriptionPlan::find($state['subscription']['plan_id']);

           if ($plan && $plan->stripe_price_id) {
                    $checkout = $user->newSubscription('default', $plan->stripe_price_id)
                        ->checkout([
                            'success_url' => route('admin.subscription.success'),
                            'cancel_url' => route('admin.subscription.cancel'),
                        ]);

                    $company->update([
                        'subscription_plan_id' => $plan->id,
                        'is_subscribed' => true,
                    ]);

                    session(['recent_user_id' => $user->id]);

                    redirect()->away($checkout->url);
                }


            Notification::make()
                ->title('Registration Complete')
                ->body('Admin, Company & Subscription setup successfully!')
                ->success()
                ->send();


        } catch (\Exception $e) {
            // Notification::make()
            //     ->title('Registration Failed')
            //     ->body('Error: ' . $e->getMessage())
            //     ->danger()
            //     ->send();
        }
    }

    protected function generateStaffInvitationLink(string $companyName, string $managerEmail): string
{
    $baseUrl = config('app.url') . '/staff/register';
    $encodedCompanyName = urlencode($companyName);
    $encodedEmail = base64_encode($managerEmail);

    return "{$baseUrl}?company_name={$encodedCompanyName}&manager_email={$encodedEmail}";
}
}
