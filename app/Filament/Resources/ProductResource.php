<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Category;
use App\Models\Collection;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name_en')->label('Name (English)')->required()->maxLength(255),
                TextInput::make('name_fa')->label('Name (Persian)')->required()->maxLength(255),
                Textarea::make('description_en')->label('Description (English)'),
                Textarea::make('description_fa')->label('Description (Persian)'),

                TextInput::make('weight')->label('Weight (grams)')->required()->numeric(),
                TextInput::make('purity')->label('Purity (e.g., 18k, 925)')->required(),
                
                Select::make('metal_type')
                    ->label('Metal Type')
                    ->options([
                        'gold' => 'Gold',
                        'silver' => 'Silver',
                    ])
                    ->required()
                    ->default('gold'),
                
                TextInput::make('making_charges')->label('Making Charges (Toman)')->required()->numeric(),

                Select::make('category_id')->label('Category')->options(Category::pluck('name_en', 'id'))->required()->searchable(),
                Select::make('collection_id')->label('Collection')->options(Collection::pluck('name_en', 'id'))->required()->searchable(),

                FileUpload::make('images')
                    ->label('Product Images (Max 4)')
                    ->directory('products')
                    ->multiple()
                    ->maxFiles(4)
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])
                    ->helperText('1st: Main photo. 2nd: Hover/Model photo. 3rd & 4th: Gallery extras.')
                    ->removeUploadedFileButtonPosition('left')
                    ->uploadButtonPosition('right')
                    ->reorderable(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_en')->label('Name (EN)')->searchable()->sortable(),
                TextColumn::make('metal_type')->label('Metal')->sortable(),
                TextColumn::make('category.name_en')->label('Category')->sortable(),
                TextColumn::make('weight')->label('Weight (g)')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array 
    { 
        return []; 
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}