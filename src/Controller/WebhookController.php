<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class WebhookController extends AbstractController
{
    public function updateListStatusAction(Request $request): Response
    {
        return new Response();
    }
}
