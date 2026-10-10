@use('App\Models\TripRequest')
New trip request #{!! $tripRequest->id !!} ({!! $tripRequest->created_at->format('j M Y, H:i') !!})

Name:        {!! $tripRequest->name !!}
Email:       {!! $tripRequest->email !!}
Phone:       {!! $tripRequest->phone ?: '-' !!}
Country:     {!! $tripRequest->country ?: '-' !!}

Trip:        {!! $tripRequest->trip ?: '-' !!}
Interests:   {!! collect($tripRequest->interests ?? [])->map(fn ($key) => TripRequest::INTERESTS[$key] ?? $key)->implode(', ') ?: '-' !!}
Travel date: {!! $tripRequest->travel_date?->format('j M Y') ?? 'Flexible' !!}
Duration:    {!! $tripRequest->duration_days ? $tripRequest->duration_days.' days' : '-' !!}
Travellers:  {!! $tripRequest->adults !!} adult(s), {!! $tripRequest->children !!} child(ren)
Budget:      {!! TripRequest::BUDGETS[$tripRequest->budget] ?? '-' !!}

Message:
{!! $tripRequest->message ?: '-' !!}

Reply to this email to answer {!! $tripRequest->name !!} directly.
