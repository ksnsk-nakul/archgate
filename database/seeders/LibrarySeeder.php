<?php

namespace Database\Seeders;

use App\Models\LibraryContent;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'Getting Started with Our Platform',
                'type' => 'article',
                'access_level' => 'free',
                'description' => 'A complete onboarding guide covering the core features, navigation, and initial setup steps to get your team up and running quickly.',
            ],
            [
                'title' => 'Advanced Dashboard Walkthrough',
                'type' => 'video',
                'access_level' => 'basic',
                'description' => 'A 25-minute video tour of the analytics dashboard, covering custom widgets, date ranges, export options, and team sharing.',
            ],
            [
                'title' => 'API Integration Handbook',
                'type' => 'pdf',
                'access_level' => 'premium',
                'description' => 'Full technical reference for our REST API. Includes authentication flows, rate limits, endpoint schemas, and sample request/response payloads.',
            ],
            [
                'title' => 'CRM Best Practices',
                'type' => 'article',
                'access_level' => 'basic',
                'description' => 'Strategies for managing contacts, structuring your pipeline, and closing deals faster using the built-in CRM tools.',
            ],
            [
                'title' => 'Project Management Essentials',
                'type' => 'video',
                'access_level' => 'free',
                'description' => 'Learn how to create projects, assign tasks, track milestones, and generate status reports — a must-watch for new project managers.',
            ],
            [
                'title' => 'Security & Compliance Guide',
                'type' => 'pdf',
                'access_level' => 'premium',
                'description' => 'Detailed documentation on data encryption, access control policies, audit logging, GDPR compliance steps, and security best practices.',
            ],
            [
                'title' => 'Team Roles & Permissions Explained',
                'type' => 'article',
                'access_level' => 'basic',
                'description' => 'An overview of the RBAC model, how to create custom roles, assign permissions, and manage team member access at the project level.',
            ],
            [
                'title' => 'Productivity Mindset — Founder Podcast',
                'type' => 'audio',
                'access_level' => 'free',
                'description' => 'A 40-minute conversation with our founder on building high-output teams, removing blockers, and sustaining deep work in a remote environment.',
            ],
            [
                'title' => 'Quarterly Business Review Template',
                'type' => 'pdf',
                'access_level' => 'basic',
                'description' => 'A ready-to-use QBR template with slide-ready sections for revenue summary, pipeline review, team performance, and next quarter goals.',
            ],
            [
                'title' => 'Custom Integrations & Webhooks',
                'type' => 'article',
                'access_level' => 'premium',
                'description' => 'Step-by-step guide to setting up custom webhooks, Zapier connections, and native integrations with Slack, HubSpot, and Google Workspace.',
            ],
        ];

        foreach ($items as $item) {
            LibraryContent::query()->firstOrCreate(['title' => $item['title']], [
                'type' => $item['type'],
                'access_level' => $item['access_level'],
                'description' => $item['description'],
            ]);
        }
    }
}
