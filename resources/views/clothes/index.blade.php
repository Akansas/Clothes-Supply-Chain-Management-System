<x-layout>
    <h1>Currently available Clothes</h1>
@if($material== "silk")
<p>Please buy our clothes</p>
@endif
    <ul>
   @foreach($clothes as $clothe)
    <li>
    <x-card href="/clothes/{{$clothe["size"]}}">
    <h2>{{$clothe["name"]}}</h2>
    </x-card>
    </li>
   @endforeach
    </ul>
    </x-layout>
