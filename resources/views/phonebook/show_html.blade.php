@extends("parts.base_html")

@section("content")
    <div class="container">
        <div id="phone-operation">
            <a class=" button is-primary" href="{{route("phonebook.edit")}}">
                追加
            </a>
            <button class="button is-primary js-modal-trigger" data-target="import-modal">
                インポート
            </button>
        </div>
        <div class="modal" id="import-modal">

            <div class="modal-background"></div>
            <form class="modal-card" style="width: 1000px" method="POST" action="{{route("phonebook.addAll")}}">
                <header class="modal-card-head">
                    <p class="modal-card-title">インポート</p>
                    <button class="delete" aria-label="close"></button>
                </header>
                <section class="modal-card-body">
                    <input type="file" accept="text/csv,application/json" id="import">
                    <label>
                        <input type="checkbox" name="overwrite">
                        上書き保存
                    </label>
                    @csrf
                    <div id="fileContent"></div>
                </section>
                <footer class="modal-card-foot">
                    <button class="button is-success" type="submit">実行</button>
                    <button class="button">Cancel</button>
                </footer>
            </form>
            <button class="modal-close is-large" aria-label="close"></button>

        </div>
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
                            @if($idx==0)
                                <td rowspan="{{$name->numbers->count()}}">
                                    <a class="button is-primary" href="{{route("phonebook.edit", ["id"=>$name->id])}}">編集</a>
                                    <form method="POST" style="display: inline" action="{{route("phonebook.delete", ["id" => $name->id])}}">
                                        <button type="submit" class="button is-danger">削除</button>
                                        <input type="hidden" name="_method" value="DELETE">
                                        @csrf
                                    </form>
                                </td>
                            @endif
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>

    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            // Functions to open and close a modal
            function openModal($el) {
                $el.classList.add('is-active');
            }

            function closeModal($el) {
                $el.classList.remove('is-active');
            }

            function closeAllModals() {
                (document.querySelectorAll('.modal') || []).forEach(($modal) => {
                    closeModal($modal);
                });
            }

            // Add a click event on buttons to open a specific modal
            (document.querySelectorAll('.js-modal-trigger') || []).forEach(($trigger) => {
                const modal = $trigger.dataset.target;
                const $target = document.getElementById(modal);

                $trigger.addEventListener('click', () => {
                    openModal($target);
                });
            });

            // Add a click event on various child elements to close the parent modal
            (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button') || []).forEach(($close) => {
                const $target = $close.closest('.modal');

                $close.addEventListener('click', () => {
                    closeModal($target);
                });
            });

            // Add a keyboard event to close all modals
            document.addEventListener('keydown', (event) => {
                const e = event || window.event;

                if (e.keyCode === 27) { // Escape key
                    closeAllModals();
                }
            });
        });

        function parseCSV(text) {
            const lines_all = text.split("\n")
            if (lines_all.length === 0) return []
            const keys = lines_all[0].split(",")
            const lines = []
            let idx = 0
            for (const value of lines_all.slice(1).join("\n").split(",")) {
                if (idx % keys.length === 0) {
                    lines.push([])
                }
                if (idx % keys.length === keys.length-1 && value.includes("\n")) {
                    lines[lines.length - 1].push(value.split("\n")[0])
                    idx += 1
                    lines.push([])
                    lines[lines.length - 1].push(value.split("\n")[1])
                } else {
                    lines[lines.length - 1].push(value)
                }
                idx += 1
            }
            if (lines[lines.length-1].length !== keys.length) lines.pop()
            return [keys, lines]
        }

        function toObjectArrayCSV(text) {
            const [keys, lines] = parseCSV(text)
            const oa = []
            for (const line of lines) {
                const lo = {}
                for (const [idx, value] of Object.entries(line)) {
                    lo[keys[idx]] = value
                }
                oa.push(lo)
            }
            return oa
        }

        function parseGoogleCSV(text) {
            const result = []
            const [keys, lines] = parseCSV(text)
            const lines_oa = toObjectArrayCSV(text)
            const GooglePhoneNumberTypes = {
                "Home": "自宅",
                "Mobile": "携帯"
            }
            for (const idx in lines) {
                const line = lines[idx]
                const lo = lines_oa[idx]
                const name = {
                    name: lo.Name,
                    ruby: lo["Family Name Yomi"]+lo["Additional Name Yomi"]+lo["Given Name Yomi"],
                    number: []
                }
                for (const [value_idx, value] of Object.entries(line)) {
                    if (keys[value_idx].startsWith("Phone")) {
                        if (keys[value_idx].endsWith("Type")) {
                            name.number.push({"type": GooglePhoneNumberTypes.hasOwnProperty(value)?GooglePhoneNumberTypes[value]:value})
                        } else if (keys[value_idx].endsWith("Value")) {
                            name.number[name.number.length-1]["number"] = value
                            if (value === "") {
                                name.number.pop()
                            }
                        }
                    }
                }

                result.push(name)
            }

            return result
        }

        function makeRow(rows, names=null, editable=false, head=false) {
            const tr = document.createElement("tr")
            for (const idx in rows) {
                const field = rows[idx]
                const name = names?names[idx]:""
                let tf
                if (head) {
                    tf = document.createElement("th")
                } else {
                    tf = document.createElement("td")
                }

                if (editable) {
                    const input = document.createElement("input")
                    input.value = field
                    input.name = name
                    input.classList.add("input")
                    tf.append(input)
                } else {
                    tf.textContent = field
                }

                tr.append(tf)
            }
            return tr
        }

        function makeTable(names, editable=true) {
            const table = document.createElement("table")
            table.classList.add("table")
            const thead = document.createElement("thead")
            thead.append(makeRow(["No.", "名前", "ふりがな", "種別", "番号"], null, false, true))
            table.append(thead)

            const tbody = document.createElement("tbody")
            for (const [idx, name] of Object.entries(names)) {
                for (const [idx1, number] of Object.entries(name.number)) {
                    if (idx1 === "0") {
                        const row = makeRow([idx, name.name, name.ruby, number.type, number.number],
                            ["", `names[${idx}][name]`, `names[${idx}][ruby]`, `names[${idx}][number][${idx1}][type]`, `names[${idx}][number][${idx1}][number]`],
                            true)
                        row.children[0].rowSpan = name.number.length
                        row.children[1].rowSpan = name.number.length
                        row.children[2].rowSpan = name.number.length
                        tbody.append(row)
                    } else {
                        const row = makeRow([number.type, number.number],
                            [`names[${idx}][number][${idx1}][type]`, `names[${idx}][number][${idx1}][number]`],
                            true)
                        tbody.append(row)
                    }
                }
            }
            table.append(tbody)
            return table

        }

        document.getElementById("import").addEventListener("input", (event) => {
            const fileContentElem = document.getElementById("fileContent")
            fileContentElem.innerHTML = ""
            if (event.target.files.length !== 1) {
                return
            }
            const file = event.target.files[0]
            const reader = new FileReader()
            reader.onerror = () => {
                fileContentElem.innerText = "File Error"
            }

            reader.onload = () => {
                const text = reader.result
                const table = makeTable(parseGoogleCSV(text))
                fileContentElem.append(table)
            }

            reader.readAsText(file)

        })
    </script>
@endsection
