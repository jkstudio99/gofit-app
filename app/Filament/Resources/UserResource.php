<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\MasterUserType;
use App\Models\MasterUserStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'ผู้ใช้งาน';

    protected static ?string $modelLabel = 'ผู้ใช้งาน';

    protected static ?string $pluralModelLabel = 'รายการผู้ใช้งาน';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_type_id')
                    ->label('ประเภทผู้ใช้')
                    ->relationship('userType', 'type_name')
                    ->required(),
                Forms\Components\Select::make('user_status_id')
                    ->label('สถานะผู้ใช้')
                    ->relationship('userStatus', 'user_status_name')
                    ->required(),
                Forms\Components\TextInput::make('username')
                    ->label('ชื่อผู้ใช้')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('password')
                    ->label('รหัสผ่าน')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(191),
                Forms\Components\TextInput::make('firstname')
                    ->label('ชื่อ')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('lastname')
                    ->label('นามสกุล')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('email')
                    ->label('อีเมล')
                    ->email()
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('telephone')
                    ->label('เบอร์โทรศัพท์')
                    ->tel()
                    ->maxLength(10),
                Forms\Components\DateTimePicker::make('last_login_at')
                    ->label('เข้าสู่ระบบล่าสุด')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('ชื่อผู้ใช้')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('firstname')
                    ->label('ชื่อ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lastname')
                    ->label('นามสกุล')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('อีเมล')
                    ->searchable(),
                Tables\Columns\TextColumn::make('userType.type_name')
                    ->label('ประเภทผู้ใช้')
                    ->sortable(),
                Tables\Columns\TextColumn::make('userStatus.user_status_name')
                    ->label('สถานะ')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Inactive' => 'danger',
                        'Pending' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('เข้าสู่ระบบล่าสุด')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('วันที่สร้าง')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_type_id')
                    ->label('ประเภทผู้ใช้')
                    ->relationship('userType', 'type_name'),
                Tables\Filters\SelectFilter::make('user_status_id')
                    ->label('สถานะ')
                    ->relationship('userStatus', 'user_status_name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('แก้ไข'),
                Tables\Actions\DeleteAction::make()->label('ลบ'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('ลบที่เลือก'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('สร้างผู้ใช้ใหม่'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // ความสัมพันธ์จะเพิ่มภายหลัง
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
