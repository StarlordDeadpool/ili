<x-layout>
    <x-header />
    <main class="d-flex align-patrons-center justify-content-center w-100 bg-light">
        <div class="container d-flex flex-column py-4">
            <form action="/reports/attendance_list" method="GET" class="card p-3">
                @method('GET')
                <div class="d-flex column-gap-2">
                    <div class="w-25">
                        @php
                            $_college = request('college') ?? '';
                        @endphp
                        <label for="college" class="form-label">College</label>
                        <select name="college" id="college" class="form-control form-control-sm">
                            <option value="">--</option>
                            @foreach ($colleges as $college)
                                <option {{ $college['key']==$_college ? 'selected' : '' }} value="{{ $college['key'] }}">{{ $college['value'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-25">
                        @php
                            $_program = request('program') ?? '';
                        @endphp
                        <label for="program" class="form-label">Program</label>
                        <select name="program" id="program" class="form-control form-control-sm">
                            <option value="">--</option>
                            @foreach ($programs as $program)
                                <option {{ $program['key']==$_program ? 'selected' : '' }} value="{{ $program['key'] }}">{{ $program['value'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-50 d-flex column-gap-2 mb-2">
                        <div class="w-100">
                            @php
                                $years = [
                                    ['key' => '1st year', 'value' => 1],
                                    ['key' => '2nd year', 'value' => 2],
                                    ['key' => '3rd year', 'value' => 3],
                                    ['key' => '4th year', 'value' => 4],
                                    ['key' => '5th year', 'value' => 5],
                                    ['key' => '6th year', 'value' => 6],
                                    ['key' => '7th year', 'value' => 7],
                                    ['key' => '8th year', 'value' => 8],
                                    ['key' => '9th year', 'value' => 9],
                                    ['key' => '10th year', 'value' => 10],
                                ];

                                $_year = request('year') ?? '';
                            @endphp
                            <label for="year" class="form-label">Year</label>
                            <select class="form-control form-control-sm text-capitalize" name="year" id="year">
                                <option value="">--</option>
                                @foreach ($years as $year)
                                    <option {{ $year['value'] == $_year ? 'selected' : '' }} value="{{ $year['value'] }}">{{ $year['key'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-100">
                            @php
                                $roles = [
                                    'teacher',
                                    'student',
                                ];

                                $_role = request('role') ?? '';
                            @endphp
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control form-control-sm text-capitalize" name="role" id="role">
                                <option value="">--</option>
                                @foreach($roles as $role)
                                    <option {{ $role == $_role ? 'selected' : '' }} value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                        @php
                            $from = request('from') ?? '';
                            $to = request('to') ?? '';
                        @endphp
                        <div class="w-100">
                            <label for="from" class="form-label">From</label>
                            <input class="form-control form-control-sm" type="date" value="{{ $from }}" name="from" id="from">
                        </div>
                        <div class="w-100">
                            <label for="to" class="form-label">To</label>
                            <input class="form-control form-control-sm" type="date" value="{{ $to }}" name="to" id="to">
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-sm btn-primary">Apply Filter</button>
                    <a href="/{{ request()->path() }}" class="btn btn-sm btn-danger">Clear Filter</a>
                </div>
            </form>

            <h2 class="text-center my-3">List of Attendance</h2>
            <table class="table table-bordered">
                <thead>
                <tr>
                <th class="text-end">#</th>
                <th>Date</th>
                <th>Time</th>
                <th>Card No.</th>
                <th>Full Name</th>
                <th>Role</th>
                <th>College</th>
                <th>Program</th>
                <th>Year & Section</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($patrons as $patron)
                    @php
                        $date_object = new DateTime($patron->in);
                        $date = $date_object->format('Y-m-d');
                        $time = $date_object->format('h:i A');
                    @endphp
                    <tr>
                    <td class="text-end">{{ $loop->index + 1 }}.</td>
                    <td>{{ $date }}</td>
                    <td>{{ $time }}</td>
                    <td>{{ $patron->card_number }}</td>
                    <td class="text-capitalize">{{ $patron->name }}</td>
                    <td class="text-capitalize">{{ $patron->role }}</td>
                    <td>{{ $patron->college ?? '--' }}</td>
                    <td>{{ $patron->program ?? '--' }}</td>
                    <td>{{ $patron->year }} {{ $patron->section ?? '--' }}</td>
                    </tr>
                @empty
                    <tr>
                    <td colspan="9" class="text-center">
                        <h1 class="text-center text-muted">No data found.</h1>
                    </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
    <div class="d-flex justify-content-end pe-5 w-100 mb-3">
    <div class="d-flex justify-content-end pe-5 w-75 mb-3">
        <button id="printButton" class="btn btn-primary">Print</button>
    </div>

    <script>
        document.getElementById('printButton').addEventListener('click', () => {
            const params = new URLSearchParams({
                college: document.getElementById('college').value,
                program: document.getElementById('program').value,
                year: document.getElementById('year').value,
                role: document.getElementById('role').value,
                from: document.getElementById('from').value,
                to: document.getElementById('to').value
            });
            window.location.href = `/reports/attendance_list/print?_method=GET&${params.toString()}`;
        });
    </script>
    </div>
    <x-footer />
    <x-slot:script>
        <script>

        </script>
    </x-slot>
</x-layout>
