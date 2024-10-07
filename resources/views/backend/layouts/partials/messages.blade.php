@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

@if (Session::has('success'))
    <div class="alert alert-success" id="flash-message-success">
        <p>{{ Session::get('success') }}</p>
    </div>
@endif

@if (Session::has('error'))
    <div class="alert alert-danger" id="flash-message-error">
        <p>{{ Session::get('error') }}</p>
    </div>
@endif

<script>
    window.onload = function() {
        // Hide the flash message after 5 seconds
        setTimeout(() => {
            const flashMessageSuccess = document.getElementById('flash-message-success');
            const flashMessageError = document.getElementById('flash-message-error');

            if (flashMessageSuccess) {
                flashMessageSuccess.style.display = 'none';
            }
            if (flashMessageError) {
                flashMessageError.style.display = 'none';
            }
        }, 5000);
    }
</script>
