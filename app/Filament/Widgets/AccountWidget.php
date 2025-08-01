<?php

namespace Filament\Widgets;

class AccountWidget extends Widget
{
    protected static ?int $sort = -3;

    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::widgets.account-widget';

    // Añade esta línea para que ocupe el ancho completo
    protected int | string | array $columnSpan = 'full';
}
