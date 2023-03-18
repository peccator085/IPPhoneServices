<?xml version="1.0" encoding="utf-8" ?>
<CiscoIPPhoneDirectory>
    <Title>PhoneBook</Title>
    @foreach($numbers as $number)
        <DirectoryEntry>
            @if($number->type)
                <Name>{{$number->name->name}} ({{$number->type}})</Name>
            @else
                <Name>{{$number->name->name}}</Name>
            @endif
            <Telephone>{{$number->number}}</Telephone>
        </DirectoryEntry>
    @endforeach
</CiscoIPPhoneDirectory>
