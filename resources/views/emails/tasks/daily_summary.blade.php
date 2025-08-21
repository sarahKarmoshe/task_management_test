@component('mail::message')
    # Hi {{ $user->name }},

    Here’s your daily task summary:

    @component('mail::panel')
        **Pending:** {{ $counts['pending'] ?? 0 }}
        **In Progress:** {{ $counts['in-progress'] ?? 0 }}
        **Completed:** {{ $counts['completed'] ?? 0 }}
    @endcomponent

    Thanks,
@endcomponent
