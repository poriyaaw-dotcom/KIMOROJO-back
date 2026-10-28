<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomOrderResource\Pages;
use App\Models\CustomOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomOrderResource extends Resource
{
    protected static ?string $model = CustomOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')->numeric(),
                Forms\Components\TextInput::make('phone')->required(),
                Forms\Components\Textarea::make('address')->columnSpanFull(),
                Forms\Components\TextInput::make('jewelry_type')->required(),
                Forms\Components\TextInput::make('metal_type')->required(),
                Forms\Components\FileUpload::make('reference_image')->image(),
                Forms\Components\Textarea::make('details')->required()->columnSpanFull(),
                Forms\Components\TextInput::make('budget')->numeric(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('admin_notes')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->limit(30),
                Tables\Columns\TextColumn::make('jewelry_type')->searchable(),
                Tables\Columns\TextColumn::make('metal_type')->searchable(),
                Tables\Columns\ImageColumn::make('reference_image'),
                Tables\Columns\TextColumn::make('budget')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'primary',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomOrders::route('/'),
            'create' => Pages\CreateCustomOrder::route('/create'),
            'edit' => Pages\EditCustomOrder::route('/{record}/edit'),
        ];
    }
}