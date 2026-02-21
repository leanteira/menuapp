<?php
// menu_functions.php

/**
 * Genera el HTML del menú en función del array de ítems y el rol del usuario.
 *
 * @param array  $menu      Array de menú.
 * @param string $user_role Rol del usuario.
 * @return string           HTML generado.
 */
function generate_menu($menu, $user_role, $current_page = '')
{
    // Si no se proporciona current_page, obtenerlo de REQUEST_URI
    if (empty($current_page)) {
        $current_page = basename($_SERVER['REQUEST_URI'] ?? '');
        // Remover parámetros de query string
        if (strpos($current_page, '?') !== false) {
            $current_page = substr($current_page, 0, strpos($current_page, '?'));
        }
    }

    $html = '';
    foreach ($menu as $item) {
        // Verifica si el rol del usuario está permitido para este ítem
        if (in_array($user_role, $item['roles'])) {
            // Verifica si el ítem tiene hijos
            $hasChildren = isset($item['children']) && is_array($item['children']);
            // Si se especifica que debe estar abierto, se agrega la clase "open"
            $openClass = (isset($item['open']) && $item['open'] === true) ? ' open' : '';
            // Se agrega la clase "menu-toggle" si tiene hijos
            $toggleClass = $hasChildren ? ' menu-toggle' : '';
            $link = isset($item['link']) ? $item['link'] : 'javascript:void(0);';

            $html .= '<li class="menu-item' . $openClass . '">';
            $html .= '<a href="' . $link . '" class="menu-link' . $toggleClass . '">';
            $html .= '<i class="menu-icon tf-icons ' . $item['icon'] . '"></i>';
            $html .= '<div>' . $item['label'] . '</div>';
            $html .= '</a>';

            // Verifica si algún hijo o descendiente coincide con la página actual
            $childIsActive = false;
            if ($hasChildren) {
                foreach ($item['children'] as $child) {
                    $childLink = isset($child['link']) ? basename($child['link']) : '';
                    if ($childLink === $current_page) {
                        $childIsActive = true;
                        break;
                    }
                    // También revisar los nietos
                    if (isset($child['children']) && is_array($child['children'])) {
                        foreach ($child['children'] as $grandchild) {
                            $grandchildLink = isset($grandchild['link']) ? basename($grandchild['link']) : '';
                            if ($grandchildLink === $current_page) {
                                $childIsActive = true;
                                break 2;
                            }
                        }
                    }
                }
            }

            // Si hay un hijo activo, forzar abrir este item
            if ($childIsActive) {
                $openClass = ' open';
            }

            // Si tiene subítems, se genera la lista anidada
            if ($hasChildren) {
                // Si "open" es true o si un hijo está activo, mostramos el submenú (display: block)
                $showSub = (isset($item['open']) && $item['open'] === true) || $childIsActive;
                $ulStyle = $showSub ? ' style="display: block;"' : '';
                $html .= '<ul class="menu-sub"' . $ulStyle . '>';
                foreach ($item['children'] as $child) {
                    if (in_array($user_role, $child['roles'])) {
                        // Verificar si el hijo también tiene children
                        $childHasChildren = isset($child['children']) && is_array($child['children']) && count($child['children']) > 0;
                        $childToggleClass = $childHasChildren ? ' menu-toggle' : '';
                        $childLink = isset($child['link']) ? $child['link'] : 'javascript:void(0);';

                        $html .= '<li class="menu-item">';
                        $html .= '<a href="' . $childLink . '" class="menu-link' . $childToggleClass . '">';
                        $html .= '<i class="menu-icon tf-icons ' . $child['icon'] . '"></i>';
                        $html .= '<div>' . $child['label'] . '</div>';
                        $html .= '</a>';

                        // Si el hijo tiene sus propios hijos
                        if ($childHasChildren) {
                            $html .= '<ul class="menu-sub">';
                            foreach ($child['children'] as $grandchild) {
                                if (in_array($user_role, $grandchild['roles'])) {
                                    $html .= '<li class="menu-item">';
                                    $html .= '<a href="' . $grandchild['link'] . '" class="menu-link">';
                                    $html .= '<i class="menu-icon tf-icons ' . $grandchild['icon'] . '"></i>';
                                    $html .= '<div>' . $grandchild['label'] . '</div>';
                                    $html .= '</a>';
                                    $html .= '</li>';
                                }
                            }
                            $html .= '</ul>';
                        }

                        $html .= '</li>';
                    }
                }
                $html .= '</ul>';
            }
            $html .= '</li>';
        }
    }
    return $html;
}
