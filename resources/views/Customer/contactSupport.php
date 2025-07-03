


<div class="container mx-auto mt-8 max-w-2xl">
    <h2 class="text-2xl font-bold mb-4">📩 Contact Support</h2>

    
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            
        </div>
    

    <form method="POST" action="{{ route('customer.sendSupport') }}">
        <div class="mb-4">
            <label class="block font-medium mb-1">Your Message</label>
            <textarea name="message" rows="5" class="w-full p-2 border rounded" required></textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Send Message
        </button>
    </form>
</div>
