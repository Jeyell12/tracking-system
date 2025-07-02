<?php

namespace App\Filament\Tables\Columns;

use Closure;
use Filament\Support\Htmlable;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

class QRCodeColumn extends Column
{
    protected string $view = 'filament.tables.columns.qr-code-column';

    protected string | Closure | null $size = '100';

    protected string | Closure | null $color = '#000000';

    protected string | Closure | null $bgColor = '#FFFFFF';

    protected string | Closure | null $format = 'png';

    protected string | Closure | null $margin = '1';

    protected string | Closure | null $errorCorrectionLevel = 'L';

    protected string | Closure | null $encoding = 'UTF-8';
    
    protected string $viewMode = 'button';
    
    protected string | Closure | null $text = null;
    
    public function getLabel(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return $this->label ?? 'QR Code';
    }



    public static function make(string $name): static
    {
        return parent::make($name);
    }

    public function size(string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function color(string | Closure | null $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function bgColor(string | Closure | null $bgColor): static
    {
        $this->bgColor = $bgColor;

        return $this;
    }

    public function format(string | Closure | null $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function margin(string | Closure | null $margin): static
    {
        $this->margin = $margin;

        return $this;
    }

    public function errorCorrectionLevel(string | Closure | null $errorCorrectionLevel): static
    {
        $this->errorCorrectionLevel = $errorCorrectionLevel;

        return $this;
    }

    public function encoding(string | Closure | null $encoding): static
    {
        $this->encoding = $encoding;

        return $this;
    }

    public function text(string | Closure | null $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function viewMode(string $viewMode): static
    {
        $this->viewMode = $viewMode;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->evaluate($this->size);
    }

    public function getColor(): ?string
    {
        return $this->evaluate($this->color);
    }

    public function getBgColor(): ?string
    {
        return $this->evaluate($this->bgColor);
    }

    public function getFormat(): ?string
    {
        return $this->evaluate($this->format);
    }

    public function getMargin(): ?string
    {
        return $this->evaluate($this->margin);
    }

    public function getErrorCorrectionLevel(): ?string
    {
        return $this->evaluate($this->errorCorrectionLevel);
    }

    public function getEncoding(): ?string
    {
        return $this->evaluate($this->encoding);
    }

    public function getText($record): ?string
    {
        if ($this->text) {
            return $this->evaluate($this->text, ['record' => $record]);
        }

        return route('vehicles.public.show', $record);
    }

    public function getViewMode(): string
    {
        return $this->viewMode;
    }
    
    public function disableClick(\Closure|bool $condition = true): static
    {
        $this->isClickDisabled = $condition;
        return $this;
    }
}
