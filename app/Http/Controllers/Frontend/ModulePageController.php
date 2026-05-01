<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\DealResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\LibraryContentResource;
use App\Http\Resources\PipelineStageResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\SectionResource;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\TaskLogResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\Contact;
use App\Models\Course;
use App\Models\Deal;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LibraryContent;
use App\Models\PipelineStage;
use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ModulePageController extends Controller
{
    public function dashboard(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Dashboard', [
            'stats' => [
                'projects' => Project::query()->where('owner_id', $user->id)->count(),
                'tasks' => Task::query()->whereIn('project_id', Project::query()->where('owner_id', $user->id)->select('id'))->whereNotIn('status', ['completed'])->count(),
                'contacts' => Contact::query()->count(),
                'courses' => Course::query()->count(),
            ],
            'recentProjects' => Project::query()
                ->where('owner_id', $user->id)
                ->latest()
                ->limit(5)
                ->get(['id', 'name', 'status']),
            'recentTasks' => Task::query()
                ->whereIn('project_id', Project::query()->where('owner_id', $user->id)->select('id'))
                ->latest()
                ->limit(5)
                ->get(['id', 'title', 'status', 'priority']),
        ]);
    }

    public function projects(Request $request): Response
    {
        return Inertia::render('Projects/Index', [
            'projects' => ProjectResource::collection(
                Project::query()
                    ->where('owner_id', $request->user()->id)
                    ->latest()
                    ->get()
            ),
        ]);
    }

    public function storeProject(Request $request): RedirectResponse
    {
        Project::create([
            ...$this->validateProject($request),
            'owner_id' => $request->user()->id,
        ]);

        return to_route('app.projects.index');
    }

    public function showProject(Project $project): Response
    {
        return Inertia::render('Projects/Show', [
            'project' => new ProjectResource($project),
        ]);
    }

    public function editProject(Project $project): Response
    {
        return Inertia::render('Projects/Edit', [
            'project' => new ProjectResource($project),
        ]);
    }

    public function updateProject(Request $request, Project $project): RedirectResponse
    {
        $project->update($this->validateProject($request));

        return to_route('app.projects.show', $project);
    }

    public function tasks(): Response
    {
        return Inertia::render('Tasks/Index', [
            'tasks' => TaskResource::collection(
                Task::query()
                    ->with('subtasks')
                    ->latest()
                    ->get()
            ),
        ]);
    }

    public function createTask(Request $request): Response
    {
        return Inertia::render('Tasks/Create', [
            ...$this->taskFormProps($request),
        ]);
    }

    public function storeTask(Request $request, TaskService $tasks): RedirectResponse
    {
        $task = $tasks->create(
            $this->validateTask($request),
            $request->user(),
        );

        return to_route('app.tasks.show', $task);
    }

    public function showTask(Task $task): Response
    {
        return Inertia::render('Tasks/Show', [
            'task' => new TaskResource($task->load('subtasks')),
        ]);
    }

    public function taskHistory(Task $task): Response
    {
        return Inertia::render('Tasks/History', [
            'task' => new TaskResource($task),
            'logs' => TaskLogResource::collection(
                $task->logs()->with('user')->latest()->get()
            ),
        ]);
    }

    public function editTask(Request $request, Task $task): Response
    {
        return Inertia::render('Tasks/Edit', [
            'task' => new TaskResource($task),
            ...$this->taskFormProps($request),
        ]);
    }

    public function updateTask(Request $request, Task $task, TaskService $tasks): RedirectResponse
    {
        $tasks->update($task, $this->validateTask($request), $request->user());

        return to_route('app.tasks.show', $task);
    }

    public function storeSubtask(Request $request, Task $task, TaskService $tasks): RedirectResponse
    {
        $tasks->createSubtask($task, $this->validateSubtask($request), $request->user());

        return to_route('app.tasks.show', $task);
    }

    public function updateSubtask(Request $request, Task $task, Subtask $subtask, TaskService $tasks): RedirectResponse
    {
        abort_unless($subtask->task_id === $task->id, 404);

        $tasks->updateSubtask($task, $subtask, $this->validateSubtask($request), $request->user());

        return to_route('app.tasks.show', $task);
    }

    public function contacts(): Response
    {
        return Inertia::render('Contacts/Index', [
            'contacts' => ContactResource::collection(
                Contact::query()->latest()->get()
            ),
        ]);
    }

    public function showContact(Contact $contact): Response
    {
        return Inertia::render('Contacts/Show', [
            'contact' => new ContactResource($contact),
            'deals' => DealResource::collection(
                $contact->deals()->with(['stage'])->latest()->get()
            ),
        ]);
    }

    public function createContact(): Response
    {
        return Inertia::render('Contacts/Create');
    }

    public function storeContact(Request $request): RedirectResponse
    {
        $contact = Contact::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]));

        return to_route('app.contacts.show', $contact);
    }

    public function editContact(Contact $contact): Response
    {
        return Inertia::render('Contacts/Edit', [
            'contact' => new ContactResource($contact),
        ]);
    }

    public function updateContact(Request $request, Contact $contact): RedirectResponse
    {
        $contact->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]));

        return to_route('app.contacts.show', $contact);
    }

    public function destroyContact(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return to_route('app.contacts.index');
    }

    public function deals(): Response
    {
        return Inertia::render('Deals/Index', [
            'deals' => DealResource::collection(
                Deal::query()
                    ->with(['stage', 'contact'])
                    ->latest()
                    ->get()
            ),
            'stages' => PipelineStageResource::collection(PipelineStage::orderBy('order')->get()),
            'contacts' => ContactResource::collection(Contact::orderBy('name')->get()),
        ]);
    }

    public function createDeal(): Response
    {
        return Inertia::render('Deals/Create', [
            'stages' => PipelineStageResource::collection(PipelineStage::orderBy('order')->get()),
            'contacts' => ContactResource::collection(Contact::orderBy('name')->get()),
        ]);
    }

    public function storeDeal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'stage_id' => ['required', 'integer', 'exists:pipeline_stages,id'],
            'contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
        ]);

        $validated['owner_id'] = $request->user()->id;

        Deal::create($validated);

        return to_route('app.deals.index');
    }

    public function destroyDeal(Deal $deal): RedirectResponse
    {
        $deal->delete();

        return to_route('app.deals.index');
    }

    public function library(Request $request): Response
    {
        return Inertia::render('Library/Index', [
            'content' => LibraryContentResource::collection(
                LibraryContent::query()->latest()->get()
            ),
            'subscriptions' => SubscriptionResource::collection(
                $request->user()->subscriptions()->with('plan')->latest()->get()
            ),
        ]);
    }

    public function courses(Request $request): Response
    {
        return Inertia::render('Courses/Index', [
            'courses' => CourseResource::collection(
                Course::query()->latest()->get()
            ),
            'enrollments' => EnrollmentResource::collection(
                Enrollment::query()
                    ->where('user_id', $request->user()->id)
                    ->latest()
                    ->get()
            ),
        ]);
    }

    public function showCourse(Request $request, Course $course): Response
    {
        $course->load(['sections.lessons', 'instructor']);

        $enrollment = Enrollment::query()
            ->where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->first();

        return Inertia::render('Courses/Show', [
            'course' => new CourseResource($course),
            'sections' => SectionResource::collection($course->sections),
            'enrollment' => $enrollment ? new EnrollmentResource($enrollment) : null,
        ]);
    }

    public function showLesson(Request $request, Lesson $lesson): Response
    {
        $lesson->load('section.course.sections.lessons');

        $course = $lesson->section->course;
        $enrollment = Enrollment::query()
            ->where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->first();

        return Inertia::render('Lessons/Show', [
            'lesson' => new LessonResource($lesson),
            'course' => new CourseResource($course),
            'sections' => SectionResource::collection($course->sections),
            'enrollment' => $enrollment ? new EnrollmentResource($enrollment) : null,
        ]);
    }

    public function profile(Request $request): Response
    {
        return Inertia::render('Profile/Show', [
            'profile' => new UserResource($request->user()),
        ]);
    }

    public function editProfile(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'profile' => new UserResource($request->user()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function taskFormProps(Request $request): array
    {
        return [
            'projects' => ProjectResource::collection(
                Project::query()
                    ->where('owner_id', $request->user()->id)
                    ->orderBy('name')
                    ->get()
            ),
            'users' => UserResource::collection(
                User::query()
                    ->orderBy('name')
                    ->get()
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateTask(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:pending,in_progress,completed'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'project_id' => [
                'required',
                'integer',
                Rule::exists('projects', 'id')->where('owner_id', $request->user()->id),
            ],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateProject(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:active,completed,archived'],
        ]);
    }

    /**
     * @return array{title: string, completed?: bool}
     */
    private function validateSubtask(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'completed' => ['sometimes', 'boolean'],
        ]);
    }
}
