<?php

declare(strict_types=1);

namespace MauticPlugin\MauticMarketingPlannerBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use MauticPlugin\MauticMarketingPlannerBundle\Entity\PlannerItem;
use MauticPlugin\MauticMarketingPlannerBundle\Form\Type\PlannerItemType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PlannerController extends FormController
{
    // -------------------------------------------------------------------------
    // INDEX — calendar overview
    // -------------------------------------------------------------------------

    public function indexAction(Request $request): Response
    {
        $view  = $request->query->get('view', 'month');
        $year  = max(2000, min(2100, (int) $request->query->get('year', (int) date('Y'))));
        $month = max(1, min(12, (int) $request->query->get('month', (int) date('n'))));

        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(PlannerItem::class);

        $viewParams = [
            'view'  => $view,
            'year'  => $year,
            'month' => $month,
        ];

        switch ($view) {
            case 'year':
                $items = $repo->findForYear($year);
                $viewParams['itemsByMonth'] = $this->groupByMonth($items);
                $viewParams['prevYear']     = $year - 1;
                $viewParams['nextYear']     = $year + 1;
                break;

            case 'list':
                $viewParams['items'] = $repo->findAllItems();
                $viewParams['today'] = new \DateTime();
                break;

            default: // month
                $items = $repo->findForMonth($year, $month);
                $dt    = new \DateTime(sprintf('%d-%02d-01', $year, $month));

                $prev = (clone $dt)->modify('-1 month');
                $next = (clone $dt)->modify('+1 month');

                $viewParams['calendarWeeks'] = $this->buildMonthGrid($year, $month, $items);
                $viewParams['monthName']     = $dt->format('F');
                $viewParams['prevYear']      = (int) $prev->format('Y');
                $viewParams['prevMonth']     = (int) $prev->format('n');
                $viewParams['nextYear']      = (int) $next->format('Y');
                $viewParams['nextMonth']     = (int) $next->format('n');
                break;
        }

        return $this->delegateView([
            'viewParameters'  => $viewParams,
            'contentTemplate' => '@MauticMarketingPlanner/Planner/index.html.twig',
            'passthroughVars' => [
                'activeLink'    => '#mautic_marketing_planner_index',
                'mauticContent' => 'marketingplanner',
                'route'         => $this->generateUrl('mautic_marketing_planner_index', ['view' => $view, 'year' => $year, 'month' => $month]),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // NEW
    // -------------------------------------------------------------------------

    public function newAction(Request $request): Response
    {
        $item = new PlannerItem();
        $form = $this->createForm(PlannerItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('mautic_marketing_planner_index');
        }

        return $this->delegateView([
            'viewParameters'  => [
                'form'    => $form->createView(),
                'item'    => $item,
                'isNew'   => true,
                'heading' => 'mautic.marketing_planner.new_item',
            ],
            'contentTemplate' => '@MauticMarketingPlanner/Planner/form.html.twig',
            'passthroughVars' => [
                'activeLink'    => '#mautic_marketing_planner_index',
                'mauticContent' => 'marketingplanner',
                'route'         => $this->generateUrl('mautic_marketing_planner_new'),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // EDIT
    // -------------------------------------------------------------------------

    public function editAction(Request $request, int $id): Response
    {
        $em   = $this->getDoctrine()->getManager();
        $item = $em->getRepository(PlannerItem::class)->find($id);

        if (!$item) {
            return $this->notFound('mautic.marketing_planner.item.not_found');
        }

        $form = $this->createForm(PlannerItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('mautic_marketing_planner_index');
        }

        return $this->delegateView([
            'viewParameters'  => [
                'form'    => $form->createView(),
                'item'    => $item,
                'isNew'   => false,
                'heading' => 'mautic.marketing_planner.edit_item',
            ],
            'contentTemplate' => '@MauticMarketingPlanner/Planner/form.html.twig',
            'passthroughVars' => [
                'activeLink'    => '#mautic_marketing_planner_index',
                'mauticContent' => 'marketingplanner',
                'route'         => $this->generateUrl('mautic_marketing_planner_edit', ['id' => $id]),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function deleteAction(Request $request, int $id): Response
    {
        $em   = $this->getDoctrine()->getManager();
        $item = $em->getRepository(PlannerItem::class)->find($id);

        if ($item) {
            $em->remove($item);
            $em->flush();
        }

        $referer = $request->headers->get('referer');

        return $this->redirect($referer ?: $this->generateUrl('mautic_marketing_planner_index'));
    }

    // -------------------------------------------------------------------------
    // DONE toggle
    // -------------------------------------------------------------------------

    public function doneAction(Request $request, int $id): Response
    {
        $em   = $this->getDoctrine()->getManager();
        $item = $em->getRepository(PlannerItem::class)->find($id);

        if ($item) {
            $item->setDoneAt($item->isDone() ? null : new \DateTime());
            $em->flush();
        }

        $referer = $request->headers->get('referer');

        return $this->redirect($referer ?: $this->generateUrl('mautic_marketing_planner_index'));
    }

    // -------------------------------------------------------------------------
    // Calendar helpers
    // -------------------------------------------------------------------------

    private function buildMonthGrid(int $year, int $month, array $items): array
    {
        $firstDay    = new \DateTime(sprintf('%d-%02d-01', $year, $month));
        $daysInMonth = (int) $firstDay->format('t');
        // ISO day: 1=Mon … 7=Sun
        $startDow = (int) $firstDay->format('N');

        $today = [
            'y' => (int) date('Y'),
            'm' => (int) date('n'),
            'd' => (int) date('j'),
        ];

        // Group items by day-of-month
        $itemsByDay = [];
        foreach ($items as $item) {
            $itemsByDay[(int) $item->getDeadline()->format('j')][] = $item;
        }

        $weeks = [];
        $week  = [];

        // Leading empty cells (Mon = col 0)
        for ($i = 1; $i < $startDow; ++$i) {
            $week[] = ['day' => null, 'items' => [], 'isToday' => false, 'isPast' => false];
        }

        for ($day = 1; $day <= $daysInMonth; ++$day) {
            $isToday = ($year === $today['y'] && $month === $today['m'] && $day === $today['d']);
            $isPast  = mktime(0, 0, 0, $month, $day, $year) < mktime(0, 0, 0, $today['m'], $today['d'], $today['y']);

            $week[] = [
                'day'     => $day,
                'items'   => $itemsByDay[$day] ?? [],
                'isToday' => $isToday,
                'isPast'  => $isPast,
            ];

            if (7 === count($week)) {
                $weeks[] = $week;
                $week    = [];
            }
        }

        if (!empty($week)) {
            while (7 !== count($week)) {
                $week[] = ['day' => null, 'items' => [], 'isToday' => false, 'isPast' => false];
            }
            $weeks[] = $week;
        }

        return $weeks;
    }

    private function groupByMonth(array $items): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $grouped[(int) $item->getDeadline()->format('n')][] = $item;
        }

        return $grouped;
    }
}
