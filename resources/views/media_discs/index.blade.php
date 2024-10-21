@component('media_discs.layout', [
    'media_discs' => $media_discs,
])
    @slot('breadcrumb')
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 border py-2 px-3 bg-white rounded">
                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="/collections">Collections</a></li>
                <li class="breadcrumb-item active" aria-current="page">Media Discs</li>
            </ol>
        </nav>
    @endslot
    @slot('form')
        <style>
        #media_disc-cover-container {
          width: 235px;
          height: 350px;
          display: flex;
          justify-content: center;
          align-items: center;
          overflow: hidden;
          position: relative;
        }

        #media_disc-cover-container img {
          height: 100%;
          width: auto;
          object-fit: cover;
          position: absolute;
        }
        </style>
        <form action="/collections/media_discs" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <h4 class="text-body-secondary">Create new media disc</h4>
            <hr>
            <div class="d-flex column-gap-4">
                <div class="w-100">
                    <div class="d-flex column-gap-2">
                        <div class="mb-2 w-100">
                            <label for="accession_number" class="form-label">Accession No.</label>
                            <input type="text" class="form-control form-control-sm" placeholder="--" name="accession_number" id="accession_number" value="{{ old('accession_number') ?? '' }}">
                            @error('accession_number')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-2 w-100">
                            <label for="barcode_number" class="form-label">Barcode No.</label>
                            <input type="text" class="form-control form-control-sm" placeholder="--" name="barcode_number" id="barcode_number" value="{{ old('barcode_number') ?? '' }}">
                            @error('barcode_number')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex column-gap-2">
                        <div class="mb-2 w-100">
                            <label for="lcc_number" class="form-label">LCC No.</label>
                            <input type="text" class="form-control form-control-sm" placeholder="--" name="lcc_number" id="lcc_number" value="{{ old('lcc_number') ?? '' }}">
                            @error('lcc_number')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-2 w-100">
                            <label for="ddc_number" class="form-label">DDC No.</label>
                            <input type="text" class="form-control form-control-sm" placeholder="--" name="ddc_number" id="ddc_number" value="{{ old('ddc_number') ?? '' }}">
                            @error('ddc_number')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="ir_number" class="form-label">Institution Repository No.</label>
                        <input type="text" class="form-control form-control-sm" placeholder="--" name="ir_number" id="ir_number" value="{{ old('ir_number') ?? '' }}">
                        @error('ir_number')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control form-control-sm" placeholder="--" name="title" id="title" value="{{ old('title') ?? '' }}">
                        @error('title')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label for="author" class="form-label">Author(s)</label>
                        <input type="text" class="form-control form-control-sm" placeholder="--" name="author" id="author" value="{{ old('author') ?? '' }}">
                        @error('author')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label for="publisher" class="form-label">Publisher</label>
                        <input type="text" class="form-control form-control-sm" placeholder="--" name="publisher" id="publisher" value="{{ old('publisher') ?? '' }}">
                        @error('publisher')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex column-gap-2">
                        <div class="mb-2 w-100">
                            @php
                                $max_year = (int) date('Y');
                                $min_year = 1950;
                            @endphp
                            <label for="year_released" class="form-label">Year Released</label>
                            <select class="form-control form-control-sm" name="year_released" id="year_released">
                                <option value="">--</option>
                                @for($i=$max_year; $i>=$min_year; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            @error('year_released')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-2 w-100">
                            <label for="duration" class="form-label">Duration in minutes</label>
                            <input type="text" class="form-control form-control-sm" placeholder="--" name="duration" id="duration" value="{{ old('duration') ?? '' }}">
                            @error('duration')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="summary" class="form-label">Summary</label>
                        <textarea style="height: 172px;" class="form-control form-control-sm" placeholder="--" name="summary" id="summary" rows="4">{{ old('summary') ?? '' }}</textarea>
                        @error('summary')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="w-100 d-flex flex-column">
                    <div class="flex-grow-1 rounded d-flex align-items-center justify-content-center">
                        <div id="media_disc-cover-container" class="border text-center shadow">
                            <img id="media_disc-cover" class="h-100 d-block" src="{{ asset('images/book_cover_not_available.jpg') }}" alt="">
                        </div>
                        <input class="d-none" type="file" name="file" id="file">
                    </div>
                    <div class="mb-2">
                        <label for="tags" class="form-label">Tag(s)</label>
                        <input type="text" class="form-control form-control-sm" placeholder="--" name="tags" id="tags" value="{{ old('tags') ?? '' }}">
                        @error('tags')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex column-gap-2">
                        <div class="mb-2 w-100">
                            <label for="language" class="form-label">Language</label>
                            <select class="form-control form-control-sm text-capitalize" name="language" id="language">
                                <option value="">--</option>
                                @foreach($languages as $language)
                                    <option value="{{ $language }}">{{ $language }}</option>
                                @endforeach
                            </select>
                            @error('language')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-2 w-100">
                            <label for="genre" class="form-label">Genre</label>
                            <select class="form-control form-control-sm text-capitalize" name="genre" id="genre">
                                <option value="">--</option>
                                @foreach($genres as $genre)
                                    <option value="{{ $genre }}">{{ $genre }}</option>
                                @endforeach
                            </select>
                            @error('genre')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex column-gap-2">
                        <div class="mb-2 w-100">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-control form-control-sm text-uppercase" name="type" id="type">
                                <option value="">--</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-2 w-100">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control form-control-sm text-capitalize" name="status" id="status">
                                <option value="">--</option>
                                @foreach ($statuses as $status)
                                    <option {{ $status==old('status') ? 'selected' : '' }} value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex flex-row-reverse">
                <button type="submit" class="w-25 btn btn-primary px-3">Submit</button>
            </div>
        </form>
    @endslot
@endcomponent
