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
    <SoftKeyItem>
        <Name>Dial</Name>
        <URL>SoftKey:Dial</URL>
        <Position>1</Position>
    </SoftKeyItem>
    <SoftKeyItem>
        <Name>Prev</Name>
        <URL>{{$prevPage}}</URL>
        <Position>2</Position>
    </SoftKeyItem>
    <SoftKeyItem>
        <Name>終了</Name>
        <URL>SoftKey:Exit</URL>
        <Position>3</Position>
    </SoftKeyItem>
    <SoftKeyItem>
        <Name>Next</Name>
        <URL>{{$nextPage}}</URL>
        <Position>4</Position>
    </SoftKeyItem>
</CiscoIPPhoneDirectory>
