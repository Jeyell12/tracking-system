<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use App\Models\MaintenanceRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class FleetStatsOverview extends BaseWidget
{
  protected int | string | array $columnSpan = 'full';

  protected function getColumns(): int
  {
    return 2;
  }

  protected function getStats(): array
  {
    $totalVehicles = Vehicle::count();
    $vehiclesUnderMaintenance = Vehicle::where('status', 'maintenance')->count();
    $pendingRequests = MaintenanceRequest::where('status', 'pending')->count();
    $activeRequests = MaintenanceRequest::where('status', 'approved')->count();

    return [
      Stat::make('Total Vehicles', $totalVehicles)
        ->description(new HtmlString(
          '<div class="mt-2">
                        <div class="text-sm text-gray-500">Active: ' . Vehicle::where('status', 'active')->count() . '</div>
                        <div class="text-sm text-gray-500">Out of Service: ' . Vehicle::where('status', 'out_of_service')->count() . '</div>
                    </div>'
        ))
        ->descriptionIcon('heroicon-m-truck')
        ->color('success')
        ->chart([7, 3, 4, 5, 6, 3, 5, 3])
        ->extraAttributes([
          'class' => 'cursor-pointer hover:shadow-lg transition-shadow duration-200',
        ]),

      Stat::make('Vehicles Under Maintenance', $vehiclesUnderMaintenance)
        ->description(new HtmlString(
          '<div class="mt-2">
                        <div class="text-sm text-gray-500">' . number_format(($vehiclesUnderMaintenance / $totalVehicles) * 100, 1) . '% of total fleet</div>
                        <div class="text-sm text-gray-500">Last 7 days: ' . Vehicle::where('status', 'maintenance')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count() . ' vehicles</div>
                    </div>'
        ))
        ->descriptionIcon('heroicon-m-wrench-screwdriver')
        ->color('warning')
        ->chart([3, 5, 4, 6, 3, 5, 4, 3])
        ->extraAttributes([
          'class' => 'cursor-pointer hover:shadow-lg transition-shadow duration-200',
        ]),

      Stat::make('Pending Maintenance Requests', $pendingRequests)
        ->description(new HtmlString(
          '<div class="mt-2">
                        <div class="text-sm text-gray-500">Average wait time: ' . $this->getAverageWaitTime('pending') . ' hours</div>
                        <div class="text-sm text-gray-500">Last 24h: ' . MaintenanceRequest::where('status', 'pending')
            ->where('created_at', '>=', now()->subDay())
            ->count() . ' new requests</div>
                    </div>'
        ))
        ->descriptionIcon('heroicon-m-clock')
        ->color('danger')
        ->chart([4, 3, 5, 4, 6, 3, 4, 5])
        ->extraAttributes([
          'class' => 'cursor-pointer hover:shadow-lg transition-shadow duration-200',
        ]),

      Stat::make('Active Maintenance Requests', $activeRequests)
        ->description(new HtmlString(
          '<div class="mt-2">
                        <div class="text-sm text-gray-500">Average completion time: ' . $this->getAverageCompletionTime() . ' days</div>
                        <div class="text-sm text-gray-500">Completed today: ' . MaintenanceRequest::where('status', 'completed')
            ->whereDate('completed_at', today())
            ->count() . ' requests</div>
                    </div>'
        ))
        ->descriptionIcon('heroicon-m-check-circle')
        ->color('info')
        ->chart([5, 4, 3, 5, 4, 6, 3, 4])
        ->extraAttributes([
          'class' => 'cursor-pointer hover:shadow-lg transition-shadow duration-200',
        ]),
    ];
  }

  protected function getAverageWaitTime(string $status): string
  {
    $avgHours = MaintenanceRequest::where('status', $status)
      ->whereNotNull('approved_at')
      ->get()
      ->avg(function ($request) {
        return $request->approved_at->diffInHours($request->created_at);
      });

    return number_format($avgHours ?? 0, 1);
  }

  protected function getAverageCompletionTime(): string
  {
    $avgDays = MaintenanceRequest::where('status', 'completed')
      ->whereNotNull('completed_at')
      ->whereNotNull('approved_at')
      ->get()
      ->avg(function ($request) {
        return $request->completed_at->diffInDays($request->approved_at);
      });

    return number_format($avgDays ?? 0, 1);
  }
}
