<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectionResource\Pages;
use App\Models\Collection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name_en')
                    ->label('Name (English)')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('name_fa')
                    ->label('Name (Persian)')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('slug')
                    ->label('Slug (e.g., timeless-elegance)')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                FileUpload::make('image')
                    ->label('Collection Image')
                    ->directory('collections')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])
                    ->helperText('Recommended: Square or portrait image (e.g., 800×800px)'),

                // SEO SECTION ADDED HERE
                Section::make('SEO Settings')
                    ->description('Optimize this collection for search engines. (Optional)')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('SEO Title')
                            ->nullable()
                            ->maxLength(60),
                        
                        Textarea::make('meta_description')
                            ->label('SEO Description')
                            ->nullable()
                            ->maxLength(160)
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->square()
                    ->size(50),
                
                TextColumn::make('name_en')
                    ->label('Name (EN)')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('name_fa')
                    ->label('Name (FA)')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
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
            'index' => Pages\ListCollections::route('/'),
            'create' => Pages\CreateCollection::route('/create'),
            'edit' => Pages\EditCollection::route('/{record}/edit'),
        ];
    }
}