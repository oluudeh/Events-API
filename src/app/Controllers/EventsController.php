<?php

namespace App\Controllers;


use App\Services\EventService;
use App\Validators\InputValidator;
use App\Validators\Rule as ValidatorsRule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DI\Attribute\Inject;

class EventsController
{
    #[Inject]
    private EventService $eventService;

    /**
     * Fetch a paged list of events based of `term` and/or `date` query parameters.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        InputValidator::validate(
            rules: [
                'date' => ValidatorsRule::PresentAndFutureDate
            ],
            inputs: $request->query->all(),
        );

        $term = $request->query->get('term', '');
        $date = $request->query->get('date', '');
        $page = $request->query->get('page', 1);
        $size = $request->query->get('size', 10);
        $events = $this->eventService->fetchEvents($term, $date, $page, $size);

        return new Response(json_encode($events), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }
}
