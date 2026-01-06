<?php

namespace App\Http\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

use Closure;
// Note next two lines:
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Obtener el parámetro de idioma o inferirlo del primer segmento de la URL
        $lang = $request->language ?? $request->segment(1);

        // Si el segmento es uno de los contextos conocidos, úsalo
        if (!in_array($lang, ['cli', 'admin', 'op'])) {
            // Si el idioma no es uno de los contextos, podrías establecer un default o mantener la lógica original.
            // En este caso, asumimos que si no es uno de estos, podría ser un idioma real 'es', 'en', etc.
            // O si es null, se mantiene el default de AppServiceProvider.
        }

        \App::setLocale($lang);

        $menus = [];
        if ($lang == 'cli') {
            $menus = config('menus.cli.menu');
        }
        if ($lang == 'admin') {
            $menus = config('menus.admin.menu');
        }
        if ($lang == 'op') {
            $menus = config('menus.op.menu');
        }
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) use ($menus) {
            foreach ($menus as $menu) {
                $event->menu->add($menu);
            }
        });

        return $next($request);
    }
}
