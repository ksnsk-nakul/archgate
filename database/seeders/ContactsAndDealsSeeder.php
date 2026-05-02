<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Deal;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactsAndDealsSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure pipeline stages exist
        $stages = [
            ['name' => 'Lead', 'order' => 1],
            ['name' => 'Qualified', 'order' => 2],
            ['name' => 'Proposal', 'order' => 3],
            ['name' => 'Negotiation', 'order' => 4],
            ['name' => 'Closed Won', 'order' => 5],
        ];

        foreach ($stages as $stage) {
            PipelineStage::query()->firstOrCreate(['name' => $stage['name']], ['order' => $stage['order']]);
        }

        $allStages = PipelineStage::all();
        $owner = User::query()->first();

        // Seed contacts with realistic names
        $contactData = [
            ['name' => 'Sarah Mitchell', 'email' => 'sarah.mitchell@acmecorp.com', 'phone' => '+1 415 555 0101'],
            ['name' => 'James Thornton', 'email' => 'jthornton@brightwave.io', 'phone' => '+1 628 555 0234'],
            ['name' => 'Priya Kapoor', 'email' => 'priya.k@novaclient.co', 'phone' => '+44 20 7946 0301'],
            ['name' => 'Marcus Webb', 'email' => 'mwebb@deltatech.com', 'phone' => '+1 512 555 0189'],
            ['name' => 'Lucia Fernandez', 'email' => 'l.fernandez@globalops.net', 'phone' => '+34 91 555 0782'],
            ['name' => 'Tom Hargrove', 'email' => 'tom.hargrove@vertexpro.com', 'phone' => '+1 206 555 0345'],
            ['name' => 'Anika Osei', 'email' => 'aosei@clearpath.io', 'phone' => '+233 30 555 0012'],
            ['name' => 'Daniel Park', 'email' => 'd.park@summitgroup.com', 'phone' => '+1 917 555 0567'],
        ];

        foreach ($contactData as $data) {
            Contact::query()->firstOrCreate(['email' => $data['email']], [
                'name' => $data['name'],
                'phone' => $data['phone'],
            ]);
        }

        $contacts = Contact::all();

        // Seed deals distributed across stages
        $dealData = [
            ['title' => 'Enterprise SaaS Contract – Acme Corp', 'value' => 48000, 'stage' => 'Closed Won'],
            ['title' => 'Q3 Consulting Retainer – Brightwave', 'value' => 12000, 'stage' => 'Negotiation'],
            ['title' => 'Platform Migration – NovaClient', 'value' => 95000, 'stage' => 'Proposal'],
            ['title' => 'Annual Support Package – DeltaTech', 'value' => 7500, 'stage' => 'Qualified'],
            ['title' => 'New Market Entry – GlobalOps', 'value' => 32000, 'stage' => 'Lead'],
            ['title' => 'API Integration Project – VertexPro', 'value' => 18500, 'stage' => 'Proposal'],
            ['title' => 'Data Analytics Dashboard – ClearPath', 'value' => 22000, 'stage' => 'Qualified'],
            ['title' => 'Security Audit – Summit Group', 'value' => 9000, 'stage' => 'Negotiation'],
            ['title' => 'Mobile App Redesign – Acme Corp', 'value' => 55000, 'stage' => 'Lead'],
            ['title' => 'Onboarding Automation – Brightwave', 'value' => 14000, 'stage' => 'Closed Won'],
        ];

        foreach ($dealData as $i => $data) {
            $stage = $allStages->firstWhere('name', $data['stage']) ?? $allStages->first();
            $contact = $contacts->get($i % $contacts->count());

            Deal::query()->firstOrCreate(['title' => $data['title']], [
                'value' => $data['value'],
                'stage_id' => $stage?->id,
                'contact_id' => $contact?->id,
                'owner_id' => $owner?->id,
            ]);
        }
    }
}
