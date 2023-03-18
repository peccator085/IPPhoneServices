<?xml version="1.0" encoding="utf-8" ?>
<CiscoIPPhoneMenu>
    <Title>PhoneBook</Title>
    <Prompt>頭文字を選択</Prompt>
    <MenuItem>
        <Name>あ行</Name>
        <URL>{{route("phonebook.show", ["gyo"=>"あ"])}}</URL>
    </MenuItem>
    <MenuItem>
        <Name>か行</Name>
        <URL>{{route("phonebook.show", ["gyo"=>"か"])}}</URL>
    </MenuItem>
    <MenuItem>
        <Name>さ行</Name>
        <URL>{{route("phonebook.show", ["gyo"=>"さ"])}}</URL>
    </MenuItem>
    <MenuItem>
        <Name>た行</Name>
        <URL>{{route("phonebook.show", ["gyo"=>"た"])}}</URL>
    </MenuItem>
    <MenuItem>
        <Name>な行</Name>
        <URL>{{route("phonebook.show", ["gyo"=>"な"])}}</URL>
    </MenuItem>
    <MenuItem>
        <Name>は行</Name>
        <URL>{{route("phonebook.show", ["gyo"=>"は"])}}</URL>
    </MenuItem>
    <MenuItem>
        <Name>ま行</Name>
        <URL>{{route("phonebook.show", ["gyo"=>"ま"])}}</URL>
    </MenuItem>
    <MenuItem>
        <Name>や行</Name>
        <URL>{{route("phonebook.show", ["gyo"=>"や"])}}</URL>
    </MenuItem>
    <MenuItem>
        <Name>ら行</Name>
        <URL>{{route("phonebook.show", ["gyo"=>"ら"])}}</URL>
    </MenuItem>
    <MenuItem>
        <Name>わ行</Name>
        <URL>{{route("phonebook.show", ["gyo"=>"わ"])}}</URL>
    </MenuItem>
    <MenuItem>
        <Name>英数</Name>
        <URL>{{route("phonebook.show", ["gyo"=>"英数"])}}</URL>
    </MenuItem>
    <MenuItem>
        <Name>その他</Name>
        <URL>{{route("phonebook.show", ["gyo"=>"その他"])}}</URL>
    </MenuItem>
</CiscoIPPhoneMenu>
