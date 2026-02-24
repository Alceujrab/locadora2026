<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\LoginPage as BaseLoginPage;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\ActionButton;

class LoginPage extends BaseLoginPage
{
    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        $components = parent::components();

        // Adiciona um divisor com texto "Ou" e os botões de redes sociais
        $components[] = Divider::make('Ou entre com');
        $components[] = Flex::make([
            ActionButton::make('Google', '/auth/social/google')
                ->icon('envelope')
                ->customAttributes(['class' => 'w-full'])
                ->primary(),
            
            ActionButton::make('Facebook', '/auth/social/facebook')
                ->icon('users')
                ->customAttributes(['class' => 'w-full'])
                ->primary(),
        ])
        ->justifyAlign('center')
        ->customAttributes(['class' => 'gap-4 mt-4']);

        return $components;
    }
}
