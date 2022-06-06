<?php

namespace App\Twig;

use App\Entity\Game;
use App\Entity\Order;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('remove_html_entities', [$this, 'removeHtmlEntities']),
            new TwigFilter('iva', [$this, 'applyIva']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
            new TwigFunction('sub_totals_items_cart', [$this, 'totalsItemsCart']),
        ];
    }

    public function removeHtmlEntities($value): string
    {
        return strip_tags($value);
    }

    public function totalsItemsCart(?array $itemsCart) : string
    {
        $sub_total = 0.00;
        if(!empty($itemsCart)){
            foreach ($itemsCart as $itemId => $item) {
                /** @var Game $game */
                $game = $item['game'];
                $sub_total += $game->getPrice() * intval($item['quantity']);
            }
        }

        return number_format($sub_total,Order::DECIMALS);
    }

    public function applyIva($totalWithoutIva) : string
    {
        return number_format((Order::IVA * $totalWithoutIva / 100) + $totalWithoutIva,Order::DECIMALS);
    }
}
