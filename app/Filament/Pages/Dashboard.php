<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
  protected static ?string $navigationLabel = 'Dashboard';

  protected static ?string $title = 'Summary';

  protected static ?string $navigationIcon = 'heroicon-o-home';

  protected static ?int $navigationSort = -2;
}
