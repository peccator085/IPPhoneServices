@extends("parts.base_html")

@section("content")
    <div class="container">
        <form method="post" action="{{route("phonebook.edit")}}">
            <div id="phone-operation">
                <button type="submit" class=" button is-primary">
                    {{$name?"適用":"追加"}}
                </button>
            </div>
            @csrf
            <table class="table">
                <thead>
                <tr>
                    <th style="display: none;">ID</th>
                    <th>名前</th>
                    <th>ふりがな</th>
                    <th>電話種別</th>
                    <th>電話番号</th>
                </tr>
                </thead>
                <tbody>
                @foreach(($name?$name->numbers:[0,1,2,3,4,5]) as $i => $number)
                    <tr>
                        @if($i == 0)
                            <td rowspan="6" style="display: none"><input type="hidden" name="id" value="{{$name?$name->id:""}}" /></td>
                            <td rowspan="6"><input class="input" name="name" value="{{$name?$name->name:""}}"/></td>
                            <td rowspan="6"><input class="input" name="ruby" value="{{$name?$name->ruby:""}}"/></td>
                        @endif
                        <td><input class="input" name="number[{{$i}}][type]" value="{{$name?$number->type:""}}"/></td>
                        <td><input class="input" type="tel" name="number[{{$i}}][number]" value="{{$name?$number->number:""}}" /></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </form>

    </div>
@endsection
