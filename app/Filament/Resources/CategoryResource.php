<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
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
                            ->label('Slug (e.g., rings, necklaces)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        // ADDED: Image Upload Field
                        FileUpload::make('image')
                            ->label('Category Image')
                            ->image()
                            ->directory('categories')
                            ->maxSize(2048) // 2MB max
                            ->columnSpanFull()
                            ->helperText('Recommended: Square or portrait image (e.g., 800x800px)'),
                    ])->columns(2),

                Section::make('SEO Settings')
                    ->description('Optimize this category for search engines. (Optional)')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('SEO Title')
                            ->nullable()
                            ->maxLength(60)
                            ->helperText('Recommended: 50-60 characters'),
                        
                        Textarea::make('meta_description')
                            ->label('SEO Description')
                            ->nullable()
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('Recommended: 150-160 characters'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ADDED: Image Column in Table
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
                
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}