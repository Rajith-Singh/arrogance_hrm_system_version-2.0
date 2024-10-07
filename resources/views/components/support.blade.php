<div class="container">

    <h1 class="mt-8 text-2xl font-medium text-gray-900">
        Help & Support
    </h1>

    <form action="{{ route('support.send') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if(session('msg'))
            <div class="alert alert-success">{{session('msg')}} </div>
        @endif

        <div class="form-group">
            <label for="department" class="text-dark">Select Department</label>
            <select name="department" id="department" class="form-control" required>
                <option value="HR">HR</option>
                <option value="IT Admin">IT Admin</option>
                <option value="Management">Management</option>
            </select>
        </div>

        <div class="form-group">
            <label for="message" class="text-dark">Message</label>
            <textarea name="message" id="message" rows="5" class="form-control" placeholder="Type your message..." required></textarea>
        </div>

        <div class="form-group">
            <label for="attachment" class="text-dark">Attachment (optional)</label>
            <input type="file" name="attachment" class="form-control-file" id="attachment">
        </div>

        <button type="submit" class="btn btn-warning btn-block">Send Support Request</button>
        <br> <br>
    </form>
</div>