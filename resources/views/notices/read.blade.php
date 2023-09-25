<x-layout>
    <x-slot:title>{{ $notice->meeting }} {{ $notice->title }}</x-slot:title>
   
    {!! $notice->content !!} 
    
</x-layout>
