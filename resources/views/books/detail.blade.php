<x-layout>
    <x-slot:head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </x-slot:head>
    <x-header />
    <main class="d-flex flex-column align-items-center justify-content-center w-100 bg-light">
        <div class="container py-2 d-flex">

            <style>
                .multiline-ellipsis {
                    display: -webkit-box;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    -webkit-line-clamp: 2;
                    line-height: 1.5em;
                    max-height: 3em;
                }
            </style>
            <div class="w-100 ps-4">
                <section class="d-flex w-100 py-4">
                    <div class="w-50">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-arrow-left"></i>
                            Back
                        </a>
                    </div>
                    <div class="w-50 text-end">
                        <a href="/collections/books/{{ $book->id }}/copy#books-form" class="btn btn-outline-secondary btn-sm">
                            Duplicate
                            <i class="bi bi-copy"></i>
                        </a>
                        <a href="/collections/books/{{ $book->id }}/edit#books-form" class="btn btn-outline-primary btn-sm">
                            Edit
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-outline-danger btn-sm">
                            Delete
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </section>

                <section class="d-flex w-100">
                    <div class="px-4">
                        <section style="height: 200px;" class="card p-1 mt-2">
                            @php $book_cover = ($book->cover_image) ? "/storage/images/books/$book->cover_image" : '/images/book_cover_not_available.jpg'; @endphp
                            <img class="h-100 d-block" src="{{ asset($book_cover) }}" alt="">
                        </section>
                    </div>
                    <div class="w-100 px-1">
                        <section class="d-flex">
                            <div class="w-100">
                                <h2>{{ $book->title }}</h2>
                            </div>
                        </section>
                        <hr style="margin-top: 0; margin-bottom: 12px;">
                        <p style="margin: 0;">
                            <b>ISBN:</b> {{ $book->isbn }} <br>
                            <b>Author(s):</b> {{ $book->author }} <br>
                            <b>Published in:</b> {{ $book->publisher }} ({{ $book->publication_year }}) <br>
                            <b>Abstract/Summary:</b>
                        </p>
                        <p class="multiline-ellipsis" style="text-align: justify; margin: 0; text-indent: 50px;">
                            <i>{{ $book->summary }}</i></p>
                        <hr>
                        @if ($book->tags)
                            <p>
                                @php
                                    $tags = explode(',', $book->tags) ?? [];
                                @endphp
                                @foreach ($tags as $tag)
                                    <a class="badge text-bg-secondary">
                                        {{ $tag }}
                                    </a>
                                @endforeach
                            </p>
                        @endif
                    </div>
                </section>

                <br>
                <ul class="nav nav-tabs" style="font-size: 17px;" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active bg-transparent" id="copies-tab" data-bs-toggle="tab" data-bs-target="#copies"
                            type="button" role="tab" aria-controls="copies" aria-selected="true">
                            <i class="bi bi-basket me-1"></i>
                            Get
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link bg-transparent" id="description-tab" data-bs-toggle="tab" data-bs-target="#description"
                            type="button" role="tab" aria-controls="description" aria-selected="false">
                            <i class="bi bi-list me-1"></i>
                            Description
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link bg-transparent" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages"
                            type="button" role="tab" aria-controls="messages"
                            aria-selected="false">
                            <i class="bi bi-boxes me-1"></i>
                            Related Entities
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link bg-transparent" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings"
                            type="button" role="tab" aria-controls="settings"
                            aria-selected="false">
                            <i class="bi bi-file-earmark me-1"></i>
                            Files
                        </button>
                    </li>
                </ul>
                <div class="tab-content p-3">
                    <div class="tab-pane active" id="copies" role="tabpanel" tabindex="0">
                        @foreach($libraries as $library)
                            <div class="accordion mb-2" id="libray-{{ $library->code }}">
                                <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" aria-expanded="false" data-bs-target="#libray-{{ $library->code }}-content"  aria-controls="libray-{{ $library->code }}-content">
                                        <div class="w-50">[{{ $library->code ?? '--' }}] - {{ $library->name ?? '--' }}</div>
                                        <div class="w-50">Items: {{ count($library->books) }}</div>
                                    </button>
                                </h2>
                                <div id="libray-{{ $library->code }}-content" class="accordion-collapse collapse" data-bs-parent="#libray-{{ $library->code }}">
                                    <div class="accordion-body">
                                        @forelse($library->books as $copy)
                                            <div class="d-flex mb-1 border m-3 p-3">
                                                <table class="w-100">
                                                    <tbody>
                                                        <tr>
                                                            <th class="text-nowrap px-2">Library code: </th>
                                                            <td class="text-capitalize">{{ $copy->library ?? '--'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-nowrap px-2">Barcode: </th>
                                                            <td>{{ $copy->barcode_number ?? '--'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-nowrap px-2">LCC / Call number: </th>
                                                            <td>{{ $copy->lcc_number ?? '--'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-nowrap px-2">DDC number: </th>
                                                            <td>{{ $copy->ddc_number ?? '--'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-nowrap px-2">Price: </th>
                                                            <td class="text-capitalize">{{ $copy->price ?? '--'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-nowrap px-2">Status: </th>
                                                            <td class="text-capitalize">{{ $copy->status ?? '--'}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="text-end" style="min-width: 200px;">
                                                    <button onclick="requestItem({{ $copy->barcode_number ?? 'null' }})" title="Request item" class="btn btn-outline-success">
                                                        <i class="bi bi-basket"></i>
                                                    </button>
                                                    @if(in_array(Auth::user()->role, ['admin','librarian','clerk','staff']))
                                                        <button title="Delete item" class="btn btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <h5 class="text-center my-3 text-secondary">No items found.</h5>
                                        @endforelse
                                    </div>
                                </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="tab-pane" id="description" role="tabpanel" tabindex="0">
                        <table>
                            <tbody class="align-top">
                                <tr>
                                    <th class="text-nowrap px-2">No. of copies</th>
                                    <td class="text px-2">{{ $book->copies ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap px-2">Title</th>
                                    <td class="text px-2">{{ $book->title }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap px-2">ISBN</th>
                                    <td class="text px-2">{{ $book->isbn }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap px-2">Author(s)</th>
                                    <td class="text px-2">{{ $book->author ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap px-2">Publisher</th>
                                    <td class="text px-2">{{ $book->publisher ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap px-2">Year Published</th>
                                    <td class="text px-2">{{ $book->publication_year ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap px-2">Genre</th>
                                    <td class="text-capitalize px-2">{{ $book->genre }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap px-2">Abstract / Summary</th>
                                    <td class="px-2" style="text-align: justify;">
                                        <div id="summary" class="multiline-ellipsis">
                                            {{ $book->summary ?? '--' }}
                                        </div>
                                        <a href="javascript:showSummary()" id="show-summary">See more.</a>
                                        <a href="javascript:hideSummary()" style="display: none;" id="hide-summary">See less.</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap px-2">No. of pages</th>
                                    <td class="text px-2">{{ $book->number_of_pages ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap px-2">Price</th>
                                    <td class="text px-2">{{ $book->price ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap px-2">Format</th>
                                    <td class="text-capitalize px-2">{{ $book->format }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap px-2">Language</th>
                                    <td class="text-capitalize px-2">{{ $book->language ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap px-2">Tag(s)</th>
                                    <td class="px-2">{{ $book->tags }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="related_entities" role="tabpanel" aria-labelledby="related_entities-tab" tabindex="0">

                    </div>
                    <div class="tab-pane" id="files" role="tabpanel" aria-labelledby="files-tab" tabindex="0">

                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-footer />
    <x-slot:script>
        <script>
        function requestItem(barcode) {
            if(barcode==null) {
                Swal.fire({
                    title: "No Barcode",
                    text: "Item is not yet available for loan ",
                    icon: "error"
                });
            } else {
                Swal.fire({
                    title: "Request this item for loan?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Confirm"
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Item requested successfully",
                            icon: "success",
                            showConfirmButton: false,
                            timer: 2000,
                        });
                    }
                });
            }
        }

        function showSummary() {
            let $showSummary = document.getElementById('show-summary');
            let $hideSummary = document.getElementById('hide-summary');
            let $summary     = document.getElementById('summary');

            $summary.classList.remove('multiline-ellipsis');
            $showSummary.style.display = 'none';
            $hideSummary.style.display = 'inline';
        }

        function hideSummary() {
            let $showSummary = document.getElementById('show-summary');
            let $hideSummary = document.getElementById('hide-summary');
            let $summary     = document.getElementById('summary');

            $summary.classList.add('multiline-ellipsis');
            $showSummary.style.display = 'inline';
            $hideSummary.style.display = 'none';
        }
        </script>
    </x-slot:script>
</x-layout>
