@extends("parts.base_html")

@section("content")
    <div class="container">

        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>名前</th>
                <th>電話種別</th>
                <th>電話番号</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($names as $name)
                @foreach($name->numbers as $idx => $number)
                    <tr>
                        @if($idx==0)
                            <td rowspan="{{$name->numbers->count()}}">{{$name->id}}</td>
                            <td rowspan="{{$name->numbers->count()}}">{{$name->name}}</td>
                        @endif
                        <td>{{$number->type}}</td>
                        <td>{{$number->number}}</td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
