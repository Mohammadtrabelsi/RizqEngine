@if ($errors->any())
    @foreach($errors->all() as $error)
        <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-red-50 text-red-700 border-red-200 pr-12 fade show" role="alert">
            <div class="alert-body">
                <span>{{ $error }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    @endforeach
@endif
