<?php
namespace App\Service;

use App\Models\Menu;

class NavigationService
{
    public function getNavigationByRol(int $rolId)
    {
        $menus = Menu::query()
            ->whereNull('parent_id')
            ->where('status', true)
            ->whereHas('roles', function ($q) use ($rolId) {
                $q->where('menu_rol.rol_id', $rolId);
            })
            ->with([
                'children' => function ($q) use ($rolId) {
                    $q
                        ->where('status', true)
                        ->whereHas('roles', function ($r) use ($rolId) {
                            $r->where('menu_rol.rol_id', $rolId);
                        })
                        ->orderBy('order')
                        ->with([
                            'children' => function ($c) use ($rolId) {
                                $c
                                    ->where('status', true)
                                    ->whereHas('roles', function ($r) use ($rolId) {
                                        $r->where('menu_rol.rol_id', $rolId);
                                    })
                                    ->orderBy('order');
                            }
                        ]);
                }
            ])
            ->orderBy('order')
            ->get();

        return $this->mapToFuseNavigation($menus);
    }

    private function mapToFuseNavigation($menus)
    {
        return $menus->map(function ($menu) {
            $item = [
                'id' => (string) $menu->id,
                'title' => $menu->title,
                'subtitle' => $menu->subtitle,
                'type' => $menu->type,
                'icon' => $menu->icon,
            ];

            // Solo basic lleva link
            if ($menu->type === 'basic') {
                $item['link'] = $menu->link;
            }

            // Hijos (recursivo)
            if ($menu->children && $menu->children->count() > 0) {
                $item['children'] = $this->mapToFuseNavigation($menu->children);
            }

            return $item;
        })->values();
    }
}
