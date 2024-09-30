@if ($errors->any())
    <div class="alert alert-danger">
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
@endif

@if (Session::has('success'))
    <div class="alert alert-success" id="flash-message-success">
        <div>
            <p>{{ Session::get('success') }}</p>
        </div>
    </div>
@endif

@if (Session::has('error'))
    <div class="alert alert-danger" id="flash-message-error">
        <div>
            <p>{{ Session::get('error') }}</p>
        </div>
    </div>
@endif

<script>
    window.onload = function() {
        // Check if the success flash message exists
        const flashMessageSuccess = document.getElementById('flash-message-success');
        if (flashMessageSuccess) {
            // Set a timeout to remove the flash message after 5 seconds
            setTimeout(() => {
                flashMessageSuccess.style.display = 'none'; // Hide the success flash message
            }, 5000); // 5000 milliseconds = 5 seconds
        }

        // Check if the error flash message exists
        const flashMessageError = document.getElementById('flash-message-error');
        if (flashMessageError) {
            // Set a timeout to remove the flash message after 5 seconds
            setTimeout(() => {
                flashMessageError.style.display = 'none'; // Hide the error flash message
            }, 5000); // 5000 milliseconds = 5 seconds
        }
    }
</script>
