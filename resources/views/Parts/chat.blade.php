<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div id="chat" class="py-12">
        @if( auth()->guest())
            <chat-guest></chat-guest>
        @elseif ( auth()->user()->getRoleNames()[0]==="Support")
            <Chat></Chat>
        @else
            <chat-user></chat-user>
        @endif
    </div>
    <x-slot name="script">
        <script src="{{ asset('js/Chat/Chat.js') }}"></script>
    </x-slot>
</x-app-layout>


