<x-app-layout>
{{--    {{$rolls_permission}}--}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>

    </x-slot>

    <div>
<div id="app">

    <App></App>
{{--<sel :rolls_permission="{{$rolls_permission}}"></sel>--}}
</div>
    </div>

    <div>
        {{--        <div id="app">--}}
        {{--            <test/>--}}
        {{--        </div>--}}
    </div>
    {{--رول پرمیشن --}}


    <x-slot name="script">
{{--        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.14/vue.min.js" integrity="sha512-XdUZ5nrNkVySQBnnM5vzDqHai823Spoq1W3pJoQwomQja+o4Nw0Ew1ppxo5bhF2vMug6sfibhKWcNJsG8Vj9tg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>--}}
        <script src="{{ asset('select/select.js') }}"></script>
        {{--        <script src="{{ asset('js/app.js') }}"></script>--}}
    </x-slot>
</x-app-layout>
