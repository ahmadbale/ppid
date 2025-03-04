<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('user::layouts.footer', function ($view) {
            $links = [
                [
                    'title' => 'Pusat Unit Layanan',
                    'menu' => [
                        ['name' => 'Jaminan Mutu', 'route' => '#'],
                        ['name' => 'Perpustakaan', 'route' => 'https://library.polinema.ac.id/'],
                        ['name' => 'UPA TIK', 'route' => 'https://sipuskom.polinema.ac.id/'],
                        ['name' => 'P2M', 'route' => '#'],
                    ]
                ]
            ];

            $icons = [
                [
                    'logo-polinema' => asset('img/logo-polinema.svg'),
                    'logo-blu' => asset('img/logo-blu.svg')
                ]
            ];

            $iconsosmed = [
                [
                    'logo' => asset('img/logo-twitter.svg'),
                    'route' => '#'
                ],
                [
                    'logo' => asset('img/logo-facebook.svg'),
                    'route' => '#'
                ],
                [
                    'logo' => asset('img/logo-instagram.svg'),
                    'route' => '#'
                ],
                [
                    'logo' => asset('img/logo-youtube.svg'),
                    'route' => '#'
                ]
            ];

            $view->with(compact('links', 'icons', 'iconsosmed'));
        });
    }

}
