@use(App\Models\Program)
<x-layout>
    <x-slot:head>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.1.4/css/dataTables.bootstrap5.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/2.1.4/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/2.1.4/js/dataTables.bootstrap5.min.js"></script>
    </x-slot:head>
    <x-header />
    <main class="d-flex flex-column justify-content-center w-100 bg-body-secondary">
        <section class="container d-flex py-3 align-items-center">
            {{ $breadcrumb ?? '' }}
        </section>

        <div class="container d-flex flex-column pb-5 row-gap-4">
            <section class="w-100">
                <a href="/users/students#students-form" class="btn btn-outline-success">
                    New Student
                    <i class="bi bi-plus"></i>
                </a>
            </section>
            <section class="d-block">
                @if (session('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <section class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                            {{ session('message') }}
                        </section>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <table id="students-table" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Card No.</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Email Address</th>
                            <th>Library Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td>
                                    <form id="delete-student-{{ $student->id }}"
                                        action="/users/students/{{ $student->id }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">DELETE</button>
                                    </form>
                                    <a title="Edit" href="/users/students/{{ $student->id }}/edit#students-form" class="btn btn-light btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button title="Delete" onclick="deleteStudent({{ $student->id }});" class="btn btn-light btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                                <td>{{ $student->card_number }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->program }} {{ $student->year }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->library }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
            <section class="d-block">
                <div id="students-form" class="card p-3 w-full shadow">
                    <div class="card-body">
                        {{ $form ?? '' }}
                    </div>
                </div>
            </section>
        </div>
    </main>
    <x-footer />
    <x-slot:script>
        <script>
            const programs = @json(Program::all());

            new DataTable('#students-table');

            async function deleteStudent(id) {
                let result = await Swal.fire({
                    title: "Delete this student?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#0d6efd",
                    cancelButtonColor: "#bb2d3b",
                    confirmButtonText: "Continue"
                });

                if (result.isConfirmed) {
                    document.querySelector(`#delete-student-${id} button`).click();
                }
            }

            window.onload = function() {
                const $fileInput = document.getElementById('file');
                const $container = document.getElementById('profile-container');
                const $cover_img = document.getElementById('profile');
                const $program   = document.getElementById('program');
                const $college   = document.getElementById('college');

                $container.addEventListener('click', ()=> { file.click() });
                $fileInput.addEventListener('change', (e)=> {
                    let self = e.target;

                    if(self.files.length) {
                        let image = URL.createObjectURL(self.files[0]);

                        $cover_img.src = image;
                    } else {
                        $cover_img.src = '/images/profile.jpg';
                    }
                });

                $program.addEventListener('change', (e) => {
                    let program_code = e.target.value;
                    let program = programs.filter(p => p.code == program_code)[0];

                    $college.value = program.college;

                    console.log({program_code, program});
                });
            };

            async function addEncoding() {
                $submitProxy = document.querySelector('#submit_proxy');
                $submitProxy.innerHTML = 'Processing...';
                $submitProxy.setAttribute('disabled', true);

                let $form = document.querySelector("#patron-form");
                let formData = new FormData($form);
                let data = new FormData();

                data.set('card_number', formData.get('card_number'));
                data.set('file', formData.get('file'));

                if(!data.get('file').name) {
                    $submitProxy.innerHTML = 'Submit';
                    $submitProxy.removeAttribute('disabled');
                    document.querySelector('#submit').click();
                    return 0;
                }

                let response = await fetch('http://localhost:3000/add_encoding', {
                    method: 'POST',
                    body: data,
                });

                let _response = await response.json();

                if(_response.status == 'success') {
                    $submitProxy.innerHTML = 'Submit';
                    $submitProxy.removeAttribute('disabled');
                    document.querySelector('#submit').click();
                } else {
                    $submitProxy.innerHTML = 'Submit';
                    $submitProxy.removeAttribute('disabled');

                    Swal.fire({
                        title: _response.message,
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            }
        </script>
    </x-slot:script>
</x-layout>
