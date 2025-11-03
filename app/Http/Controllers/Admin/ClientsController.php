<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\CreateClient;
use App\Actions\UpdateClient;
use App\Data\ClientFilters;
use App\Data\CreateClientData;
use App\Data\UpdateClientData;
use App\Http\Requests\Admin\ClientCreateRequest;
use App\Http\Requests\Admin\ClientEditRequest;
use App\Http\Requests\Admin\ClientIndexRequest;
use App\Http\Requests\Admin\ClientShowRequest;
use App\Http\Requests\Admin\CreateClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Models\Client;
use App\Queries\GetClientsQuery;
use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

final readonly class ClientsController
{
    public function index(
        ClientIndexRequest $request,
        GetClientsQuery $getClientsQuery,
    ): Response {
        $filters = ClientFilters::fromRequest($request);
        $clients = $getClientsQuery->handle($filters)->withQueryString();

        return Inertia::render('admin/clients/index', [
            'clients' => fn () => $clients,
            'filters' => $request->validated(),
        ]);
    }

    public function create(ClientCreateRequest $request): Response
    {
        return Inertia::render('admin/clients/create');
    }

    public function store(
        CreateClientRequest $request,
        CreateClient $createClient,
    ): RedirectResponse {
        try {
            /** @var array{name: string, email: string, phone: ?string, address: ?string, status: string, industry: ?string, contact_person: ?string, website: ?string} $validated */
            $validated = $request->validated();

            $data = new CreateClientData(
                name: $validated['name'],
                email: $validated['email'],
                phone: $validated['phone'],
                address: $validated['address'],
                status: $validated['status'],
                industry: $validated['industry'],
                contact_person: $validated['contact_person'],
                website: $validated['website'],
            );

            $createClient->handle($data);

            return to_route('admin.clients.index')->with('status', [
                'type' => 'success',
                'message' => __('Client created successfully.'),
            ]);
        } catch (Exception) {
            return back()
                ->withInput()
                ->with('status', [
                    'type' => 'error',
                    'message' => __(
                        'Failed to create client. Please try again.',
                    ),
                ]);
        }
    }

    public function show(ClientShowRequest $request, Client $client): Response
    {
        return Inertia::render('admin/clients/show', [
            'client' => $client,
        ]);
    }

    public function edit(ClientEditRequest $request, Client $client): Response
    {
        return Inertia::render('admin/clients/edit', [
            'client' => $client,
        ]);
    }

    public function update(
        UpdateClientRequest $request,
        Client $client,
        UpdateClient $updateClient,
    ): RedirectResponse {
        try {
            /** @var array{name: string, email: string, phone: ?string, address: ?string, status: string, industry: ?string, contact_person: ?string, website: ?string} $validated */
            $validated = $request->validated();

            $data = new UpdateClientData(
                client: $client,
                name: $validated['name'],
                email: $validated['email'],
                phone: $validated['phone'] ?? $client->phone,
                address: $validated['address'] ?? $client->address,
                status: $validated['status'],
                industry: $validated['industry'] ?? $client->industry,
                contact_person: $validated['contact_person'] ??
                    $client->contact_person,
                website: $validated['website'] ?? $client->website,
            );

            $updateClient->handle($data);

            return to_route('admin.clients.index')->with('status', [
                'type' => 'success',
                'message' => __('Client updated successfully.'),
            ]);
        } catch (Exception) {
            return back()
                ->withInput()
                ->with('status', [
                    'type' => 'error',
                    'message' => __(
                        'Failed to update client. Please try again.',
                    ),
                ]);
        }
    }
}
