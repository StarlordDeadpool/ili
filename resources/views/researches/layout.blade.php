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
                <a href="/collections/research#research-form" class="btn btn-outline-success">
                    New Research
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

                <table id="researches-table" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Genre</th>
                            <th>Title</th>
                            <th>Year</th>
                            <th>Author</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($researches as $research)
                            <tr>
                                <td style="min-width: 140px;">
                                    <form id="delete-research-{{ $research->id }}"
                                        action="/collections/research/{{ $research->id }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">DELETE</button>
                                    </form>
                                    <a title="Copy" href="/collections/research/{{ $research->id }}/copy#research-form" class="btn btn-light btn-sm">
                                        <i class="bi bi-copy"></i>
                                    </a>
                                    <a title="Edit" href="/collections/research/{{ $research->id }}/edit#research-form" class="btn btn-light btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button title="Delete" onclick="deleteBook({{ $research->id }});" class="btn btn-light btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                                <td class="text-capitalize">{{ $research->genre }}</td>
                                <td>{{ $research->title }}</td>
                                <td class="text-end">{{ $research->publication_year }}</td>
                                <td>{{ $research->author }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
            <section class="d-block">
                <div id="research-form" class="card p-3 w-full shadow">
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
            new DataTable('#researches-table');

            async function deleteBook(id) {
                let result = await Swal.fire({
                    title: "Delete this research?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#0d6efd",
                    cancelButtonColor: "#bb2d3b",
                    confirmButtonText: "Continue"
                });

                if (result.isConfirmed) {
                    document.querySelector(`#delete-research-${id} button`).click();
                }
            }

            (function() {
                const fileInput = document.getElementById('file');
                const container = document.getElementById('research-cover-container');
                const cover_img = document.getElementById('research-cover');

                container.addEventListener('click', ()=> { file.click() });
                fileInput.addEventListener('change', (e)=> {
                    let self = e.target;

                    if(self.files.length) {
                        let image = URL.createObjectURL(self.files[0]);

                        cover_img.src = image;
                    } else {
                        cover_img.src = '/images/cover_not_available.jpg';
                    }
                });
            })();
        </script>
    </x-slot:script>
</x-layout>
