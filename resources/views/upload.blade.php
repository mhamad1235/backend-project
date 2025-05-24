<!DOCTYPE html>
<html>
<head>
    <title>Upload to S3</title>
</head>
<body>
    <h1>Upload File to S3</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
        <p><a href="{{ session('url') }}" target="_blank">View File</a></p>
    @endif

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('upload.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
