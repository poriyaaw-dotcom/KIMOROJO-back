<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class PricingSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static string $view = 'filament.pages.pricing-settings';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        // Load existing values or use defaults (7% profit, 10% tax)
        $this->form->fill([
            'seller_profit' => Setting::get('seller_profit', '7'),
            'tax_rate' => Setting::get('tax_rate', '10'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pricing Configuration')
                    ->description('These values are used in the frontend pricing formula.')
                    ->schema([
                        Forms\Components\TextInput::make('seller_profit')
                            ->label('Seller Profit (%)')
                            ->numeric()
                            ->required()
                            ->helperText('Enter as a whole number (e.g., 7 for 7%). This is multiplied by the live metal price.'),
                        Forms\Components\TextInput::make('tax_rate')
                            ->label('Tax Rate (%)')
                            ->numeric()
                            ->required()
                            ->helperText('Enter as a whole number (e.g., 10 for 10%). Applied to profit and making charges.'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Convert whole numbers to decimals for the formula (e.g., 7 -> 0.07)
        $profitDecimal = number_format($data['seller_profit'] / 100, 3, '.', '');
        $taxDecimal = number_format($data['tax_rate'] / 100, 3, '.', '');

        Setting::set('seller_profit', $profitDecimal);
        Setting::set('tax_rate', $taxDecimal);

        Notification::make()
            ->success()
            ->title('Settings Saved')
            ->body('Pricing formulas have been updated successfully.')
            ->send();
    }
}