<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run(): void
    {
        $instructor = User::query()->first();

        $courses = [
            [
                'title' => 'Platform Fundamentals',
                'description' => 'Master the core concepts and workflows of the platform. Perfect for new team members joining an existing organisation.',
                'status' => 'published',
                'sections' => [
                    [
                        'title' => 'Getting Started',
                        'lessons' => [
                            ['title' => 'Welcome & Overview', 'type' => 'video', 'content' => 'An introduction to the platform, its core modules, and how they fit together to support your team\'s workflows.'],
                            ['title' => 'Setting Up Your Account', 'type' => 'article', 'content' => 'How to configure your profile, notification preferences, and two-factor authentication.'],
                            ['title' => 'Navigating the Dashboard', 'type' => 'video', 'content' => 'A guided tour of the main dashboard, sidebar navigation, and quick-access shortcuts.'],
                        ],
                    ],
                    [
                        'title' => 'Core Modules',
                        'lessons' => [
                            ['title' => 'Projects & Tasks', 'type' => 'video', 'content' => 'Learn to create projects, add tasks, assign owners, set due dates, and track completion.'],
                            ['title' => 'CRM Basics', 'type' => 'article', 'content' => 'How to add contacts, create deals, manage your pipeline stages, and link deals to contacts.'],
                            ['title' => 'Knowledge Library', 'type' => 'video', 'content' => 'Exploring the library, filtering by type and access level, and downloading resources.'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Advanced CRM Strategies',
                'description' => 'Deep-dive into pipeline management, deal forecasting, contact segmentation, and CRM automation for revenue teams.',
                'status' => 'published',
                'sections' => [
                    [
                        'title' => 'Pipeline Mastery',
                        'lessons' => [
                            ['title' => 'Designing Your Pipeline Stages', 'type' => 'article', 'content' => 'Best practices for structuring pipeline stages that match your sales process and reduce deal slippage.'],
                            ['title' => 'Deal Scoring & Prioritisation', 'type' => 'video', 'content' => 'How to score deals based on value, stage probability, and activity recency to focus your team\'s energy.'],
                        ],
                    ],
                    [
                        'title' => 'Contact Management',
                        'lessons' => [
                            ['title' => 'Segmenting Your Contacts', 'type' => 'article', 'content' => 'Creating meaningful contact segments using filters, tags, and deal associations for targeted outreach.'],
                            ['title' => 'Import & Bulk Operations', 'type' => 'video', 'content' => 'How to bulk-import contacts from CSV, deduplicate records, and run bulk updates.'],
                            ['title' => 'Activity Tracking', 'type' => 'article', 'content' => 'Logging calls, emails, and meetings against contacts and deals to maintain a complete activity history.'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Team Collaboration & Roles',
                'description' => 'Learn how to structure your team with roles, permissions, and collaborative workflows that scale with your organisation.',
                'status' => 'published',
                'sections' => [
                    [
                        'title' => 'Roles & Permissions',
                        'lessons' => [
                            ['title' => 'Understanding the RBAC Model', 'type' => 'article', 'content' => 'An explanation of role-based access control, how roles are structured, and how permissions are inherited.'],
                            ['title' => 'Creating Custom Roles', 'type' => 'video', 'content' => 'Step-by-step walkthrough of building a custom role, assigning granular permissions, and applying it to team members.'],
                        ],
                    ],
                    [
                        'title' => 'Collaborative Workflows',
                        'lessons' => [
                            ['title' => 'Assigning & Delegating Tasks', 'type' => 'video', 'content' => 'How to assign tasks with context, set dependencies, and use subtasks to break down complex work.'],
                            ['title' => 'Team Reporting', 'type' => 'article', 'content' => 'Generating and interpreting team performance reports, task completion rates, and project health metrics.'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::query()->firstOrCreate(['title' => $courseData['title']], [
                'description' => $courseData['description'],
                'status' => $courseData['status'],
                'instructor_id' => $instructor?->id,
            ]);

            foreach ($courseData['sections'] as $sectionOrder => $sectionData) {
                $section = Section::query()->firstOrCreate(
                    ['title' => $sectionData['title'], 'course_id' => $course->id],
                    ['order' => $sectionOrder + 1],
                );

                foreach ($sectionData['lessons'] as $lessonOrder => $lessonData) {
                    Lesson::query()->firstOrCreate(
                        ['title' => $lessonData['title'], 'section_id' => $section->id],
                        [
                            'type' => $lessonData['type'],
                            'content' => $lessonData['content'],
                            'order' => $lessonOrder + 1,
                        ],
                    );
                }
            }
        }
    }
}
